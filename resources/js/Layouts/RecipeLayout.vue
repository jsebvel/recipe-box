<script setup lang="ts">
import { computed, ref } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";

const user = computed(() => usePage().props.auth?.user ?? null);

const searchTerm = ref((usePage().props.searchTerm as string) ?? "");

const search = () => {
    if (!searchTerm.value.trim()) return;
    router.get(route("recipes.index"), { q: searchTerm.value }, { preserveState: true });
};
</script>
<template>
    <div class="min-h-screen bg-amber-50">
        <nav class="bg-white border-b border-amber-200">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">
            <!-- Logo -->
            <Link
                :href="route('recipes.index')"
                class="flex items-center gap-2 font-bold text-lg text-amber-700"
            >
                <span class="text-2xl">🍳</span>
                Recipe Box
            </Link>

            <!-- Buscador (solo el form, sin el usuario dentro) -->
            <form @submit.prevent="search" class="hidden flex-1 max-w-md sm:block">
                <input v-model="searchTerm" type="text"
                    placeholder="Buscar recetas por título"
                    class="w-full rounded-full border border-amber-300 bg-amber-50 px-4 py-2 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500"
                />
            </form>

            <!-- Usuario + logout (FUERA del form) -->
            <div class="flex items-center gap-3">
                <span class="hidden text-sm text-gray-700 sm:inline">{{ user?.name }}</span>
                <Link :href="route('logout')" method="post" as="button"
                    class="text-sm text-amber-700 hover:underline">
                    Salir
                </Link>
            </div>
        </div>
    </div>
</nav>
         <header v-if="$slots.header" class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <slot />
        </main>
    </div>
</template>
