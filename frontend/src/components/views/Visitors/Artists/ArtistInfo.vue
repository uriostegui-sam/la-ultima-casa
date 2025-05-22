<script setup lang="ts">
import { Languages, locale } from '@/Services/Translation'
import { useRoute } from 'vue-router'
import { useArtistStore } from '@/stores/ArtistStore'
import { computed, onMounted, ref } from 'vue'
import type { Artist } from '@/Interfaces/Artist'
import { capitalizeFirstLetter } from '@/Services/Helpers'
import InfoComponent from '@/components/InfoComponent.vue'

const currentLang = locale
const route = useRoute()
const artistStore = useArtistStore()
const currentArtist = ref<Artist | null>(null)

const skillsTransformed = computed(() => {
  if (!currentArtist.value?.skills) return ''

  const skills = currentArtist.value.skills

  return capitalizeFirstLetter(skills.toString().toLowerCase()).replaceAll(
    ',',
    ', ',
  )
})

onMounted(async() => {
    const id = Number(route.params.id)

    await artistStore.getArtist(id)
    currentArtist.value = artistStore.selectedArtist
})
</script>

<template>
    <div v-if="currentArtist">
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
  </div>
  <div v-else>Loading...</div>
</template>

<style scoped></style>
