<script setup lang="ts">
import NewsCarousel from '@/components/views/Visitors/News/NewsCarousel.vue'
import Header from '@/components/layout/Header.vue'
import Footer from '@/components/layout/Footer.vue'
import { useI18n } from 'vue-i18n'
import { Languages, locale } from '@/Services/Translation'
import { useArtistStore } from '@/stores/ArtistStore'
import { computed, onMounted } from 'vue'
import CourseCard from '@/components/CourseCard.vue'
import Title from '@/components/Title.vue'
import { capitalizeFirstLetter } from '@/Services/Helpers'

const { t } = useI18n()
const current = locale
const artistStore = useArtistStore()

const artistTransformed = computed(() => {
  return artistStore.artists.map((artist) => ({
    ...artist,
  }))
})

onMounted(async () => {
  await artistStore.getArtists()
})
</script>

<template>
  <Header />
  <Title :title="capitalizeFirstLetter($t('artists'))" />
  <section class="lg:pb-15 lg:pt-5 px-10 mx-auto">
    <div class="flex flex-wrap gap-y-7 lg:gap-x-20">
      <CourseCard
        v-for="(artist, index) in artistTransformed"
        :key="index"
        :title="artist.name"
        :description="current === Languages.English ? artist.minibio['en'] : artist.minibio['es']"
        :image="artist.profile_image_url"
        :id="`/artists/${artist.id}`"
      />
    </div>
  </section>
  <NewsCarousel />
  <Footer />
</template>

<style scoped></style>
