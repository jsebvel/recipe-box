import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h, type DefineComponent } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

import { createPinia } from "pinia";
import { useDraftStore } from "./store/draftStore";
import { createExtensionRegistry, EXTENSION_POINT_KEY } from "@/composables/useExtensionPoint";
import RelatedRecipesPanel from "@/Components/RelatedRecipesPanel.vue";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: async (name) => {
        const pages = import.meta.glob<{ default: DefineComponent }>(
            "./Pages/**/*.vue",
        );
        const module = await pages[`./Pages/${name}.vue`]();
        return module.default;
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(createPinia());
        app.use(plugin);
        app.use(ZiggyVue);

        const draftStore = useDraftStore();
        draftStore.initFromInertia(props.initialPage.props.activeDraft ?? null);
        const {slots, registerSlot} = createExtensionRegistry();
        app.provide(EXTENSION_POINT_KEY, slots);
        registerSlot("recipe.show.sidebar.after", RelatedRecipesPanel);

        app.mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
