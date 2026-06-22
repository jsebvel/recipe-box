<script setup lang="ts">
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { useExtensionPoint } from "@/composables/useExtensionPoint";
import type { Recipe } from "@/types";

const props = defineProps<{
    recipe: Recipe;
    searchTerm: string;
}>();

const sideBarExtensions = useExtensionPoint("recipe.show.sidebar.after");

const canEdit = computed(() => {
    const user = usePage().props.auth?.user;
    return user?.id === props.recipe.author?.id;
});
</script>

<template>
    <div class="mx-auto max-w-3xl px-4 py-8">
        <Link
            :href="route('recipes.index')"
            class="mb-4 inline-flex items-center gap-1 text-sm text-gray-500 transition hover:text-blue-600"
        >
            ← Volver a recetas
        </Link>

        <article class="rounded-2xl bg-white p-8 shadow-lg">
            <header class="mb-6 flex items-start justify-between gap-4">
                <h1 class="text-3xl font-bold text-gray-800">
                    {{ recipe.title }}
                </h1>
                <Link
                    v-if="canEdit"
                    :href="route('recipes.edit', recipe.id)"
                    class="whitespace-nowrap rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-white hover:border border-blue-600 hover:text-blue-600"
                >
                    Editar
                </Link>
            </header>

            <div
                class="mb-6 flex flex-wrap items-center gap-4 text-sm text-gray-600"
            >
                <span class="inline-flex items-center gap-1">
                    👨‍🍳 {{ recipe.author?.name ?? "Desconocido" }}
                </span>
                <span class="inline-flex items-center gap-1">
                    ⏱ {{ recipe.prep_minutes }} min
                </span>
            </div>

            <div v-if="recipe.tags?.length" class="mb-6 flex flex-wrap gap-2">
                <span
                    v-for="tag in recipe.tags"
                    :key="tag.id"
                    class="rounded-full bg-blue-600 px-3 py-1 text-xs font-medium text-white"
                >
                    {{ tag.name }}
                </span>
            </div>

            <hr class="mb-6 border-gray-100" />

            <div class="whitespace-pre-wrap leading-relaxed text-gray-700">
                {{ recipe.body }}
            </div>
        </article>

        <aside class="mt-8 space-y-4">
            <component
                v-for="(ext, i) in sideBarExtensions"
                :is="ext"
                :key="i"
                :recipe="recipe"
            />
        </aside>
    </div>
</template>
