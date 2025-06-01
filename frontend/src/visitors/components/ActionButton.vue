<script setup lang="ts">
import { capitalizeFirstLetter } from '@/shared/services/Helpers';
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps<{
  title?: string
  color?: string
  href?: string
  isFilter?: boolean
  active?: string
  filter?: (value: string) => void
}>()

const emit = defineEmits(['click'])

const handleClick = () => {
  if (props.isFilter && props.title) {
    props.filter?.(props.title)
  }
  emit('click')
}
</script>

<template>
  <router-link 
    v-if="!isFilter && href"
    :to="href"
    class="py-2 px-4 text-white font-bold rounded hover:bg-white hover:opacity-75 text-center"
    :style="{ backgroundColor: `var(${color})` }"
  >
    <slot>{{ capitalizeFirstLetter(t(title || '')) }}</slot>
  </router-link>

  <button
    v-else
    class="py-2 px-4 rounded-full border text-sm font-medium transition-all border-(--color-salmon)"
    :class="{
      'bg-(--color-salmon) text-white': active === title,
      'bg-white text-(--color-salmon) hover:bg-(--color-salmon) hover:text-white': active !== title
    }"
    @click="handleClick"
  >
    <slot>{{ capitalizeFirstLetter(t(title || '')) }}</slot>
  </button>
</template>

<style scoped>
</style>
