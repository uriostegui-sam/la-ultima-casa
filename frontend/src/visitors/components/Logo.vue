<script setup lang="ts">
import { useAdminAboutUsStore } from '@/admin/stores/AboutUsAdminStore';
import type { AboutUs } from '@/shared/Interfaces/AboutUs';
import { onMounted, ref } from 'vue';
const props = defineProps<{
  header?: boolean
  hero?: boolean
}>()

const baseUrl = import.meta.env.VITE_STORAGE_URL
const aboutUsAdminStore = useAdminAboutUsStore()
const aboutUs = ref<AboutUs | null>(null);

onMounted(async () => {
    await aboutUsAdminStore.getAboutUsById(1);

    aboutUs.value = aboutUsAdminStore.selectedAboutUs;
})
</script>

<template>
  <router-link
    to="/"
    custom v-slot="{ navigate }"
  >
    <a class="cursor-pointer" :class="header ? '-m-1.5 p-1.5' : ''" @click="navigate">
      <span class="sr-only">La Última Casa</span>
      <img
        v-if="aboutUs"
        :class="[
          header ? 'w-10 md:w-20' : hero ? '' : 'w-25 md:w-35',
        ]"
        :src="`${baseUrl}/${header ? `${aboutUs.logo_header}` : hero ? `aboutUs/logo/logo-hero.png` : `${aboutUs.logo_footer}`}`"
        alt="logo de la última casa"
      >

    </a>
  </router-link>
</template>
