<script setup lang="ts">
import HeroSection from '@/visitors/components/home/HeroSection.vue'
import TechniquesSection from '@/visitors/components/home/TechniquesSection.vue'
import NewsCarousel from '@/visitors/views/News/NewsCarousel.vue'
</script>

<template>
  <HeroSection :first="true" />
  <HeroSection :reverse="true" />
  <HeroSection :reverse="false" />
  <TechniquesSection />
  <NewsCarousel />
</template>

<style scoped></style>
