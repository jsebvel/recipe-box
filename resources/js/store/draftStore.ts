import { Recipe } from "@/types";
import { defineStore } from "pinia";
import { router } from "@inertiajs/vue3";

export const useDraftStore = defineStore("draft", {
    state: (): { draft: Recipe | null } => ({
        draft: null,
    }),

    actions: {
        initFromInertia(draft: Recipe | null) {
            this.draft = draft;
        },
        updateField<K extends keyof Recipe>(field: K, value: Recipe[K]) {
            if (this.draft) {
                this.draft[field] = value;
            }
        },

        async persist() {
            if (!this.draft) return;
            await router.post("/recipes/draft", this.draft, {
                onSuccess: () => {
                    router.reload({ only: ["activeDraft"] });
                },
            });
        },
    },
});
