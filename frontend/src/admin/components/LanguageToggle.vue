<script setup lang="ts">
import { Languages, locale, switchLanguage } from '@/shared/services/Translation'
const current = locale

defineProps<{
  header?: boolean
}>()
</script>

<template>
  <div 
   class="flex gap-4 px-4"
  >
    
    <button
      class="hover:underline"
      :class="{ 'font-bold': current === Languages.Spanish }"
      @click="switchLanguage(Languages.Spanish)"
    >
      ESP
    </button>
    <button
      class="hover:underline"
      :class="{ 'font-bold': current === Languages.English }"
      @click="switchLanguage(Languages.English)"
    >
      ENG
    </button>
  </div>
</template>

<style scoped>
</style>
