<script setup lang="ts">
import InfoComponent from '@/visitors/components/InfoComponent.vue'
import type { News } from '@/shared/Interfaces/News'
import { Languages, locale } from '@/shared/services/Translation'
import { useNewsStore } from '@/shared/stores/NewsStore'
import { onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute } from 'vue-router'
import NewsCarousel from './NewsCarousel.vue'
import LoadingComponent from '@/shared/components/LoadingComponent.vue'
import { choseCurrentLanguage } from '@/shared/services/Helpers'

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
  <div v-if="currentNews">
    <InfoComponent
      :title="choseCurrentLanguage(currentNews.title, currentLang)"
      :cover-image="currentNews.image_url"
      :description="choseCurrentLanguage(currentNews.content, currentLang)"
    />
  </div>
  <div v-else><LoadingComponent /></div>
  <NewsCarousel />
</template>

<style scoped></style>
