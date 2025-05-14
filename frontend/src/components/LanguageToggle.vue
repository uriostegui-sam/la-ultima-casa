<script setup lang="ts">
import { Languages, locale, switchLanguage } from '@/Services/Translation/index.ts'
const current = locale

defineProps<{
  header?: boolean
}>()
</script>

<template>
  <div class="flex gap-4">
    <button
      class="hover:underline"
      :class="{ 'font-bold': current === Languages.Spanish }"
      @click="switchLanguage(Languages.Spanish)"
    >
      ESP
    </button>
    <span :class="header ? 'text-(--color-salmon)' : 'text-white'">|</span>
    <button
      class="hover:underline"
      :class="{ 'font-bold': current === Languages.English }"
      @click="switchLanguage(Languages.English)"
    >
      ENG
    </button>
  </div>
</template>