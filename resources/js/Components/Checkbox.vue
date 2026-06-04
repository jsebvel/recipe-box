<script setup lang="ts">
import { computed } from 'vue'

const emit = defineEmits<{
    'update:checked': [val: boolean | unknown[]]
}>()

const props = withDefaults(defineProps<{
    checked: boolean | unknown[]
    value?: unknown
}>(), {
    value: undefined,
})

const proxyChecked = computed({
    get() {
        return props.checked
    },

    set(val: boolean | unknown[]) {
        emit('update:checked', val)
    },
})
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
    />
</template>
