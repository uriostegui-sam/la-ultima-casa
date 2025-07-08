<script setup lang="ts">
import { Languages, locale } from '@/shared/services/Translation'
import { useRoute } from 'vue-router'
import { useArtistStore } from '@/shared/stores/ArtistStore'
import { computed, onMounted, ref } from 'vue'
import type { Artist } from '@/shared/Interfaces/Artist'
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import InfoComponent from '@/visitors/components/InfoComponent.vue'
import type { TranslatedSkill } from '@/shared/Interfaces/Skill'
import NewsCarousel from '../News/NewsCarousel.vue'
import ArtworkGallery from '../Artwork/ArtworkGallery.vue'
import LoadingComponent from '@/shared/components/LoadingComponent.vue'

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
  <div v-else><LoadingComponent /></div>
  <NewsCarousel />
</template>

<style scoped></style>
