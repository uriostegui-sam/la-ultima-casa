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
    {{props.title}}
  </button>
</template>

<style scoped>
button {
  background-color: var(--color-teal) !important;
}
</style>
