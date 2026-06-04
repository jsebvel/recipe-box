<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
    title: string
    prepMinutes: number
}>()

console.log(props)
const formattedTime = computed(() => {
    const hours = Math.floor(props.prepMinutes / 60)
    const mins = props.prepMinutes % 60
    return hours > 0 ? `${hours}h ${mins}m` : `${mins}m`
})

const isExpanded = ref(false)
</script>

<template>
    <div class="border rounded p-4">
        <h2>{{ title }}</h2>
        <p>Tiempo: {{ formattedTime }}</p>
        <button @click="isExpanded = !isExpanded">
            {{ isExpanded ? 'ocultar' : 'Mostrar más' }}
        </button>
        <div v-if="isExpanded">
            <slot />
        </div>
    </div>
</template>
