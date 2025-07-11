<script setup lang="ts">
import Logo_UC from '@/visitors/assets/Images/logo_UC.svg'
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
          header ? 'h-20 md:h-25' : hero ? 'h-50 md:h-55' : 'h-25 md:h-35',
          hero ? 'opacity-50' : '',
        ]"
      />
    </a>
  </router-link>
</template>
