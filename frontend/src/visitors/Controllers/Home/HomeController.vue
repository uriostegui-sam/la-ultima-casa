<script setup lang="ts">
import HeroSection from '@/visitors/components/home/HeroSection.vue'
import TechniquesSection from '@/visitors/components/home/TechniquesSection.vue'
import Header from '@/visitors/components/layout/Header.vue'
import Footer from '@/visitors/components/layout/Footer.vue'
import NewsCarousel from '@/visitors/views/News/NewsCarousel.vue'
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
