<script setup lang="ts">
import NewsCarousel from '@/visitors/views/News/NewsCarousel.vue'
import { useI18n } from 'vue-i18n'
import { Languages, locale } from '@/shared/services/Translation'
import { useArtistStore } from '@/shared/stores/ArtistStore'
import { computed, onMounted } from 'vue'
import CourseCard from '@/visitors/components/CourseCard.vue'
import Title from '@/visitors/components/Title.vue'
import { capitalizeFirstLetter } from '@/shared/services/Helpers'

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
  <Title :title="capitalizeFirstLetter($t('artists'))" />
  <section class="md:pb-15 md:pt-5 px-10 mx-auto max-w-screen-2xl">
    <div class="flex flex-wrap gap-y-7 md:gap-x-20">
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
</template>

<style scoped></style>
