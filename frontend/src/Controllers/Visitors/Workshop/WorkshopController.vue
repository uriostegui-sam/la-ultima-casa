<script setup lang="ts">
import NewsCarousel from '@/components/views/Visitors/News/NewsCarousel.vue'
import Header from '@/components/layout/Header.vue'
import Footer from '@/components/layout/Footer.vue'
import Title from '@/components/Title.vue'
import CourseCard from '@/components/CourseCard.vue'
import MenuFilter from '@/components/MenuFilter.vue'
import { Languages, locale } from '@/Services/Translation/index.ts'
import { useWorkshopStore } from '@/stores/WorkshopStore'
import { onMounted, computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { capitalizeFirstLetter } from '@/Services/Helpers'

const { t } = useI18n()
const current = locale
const workshopStore = useWorkshopStore()
const activeFilter = ref('all')

const workshopTransformed = computed(() => {
  if (activeFilter.value === 'all') {
    return workshopStore.workshops.map((workshop) => ({
      ...workshop,
    }))
  }
  return workshopStore.workshops
    .filter((workshop) => workshop.type === activeFilter.value)
    .map((workshop) => ({
      ...workshop,
    }))
})

onMounted(async () => {
  await workshopStore.getWorkshops()
})
</script>

<template>
  <Header />
  <Title :title="capitalizeFirstLetter($t('workshops'))" />
  <MenuFilter :active="activeFilter" @change="activeFilter = $event" class="mb-12" />
  <section class="lg:pb-15 lg:pt-5 px-10 mx-auto">
    <div class="flex flex-wrap gap-y-7 gap-x-20">
      <CourseCard
        v-for="(workshop, index) in workshopTransformed"
        :key="index"
        :title="current === Languages.English ? workshop.title['en'] : workshop.title['es']"
        :description="current === Languages.English ? workshop.description['en'] : workshop.description['es']"
        :image="workshop.cover_image_url"
        :type="workshop.type"
        :id="`/workshops/${workshop.id}`"
      />
    </div>
  </section>
  <NewsCarousel />
  <Footer />
</template>

<style scoped></style>
