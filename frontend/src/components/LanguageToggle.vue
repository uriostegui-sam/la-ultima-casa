<script setup lang="ts">
import { locale, switchLanguage } from '@/Services/Translation/index.ts'
const current = locale

defineProps<{
  header?: boolean
}>()
</script>

<template>
  <div class="flex gap-4">
    <button
      class="hover:underline"
      :class="{ 'font-bold': current === 'es-ES' }"
      @click="switchLanguage('es-ES')"
    >
      ESP
    </button>
    <span :class="header ? 'text-(--color-salmon)' : 'text-white'">|</span>
    <button
      class="hover:underline"
      :class="{ 'font-bold': current === 'en-GB' }"
      @click="switchLanguage('en-GB')"
    >
      ENG
    </button>
  </div>
</template>