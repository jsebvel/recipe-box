<script setup lang="ts">
import type { RecipeForm } from "@/types";

defineProps<{
    form: {
        title: string;
        body: string;
        prep_minutes: number;
        errors: Partial<Record<keyof RecipeForm, string>>;
    };
}>();
</script>

<template>
    <div class="max-w-2xl flex flex-col mx-auto">
        <div class="mb-4">
            <label class="block font-bold mb-1 text-gray-600">Título</label>
            <input
                v-model="form.title"
                class="border border-gray-400 rounded-xl w-full p-2"
            />
            <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">
                {{ form.errors.title }}
            </div>
        </div>

        <div class="mb-4">
            <label class="block font-bold mb-1 text-gray-600">Cuerpo</label>
            <textarea
                v-model="form.body"
                class="border border-gray-400 rounded-xl w-full p-2"
                rows="5"
            />
            <div v-if="form.errors.body" class="text-red-500 text-sm mt-1">
                {{ form.errors.body }}
            </div>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-bold text-gray-700"
                >Minutos de preparación</label
            >
            <input
                v-model="form.prep_minutes"
                type="number"
                class="border border-gray-400 rounded-xl w-full p-2"
            />
            <div
                v-if="form.errors.prep_minutes"
                class="text-red-500 text-sm mt-1"
            >
                {{ form.errors.prep_minutes }}
            </div>
        </div>
    </div>
</template>
