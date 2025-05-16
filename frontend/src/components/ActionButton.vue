<script setup lang="ts">
const props = defineProps<{
  title?: string
  color?: string
//   href?: string
}>()

</script>

<template>
  <button 
    class="text-white font-bold py-2 px-4 rounded"
    :style="{ backgroundColor: `var(${color})` }"
  >
    <slot>{{props.title}}</slot>
  </button>
</template>

<style scoped>
</style>
