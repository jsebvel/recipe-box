import { inject, ref, computed, type Component, type Ref } from "vue";

type ExtensionSlots = Record<string, Component[]>;

export const EXTENSION_POINT_KEY = "extension-points";

export function createExtensionRegistry() {
    const slots = ref<ExtensionSlots>({});
    return {
        slots,
        registerSlot(pointName: string, component: Component) {
            slots.value[pointName] ??= [];
            slots.value[pointName].push(component);
        },
    };
}

export function useExtensionPoint(pointName: string) {
    const slots = inject<Ref<ExtensionSlots>>(
        EXTENSION_POINT_KEY,
        ref<ExtensionSlots>({}),
    );

    return computed(() => slots.value[pointName] || []);
}
