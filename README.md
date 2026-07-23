# 🍳 Recipe Box

Mini-aplicación de recetas construida como ejercicio técnico. Permite crear,
listar, ver, editar y buscar recetas, con autenticación, borrador persistente,
contador de vistas y un sistema de búsqueda "instrumentada" mediante el patrón
decorador.

## Stack

- **Backend:** Laravel 11, PHP 8.2+, Eloquent ORM
- **Frontend:** Vue 3 (Composition API, `<script setup>`), Inertia.js, Pinia, Tailwind CSS
- **Base de datos:** SQLite
- **Auth:** Laravel Breeze
- **Cola:** driver `sync`
- **Tipado:** TypeScript estricto

## Requisitos implementados

Los 6 requisitos del enunciado, uno por fase:

| # | Requisito | Fase |
|---|---|---|
| 1 | Props globales vía `HandleInertiaRequests` (eager + lazy + `activeDraft` con `RecipeResource`) | 4 |
| 2 | Store Pinia `useDraftStore` hidratado desde props de Inertia | 5 |
| 3 | Decorador `InstrumentedRecipeSearch` + binding en Service Container | 6 |
| 4 | Hook de extensión `recipe.show.sidebar.after` | 7 |
| 5 | Middleware `EnsureRecipeOwner` + `useForm` + validación server-side | 8 |
| 6 | Job `SendNewRecipeDigest` + evento `RecipeViewed` con listener dedicado | 9 |

## Setup

```bash
composer install
npm install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
```

Levantar en dos terminales:

```bash
php artisan serve        # http://localhost:8000
npm run dev              # HMR Vite
```

## Decisiones de diseño

### 1. Decorador con interfaz propia (Requisito 3)

El paquete vendor `Acme\RecipeSearch\RecipeSearch` es una **clase concreta sin
interfaz**. Para aplicar el patrón decorador respetando el sistema de tipos de
PHP, se introdujo `App\Contracts\RecipeSearcher` como abstracción compartida.

**Por qué una interfaz propia:** un binding directo `RecipeSearch::class → InstrumentedRecipeSearch`
falla en runtime porque el decorador **no extiende** la clase vendor — PHP
rechaza la asignación (`TypeError`). La interfaz permite que ambos tipos sean
intercambiables sin tocar el código vendor.

**Binding con closure** (evita recursión infinita):
```php
$this->app->bind(RecipeSearcher::class, fn ($app) =>
    new InstrumentedRecipeSearch(new RecipeSearch())
);
```
Un `bind(RecipeSearcher::class, InstrumentedRecipeSearch::class)` pelado causaría
recursión: el container, al construir el decorador, resolvería su dependencia
`RecipeSearcher` → otro decorador → bucle. La closure construye el núcleo
**fuera del container**, rompiendo el ciclo.

### 2. Store Pinia hidratado desde props (Requisito 2)

`useDraftStore` se inicializa **una sola vez** en `app.ts` leyendo
`props.initialPage.props.activeDraft`. No hay `fetch` en `onMounted`: el servidor
ya entregó el borrador vía shared props de Inertia, que es la fuente de verdad.

`persist()` hace POST a `/recipes/draft` (enviando solo `title`, `body`,
`prep_minutes` — no todo el `RecipeResource`) y, en `onSuccess`, dispara
`router.reload({ only: ['activeDraft'] })` para que el servidor rehidrate sin
navegación completa.

**Rehidratación tras persistir** (`Draft.vue`): como `initFromInertia` solo
corre una vez en el arranque de la app, hace falta un `watch` para que, cuando
el server actualice `page.props.activeDraft`, el store se resincronice:

```ts
watch(
    () => page.props.activeDraft,
    (d) => draftStore.initFromInertia(d ?? null),
);
```

Sin este `watch`, el store (con `id: 0` tras un `ensureDraft`) y la prop del
server (con el draft ya guardado, `id` real) quedaban desincronizados. El ciclo
completo queda: servidor → store (1 vez) → UI edita → `persist()` → server
→ `reload` → `watch` → store actualizado.

**Wrap `data` desactivado** (`AppServiceProvider::boot()`):
`JsonResource::withoutWrapping()` para que `activeDraft` llegue como el objeto
`Recipe` directo y no envuelto en `{data: ...}` (comportamiento por defecto de
los `JsonResource` de Laravel, que rompía la hidratación del store).

### 3. Hook de extensión desacoplado del layout (Requisito 4)

El punto de extensión `recipe.show.sidebar.after` se registra en `app.ts` (nivel
raíz) vía `app.provide(EXTENSION_POINT_KEY, slots)` + `createExtensionRegistry()`,
**no** en `AuthenticatedLayout`. Así:

- `Show.vue` consume el hook con `useExtensionPoint(...)` + `v-for` de componentes
  dinámicos.
- Un Service Provider puede registrar un panel "Recetas relacionadas" **sin tocar
  la página**.

### 4. Middleware como alias (Requisito 5)

`EnsureRecipeOwner` se registra como **alias** (`'recipe.owner'`) en
`bootstrap/app.php`, no como middleware global. Esto permite aplicarlo solo a
`recipes.edit`/`recipes.update` con `->middleware('recipe.owner')` y devuelve 403
si el usuario no es el autor.

