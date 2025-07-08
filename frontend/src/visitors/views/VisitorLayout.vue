<script setup>
import '@/visitors/assets/Css/style.css'
import Footer from '../components/layout/Footer.vue';
import Header from '../components/layout/Header.vue';
</script>

<template>
  <div class="min-h-screen flex flex-col visitor-layout">
    <Header />

    <main class="flex-grow">
      <router-view />
    </main>

    <Footer />
  </div>
</template>
