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
            await router.post(
                "/recipes/draft",
                {
                    title: this.draft.title,
                    body: this.draft.body,
                    prep_minutes: this.draft.prep_minutes,
                },
                {
                    onSuccess: () => {
                        router.reload({ only: ["activeDraft"] });
                    },
                },
            );
        },
        ensureDraft(user: { id: number; name: string }) {
            if (this.draft) return;
            this.draft = {
                id: 0,
                title: "",
                body: "",
                prep_minutes: 0,
                is_draft: true,
                author: {
                    id: user.id,
                    name: user.name,
                    email: "",
                },
                tags: [],
            };
        },
    },
});
