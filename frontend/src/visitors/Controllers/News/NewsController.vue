<script setup lang="ts">
import CourseCard from '@/visitors/components/CourseCard.vue'
import Title from '@/visitors/components/Title.vue'
import { capitalizeFirstLetter, choseCurrentLanguage } from '@/shared/services/Helpers'
import { Languages, locale } from '@/shared/services/Translation'
import { useNewsStore } from '@/shared/stores/NewsStore'
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
  <Title :title="capitalizeFirstLetter($t('news.latestNews'))" />
  <section class="lg:pb-15 lg:pt-5 px-10 mx-auto pb-8 max-w-screen-2xl">
    <div class="flex flex-wrap gap-y-7 gap-x-20">
      <CourseCard
        v-for="(news, index) in newsTransformed"
        :key="index"
        :title="choseCurrentLanguage(news.title, current)"
        :description="choseCurrentLanguage(news.content, current)"
        :image="news.image_url"
        :id="`/news/${news.id}`"
      />
    </div>
  </section>
</template>

<style scoped></style>
