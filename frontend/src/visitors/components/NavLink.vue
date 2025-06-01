<script setup lang="ts">
const props = defineProps<{
  href: string
  active: boolean
  navigate?: (e: MouseEvent) => void
}>()

const onClick = (e: MouseEvent) => {
  if (props.navigate) {
    props.navigate(e)
  }
}
</script>

<template>
  <a
    :href="props.href"
    @click="onClick"
    :class="[
      props.active ? 'font-bold' : 'text-gray-500',
      'hover:font-bold transition-colors duration-200'
    ]"
  >
    <slot />
  </a>
</template>
