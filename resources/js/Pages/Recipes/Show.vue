<script setup lang="ts">
import { useExtensionPoint } from "@/composables/useExtensionPoint";
import type { Recipe } from "@/types";

defineProps<{
    recipe: Recipe;
}>();

const sideBarExtensions = useExtensionPoint("recipe.show.sidebar.after");
</script>

<template>
    <div class="p-6 max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-2">{{ recipe.title }}</h1>
        <p class="text-gray-500 mb-4">Tiempo: {{ recipe.prep_minutes }} min</p>
        <div class="prose">{{ recipe.body }}</div>
        <div class="mt-4 gap-2">
            <span
                v-for="tag in recipe.tags"
                :key="tag.id"
                class="bg-gray-200 px-2 py-1 rounded text-sm"
            >
                {{ tag.name }}
            </span>
        </div>
        <aside>
            <component
                v-for="(ext, i) in sideBarExtensions"
                :is="ext"
                :key="i"
                :recipe="recipe"
            />
        </aside>
    </div>
</template>
