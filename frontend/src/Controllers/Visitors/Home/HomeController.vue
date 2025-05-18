<script setup lang="ts">
import HeroSection from '@/components/home/HeroSection.vue'
import TechniquesSection from '@/components/home/TechniquesSection.vue'
import NewsCarousel from '@/components/layout/NewsCarousel.vue'
import Header from '@/components/layout/Header.vue'
import Footer from '@/components/layout/Footer.vue'
</script>

<template>
  <Header />
  <HeroSection :first="true" />
  <HeroSection :reverse="true" />
  <HeroSection :reverse="false" />
  <TechniquesSection />
  <NewsCarousel />
  <Footer />
</template>

<style scoped></style>
