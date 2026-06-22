<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import type { Recipe, RecipeForm } from "@/types";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import RecipeFormFields from "@/Components/RecipeFormFields.vue";

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
        <h1 class="text-2xl font-bold mb-4">Nueva receta</h1>
        <RecipeFormFields :form="form" />
        <PrimaryButton> Crear </PrimaryButton>
    </form>
</template>
