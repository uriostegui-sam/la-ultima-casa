<script setup lang="ts">
import Logo_UC from '@/assets/Images/logo_UC.svg'
defineProps<{
  header?: boolean
  hero?: boolean
}>()
</script>

<template>
  <router-link
    to="/"
    custom v-slot="{ navigate }"
  >
    <a class="cursor-pointer" :class="header ? '-m-1.5 p-1.5' : ''" @click="navigate">
      <span class="sr-only">La Última Casa</span>
      <Logo_UC
        :class="[
          header ? 'h-20 md:h-35' : 'h-35 md:h-45',
          hero ? 'opacity-50' : '',
        ]"
      />
    </a>
  </router-link>
</template>
