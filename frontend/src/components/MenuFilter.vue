<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import ActionButton from './ActionButton.vue';
const { t } = useI18n()

const props = defineProps<{
  active: string
}>()

const options = ['all', 'permanent', 'temporary']
</script>

<template>
  <div class="flex justify-center gap-3 lg:gap-x-20">
    <ActionButton
      v-for="option in options"
      :is-filter="true"
      :active="props.active"
      :key="option"
      :title="option"
      variant="outline"
      @click="$emit('change', option)"
    />
  </div>
</template>
