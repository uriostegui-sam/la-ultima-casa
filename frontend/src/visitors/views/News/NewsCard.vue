<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  title?: string
  description?: string
  date?: string
  image?: string
}>()

const formattedDate = computed(() => {
  if (!props.date) return ''
  const d = new Date(props.date)
  return d.toLocaleDateString('de-DE')
})
</script>

<template>
  <div class="rounded flex flex-col h-full">
    <img class="w-full h-48 object-cover" src="https://picsum.photos/200" :alt="props.title" /> <!-- :src="props.image" -->
      <div class="flex flex-col justify-between flex-1">
        <div>
          <p class="text-sm text-gray-500 mb-1">{{ formattedDate }}</p>
          <h3 class="text-lg font-bold text-(--color-teal) mb-2">{{ props.title }}</h3>
          <p class="text-gray-700 text-sm">{{ props.description }}</p>
        </div>
      </div>
    </div>
  </template>

<style scoped></style>
