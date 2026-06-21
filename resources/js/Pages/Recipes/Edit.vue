<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import type { Recipe, RecipeForm } from "@/types";

const props = defineProps<{ recipe: Recipe }>();

const form = useForm<RecipeForm>({
    title: props.recipe.title,
    body: props.recipe.body,
    prep_minutes: props.recipe.prep_minutes,
});

const submit = () => {
    form.put(`/recipes/${props.recipe.id}`, {
        onSuccess: () => form.reset("title", "body", "prep_minutes"),
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="p-6 max-w-lg mx-auto">
        <div class="mb-4">
            <label class="block font-bold mb-1">Título</label>
            <input v-model="form.title" class="boder rounded w-full p-2" />
            <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">
                {{ form.errors.title }}
            </div>
        </div>

        <div class="mb-4">
            <label class="block font-bold mb-1">Cuerpo</label>
            <textarea
                v-model="form.body"
                class="border rounded w-full p-2"
                rows="5"
            />
            <div v-if="form.errors.body" class="text-red-500 text-sm mt-1">
                {{ form.errors.body }}
            </div>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-bold">Minutos de preparación</label>
            <input
                v-model="form.prep_minutes"
                type="number"
                class="border rounded w-full p-2"
            />
            <div
                v-if="form.errors.prep_minutes"
                class="text-red-500 text-sm mt-1"
            >
                {{ form.errors.prep_minutes }}
            </div>
        </div>
        <button
            type="submit"
            :disabled="form.processing"
            class="bg-blue-500 text-white px-4 py-2 rounded"
        >
            Guardar
        </button>
    </form>
</template>
