<script setup lang="ts">
import { useForm, Link } from "@inertiajs/vue3";
import type { RecipeForm } from "@/types";
import RecipeFormFields from "@/Components/RecipeFormFields.vue";

const form = useForm<RecipeForm>({
    title: "",
    body: "",
    prep_minutes: 15,
});

const submit = () => {
    form.post(route("recipes.store"), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <div class="mx-auto max-w-3xl px-4 py-8">
        <Link
            :href="route('recipes.index')"
            class="mb-4 inline-flex items-center gap-1 text-sm text-gray-500 transition hover:text-amber-600"
        >
            ← Volver a recetas
        </Link>

        <form
            @submit.prevent="submit"
            class="rounded-2xl bg-white p-8 shadow-lg"
        >
            <h1 class="mb-6 text-2xl font-bold text-gray-800">Nueva receta</h1>

            <RecipeFormFields :form="form" />

            <div class="mt-6 flex items-center justify-end gap-3">
                <Link
                    :href="route('recipes.index')"
                    class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 transition hover:text-gray-800"
                >
                    Cancelar
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white transition hover:bg-white hover:border border-blue-600 hover:text-blue-600 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{ form.processing ? "Creando..." : "Crear receta" }}
                </button>
            </div>
        </form>
    </div>
</template>
