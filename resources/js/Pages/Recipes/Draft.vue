<script setup lang="ts">
import { computed, watch } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { useDraftStore } from "@/store/draftStore";

const draftStore = useDraftStore();
const page = usePage();
watch(
    () => page.props.activeDraft,
    (d) => {
        draftStore.initFromInertia(d ?? null);
    },
);

const draft = computed(() => draftStore.draft);
const flash = computed(() => page.props.flash);

function ensureDraft() {
    const user = page.props.auth?.user;
    if (!user) return;
    draftStore.ensureDraft(user);
}
</script>

<template>
    <div class="mx-auto max-w-3xl px-4 py-8">
        <Link
            :href="route('recipes.index')"
            class="mb-4 inline-block text-sm text-gray-500 hover:text-amber-600"
        >
            ← Volver a recetas
        </Link>

        <div
            v-if="flash?.success"
            class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-700"
        >
            {{ flash.success }}
        </div>

        <div v-if="!draft" class="text-center py-12">
            <h1 class="text-2xl font-bold text-gray-800 mb-3">
                No tienes borrador
            </h1>
            <button
                @click="ensureDraft"
                class="rounded-lg bg-blue-600 px-6 py-2 text-white hover:bg-blue-700"
            >
                Empezar borrador
            </button>
        </div>

        <form
            v-else
            @submit.prevent="draftStore.persist()"
            class="rounded-2xl bg-white p-8 shadow-lg"
        >
            <h1 class="mb-6 text-2xl font-bold text-gray-800">Borrador</h1>

            <label class="block mb-4">
                <span class="font-bold text-gray-600">Título</span>
                <input
                    :value="draft.title"
                    @input="
                        draftStore.updateField(
                            'title',
                            ($event.target as HTMLInputElement).value,
                        )
                    "
                    class="mt-1 w-full rounded-xl border border-gray-400 p-2"
                />
            </label>
            <label class="block mb-4">
                <span class="font-bold text-gray-600">Cuerpo</span>
                <textarea
                    :value="draft.body"
                    @input="
                        draftStore.updateField(
                            'body',
                            ($event.target as HTMLTextAreaElement).value,
                        )
                    "
                    rows="5"
                    class="mt-1 w-full rounded-xl border border-gray-400 p-2"
                />
            </label>
            <label class="block mb-4">
                <span class="font-bold text-gray-700"
                    >Tiempo de prepración (en minutos)</span
                >
                <input
                    :value="draft.prep_minutes"
                    @input="
                        draftStore.updateField(
                            'prep_minutes',
                            Number(($event.target as HTMLInputElement).value),
                        )
                    "
                    type="number"
                    class="mt-1 w-full rounded-xl border border-gray-400 p-2"
                />
            </label>
            <button
                type="submit"
                class="mt-2 rounded-lg bg-blue-600 px-6 py-2 text-white hover:bg-blue-700"
            >
                Guardar borrador
            </button>
        </form>
    </div>
</template>
