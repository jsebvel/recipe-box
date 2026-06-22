<script setup lang="ts">
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps<{
    title: string;
    prepMinutes: number;
    recipeId: number;
    body: string;
}>();

const formattedTime = computed(() => {
    const hours = Math.floor(props.prepMinutes / 60);
    const mins = props.prepMinutes % 60;
    return hours > 0 ? `${hours}h ${mins}m` : `${mins}m`;
});

const excerpt = computed(() =>
    props.body.length > 10 ? `${props.body.slice(0, 10)}...` : props.body,
);
</script>

<template>
    <div class="flex flex-col border border-gray-300 rounded-xl p-4 shadow-xl">
        <div class="flex items-start justify-between gap-2">
            <div>
                <h2 class="text-gray-800 font-bold">{{ title }}</h2>
                <p class="text-gray-600">Tiempo: {{ formattedTime }}</p>
            </div>
            <Link
                :href="route('recipes.show', recipeId)"
                class="whitespace-nowrap text-sm font-medium text-blue-600 hover:underline"
            >
                Detalles →
            </Link>
        </div>
        <p class="mt-2 text-gray-600">{{ excerpt }}</p>
    </div>
</template>
