<script setup lang="ts">
import { Languages, locale } from '@/Services/Translation'
import { useRoute } from 'vue-router'
import { useArtistStore } from '@/stores/ArtistStore'
import { computed, onMounted, ref } from 'vue'
import type { Artist } from '@/Interfaces/Artist'
import { capitalizeFirstLetter } from '@/Services/Helpers'
import InfoComponent from '@/components/InfoComponent.vue'
import type { TranslatedSkill } from '@/Interfaces/Skill'
import ArtworkGallery from '@/components/views/Visitors/Artwork/ArtworkGallery.vue'
import NewsCarousel from '../News/NewsCarousel.vue'
import Footer from '@/components/layout/Footer.vue'
import Header from '@/components/layout/Header.vue'

const currentLang = locale
const route = useRoute()
const artistStore = useArtistStore()
const currentArtist = ref<Artist | null>(null)

const skillsTransformed = computed(() => {
  if (!currentArtist.value?.skills) return ''

  const skills = currentArtist.value.skills as TranslatedSkill[]

  const translatedNames = skills.map((skill) => 
    currentLang.value === Languages.English ?
      skill.en : skill.es
  )

  return capitalizeFirstLetter(translatedNames.join(', ').toLowerCase())
})

onMounted(async() => {
    const id = Number(route.params.id)

    await artistStore.getArtist(id)
    currentArtist.value = artistStore.selectedArtist
})
</script>

<template>
  <Header />
  <div v-if="currentArtist">
    <div>
      <InfoComponent
        :is-workshop="false"
        :is-artist="true"
        :title="currentArtist.name"
        :has-subtitle="true"
        :subtitle="skillsTransformed"
        :cover-image="currentArtist.profile_image_url"
        :description="
          currentLang === Languages.English
            ? currentArtist.bio['en']
            : currentArtist.bio['es']
        "
        :instagram="currentArtist.social_links['instagram'] ? currentArtist.social_links['instagram'] : undefined"
        :facebook="currentArtist.social_links['twitter'] ? currentArtist.social_links['twitter'] : undefined"
        :website="currentArtist.social_links['website'] ? currentArtist.social_links['website'] : undefined"
        :artworks="currentArtist.artworks"
      />
      <ArtworkGallery :artist="currentArtist.name" :artworks="currentArtist.artworks" />
    </div>
  </div>
  <div v-else>Loading...</div>
  <NewsCarousel />
  <Footer />
</template>

<style scoped></style>
