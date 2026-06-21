<script setup lang="ts">
import type { Recipe } from "@/types";

defineProps<{
    recipe: Recipe;
}>();
</script>

<template>
    <div class="border rounded p-3 mt-4">
        <h3 class="font-bold text-sm mb-2">Recetas relacionadas</h3>
        <p class="text-gray-500 text-sm">Panel inyectado sin modificar el Show.vue</p>
    </div>
</template>
