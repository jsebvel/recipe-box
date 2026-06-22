<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import RecipeCard from '@/Components/RecipeCard.vue'
import type { Recipe } from '@/types'

defineProps<{
  recipes: Recipe[]
}>()
</script>

<template>
  <div class="p-6">
    <div class="mb-8 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-800">Mis Recetas</h1>
      <Link :href="route('recipes.create')"
        class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-white hover:border border-blue-600 hover:text-blue-600">
        Crear receta
      </Link>
    </div>
    <div class="grid grid-cols-3 gap-4">
      <RecipeCard
        v-for="recipe in recipes"
        :key="recipe.id"
        :title="recipe.title"
        :prep-minutes="recipe.prep_minutes"
        :recipe-id="recipe.id"
        :body="recipe.body"
      />
    </div>
  </div>
</template>
