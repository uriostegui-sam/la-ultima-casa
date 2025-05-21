<script setup lang="ts">
import CourseCard from '@/components/CourseCard.vue'
import Footer from '@/components/layout/Footer.vue'
import Header from '@/components/layout/Header.vue'
import Title from '@/components/Title.vue'
import { Languages, locale } from '@/Services/Translation/index.ts'
import { useNewsStore } from '@/stores/NewsStore'
import { computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'

const newsStore = useNewsStore()
const { t } = useI18n()
const current = locale

const newsTransformed = computed(() => {
  return newsStore.news.map((news) => ({
    ...news,
  }))
})

onMounted(async () => {
  await newsStore.getNews()
})
</script>

<template>
  <Header />
  <Title :title="$t('latestNews')" />
  <section class="lg:pb-15 lg:pt-5 px-10 mx-auto">
    <div class="flex flex-wrap gap-y-7 gap-x-20">
      <CourseCard
        v-for="(news, index) in newsTransformed"
        :key="index"
        :title="current === Languages.English ? news.title['en'] : news.title['es']"
        :description="current === Languages.English ? news.content['en'] : news.content['es']"
        :image="news.image_url"
        :id="`/news/${news.id}`"
      />
    </div>
  </section>
  <Footer />
</template>

<style scoped></style>
