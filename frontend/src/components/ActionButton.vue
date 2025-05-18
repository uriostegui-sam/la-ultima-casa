<script setup lang="ts">
import { capitalizeFirstLetter } from '@/Services/Helpers';
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps<{
  title?: string
  color?: string
//   href?: string
  isFilter?: boolean
  active?: string
  filter?: (e: MouseEvent) => void
}>()

const onClick = (e: MouseEvent) => {
  if (props.filter) {
    props.filter(e)
  }
}
</script>

<template>
  <button 
    class="py-2 px-4"
    :class="[
      props.isFilter
        ? 'rounded-full border text-sm font-medium transition-all border-(--color-salmon)'
        : 'text-white font-bold rounded hover:bg-white hover:opacity-75',
      props.isFilter && props.active === props.title
        ? 'bg-(--color-salmon) text-white'
        : 'bg-white text-(--color-salmon) hover:bg-(--color-salmon) hover:text-white',
    ]"
    :style="{ backgroundColor: `var(${color})` }"
    @click="onClick"
  >
    <slot>{{capitalizeFirstLetter(t(props.title))}}</slot>
  </button>
</template>

<style scoped>
</style>
