<script setup lang="ts">
defineProps<{
  header?: boolean
}>();

</script>

<template>
  <a href="#"
  :class="header ? '-m-1.5 p-1.5' : ''">
    <span class="sr-only">La Última Casa</span>
    <img
      :class="header ? 'h-20' : 'h-45'"
      class="w-auto"
      src="@/assets/Images/logo_UC.svg"
      alt="Logo de La Última Casa"
    />
  </a>
</template>
