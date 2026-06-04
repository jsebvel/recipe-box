<script setup lang="ts">
import RecipeCard from '@/Components/RecipeCard.vue'
import type { Recipe } from '@/types'

defineProps<{
  recipes: Recipe[]
}>()
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Mis Recetas</h1>
    <div class="grid gap-4">
      <RecipeCard
        v-for="recipe in recipes"
        :key="recipe.id"
        :title="recipe.title"
        :prep-minutes="recipe.prep_minutes"
      >
        {{ recipe.body }}
      </RecipeCard>
    </div>
  </div>
</template>
