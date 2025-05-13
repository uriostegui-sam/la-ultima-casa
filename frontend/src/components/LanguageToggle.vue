<script setup lang="ts">
import { locale, switchLanguage } from '@/Services/Translation/index.ts';
const current = locale;

const props = defineProps({
  current: {
    type: String,
    default: 'es-ES',
  },
  header: {
    type: Boolean,
    default: false,
  },
});
</script>

<template>
  <div class="flex gap-4">
    <button 
        :class="{ 'font-bold': current === 'es-ES' }"
        @click="switchLanguage('es-ES')"
    >ESP</button>
    <span v-if="header" class="text-(--color-salmon)">|</span>
    <span v-else class="text-white">|</span>
    <button
    :class="{ 'font-bold': current === 'en-GB' }"
      @click="switchLanguage('en-GB')"
      >ENG</button>
  </div>
</template>