### 5. Eventos: auto-discovery de Laravel 11 (Requisito 6)

En Laravel 11, el **auto-discovery** escanea `app/Listeners/` y registra cada
listener automáticamente por el type-hint de su método `handle(...)`. Por eso
`IncrementRecipeViews::handle(RecipeViewed $event)` queda conectado **sin
declarar nada** en el `EventServiceProvider`:

```php
// app/Providers/EventServiceProvider.php
class EventServiceProvider extends ServiceProvider
{
    // vacío a propósito: el auto-discovery de Laravel 11 hace el wiring
}
```

**Por qué no `$listen` explícito:** la primera versión usaba el `$listen`
heredado de Laravel <11. Como el provider extiende el
`EventServiceProvider` de Foundation, el mapa **sí** se procesaba, pero
**además** seguía corriendo el auto-discovery del framework → el listener
quedaba registrado **dos veces** (`php artisan event:list` lo mostraba).
Resultado: `view_count` subía de 2 en 2 por visita.

La forma idiomática en L11 es **dejar solo el auto-discovery** (clase vacía).
Si se quisiera wiring explícito, habría que desactivar el discovery a nivel app
en `bootstrap/app.php` (`->withEvents(discover: false)`), no desde el provider.

El `EventServiceProvider` se mantiene como **provider dedicado** (registrado en
`bootstrap/providers.php`), cumpliendo el espíritu del enunciado de no dejar el
wiring "suelto" en `AppServiceProvider`.

### 6. Formulario DRY (`RecipeFormFields`)

Create y Edit comparten el componente presentacional `RecipeFormFields.vue`
(campos + errores inline). Cada página conserva su propio `useForm` y lógica de
submit, pero la UI de los 3 campos vive en un solo sitio.

### 7. Route ordering (gotcha resuelto)

Laravel **sobrescribe** duplicados de mismo `método + URI` (gana el último
registrado, no el primero como se cree comúnmente). Se eliminó un closure
`GET /recipes` duplicado que, aunque ya era dead code, daba mala impresión al
leer `routes/web.php`.

### 8. `activeDraft` es eager, no lazy (Requisito 1)

En `HandleInertiaRequests`, `activeDraft` se sirve como prop **eager** (valor
directo), mientras que `auth.recentRecipes`, `flash.success` y `flash.error` sí
van como `Inertia::lazy(...)`. La razón: las lazy props implementan
`IgnoreFirstLoad`, así que **no viajan en el primer render** — solo se evalúan
en partial reloads que las piden.

Como el store `useDraftStore` se hidrata **una sola vez** en `app.ts` leyendo
`props.initialPage.props.activeDraft`, si esa prop fuera lazy llegaría
`undefined` al arranque y el store se inicializaría vacío. Por eso va fuera del
grupo lazy.

> Nota Inertia v2: `lazy()` está deprecado a favor de `optional()` (mismo
> comportamiento, renombrado).

## Estructura

```
app/
├── Contracts/RecipeSearcher.php          # interfaz para el decorador
├── Events/RecipeViewed.php
├── Jobs/SendNewRecipeDigest.php
├── Http/
│   ├── Controllers/RecipeController.php
│   ├── Middleware/EnsureRecipeOwner.php
│   └── Resources/RecipeResource.php
├── Listeners/IncrementRecipeViews.php
├── Models/{Recipe,Tag,User}.php
├── Providers/
│   ├── AppServiceProvider.php
│   ├── EventServiceProvider.php          # Fase 9
│   └── RecipeSearchServiceProvider.php   # Fase 6 (binding closure)
└── Support/Search/InstrumentedRecipeSearch.php

packages/Acme/RecipeSearch/               # vendor simulado (no editar)
resources/js/
├── Components/
│   ├── RecipeCard.vue
│   ├── RecipeFormFields.vue              # DRY entre Create/Edit
│   └── RelatedRecipesPanel.vue           # registro del hook Fase 7
├── Layouts/
│   ├── AuthenticatedLayout.vue           # Breeze (Dashboard, Profile, Recipes)
│   └── GuestLayout.vue
├── Pages/Recipes/{Index,Show,Create,Edit}.vue
├── composables/useExtensionPoint.ts      # hook Fase 7
├── store/draftStore.ts                   # Pinia Fase 5
└── app.ts                                # provide del hook + hidratación draft
```

## Testing

```bash
php artisan test                    # 27 tests, 64 assertions
npm run typecheck                   # vue-tsc --noEmit
vendor/bin/pint --test              # formato PHP (solo revisión)
```

Feature tests en `tests/Feature/RecipeValidationTest.php` cubren la validación
de `prep_minutes` (rango 1–600). Resto de tests son los que trae Breeze por
defecto (auth, profile).

## Comandos útiles

```bash
php artisan migrate:fresh --seed    # reset completo + 20 recetas + 5 tags
php artisan route:list              # verificar rutas registradas
php artisan serve                   # backend
npm run dev                         # frontend (HMR)
```

## Licencia

MIT.
