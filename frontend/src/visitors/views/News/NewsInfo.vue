<script setup lang="ts">
import InfoComponent from '@/visitors/components/InfoComponent.vue';
import Footer from '@/visitors/components/layout/Footer.vue';
import type { News } from '@/shared/Interfaces/News';
import { Languages, locale } from '@/shared/services/Translation';
import { useNewsStore } from '@/shared/stores/NewsStore';
import { onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRoute } from 'vue-router';
import NewsCarousel from './NewsCarousel.vue';
import Header from '@/visitors/components/layout/Header.vue';

const currentLang = locale
const route = useRoute()
const newsStore = useNewsStore()
const currentNews = ref<News | null>(null)
const { t } = useI18n()

onMounted(async () => {
    const id = Number(route.params.id)

    await newsStore.getNewsById(id)
    currentNews.value = newsStore.selectedNews
})
</script>

<template>
  <Header />
  <div v-if="currentNews">
        <InfoComponent
      :title="
        currentLang === Languages.English
          ? currentNews.title['en']
          : currentNews.title['es']
      "
      :cover-image="currentNews.image_url"
      :description="
        currentLang === Languages.English
          ? currentNews.content['en']
          : currentNews.content['es']
      "
    />
  </div>
  <div v-else>Loading...</div>
   <NewsCarousel />
  <Footer />
</template>

<style scoped></style>