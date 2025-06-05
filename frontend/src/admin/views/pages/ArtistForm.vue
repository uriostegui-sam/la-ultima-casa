<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useAdminArtistStore } from '../../stores/ArtistAdminStore'
import type { Artist } from '@/shared/Interfaces/Artist'
import { useRoute } from 'vue-router'
import { Languages, locale } from '@/shared/services/Translation'
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import { useAdminSkillStore } from '../../stores/SkillAdminStore'
import { useI18n } from 'vue-i18n'
import type { Artwork } from '@/shared/Interfaces/Artwork'

const currentLang = locale
const { t } = useI18n()
const route = useRoute()
const currentArtist = ref<Artist | null>(null)
const artistAdminStore = useAdminArtistStore()
const skillAdminStore = useAdminSkillStore()
const currentArtistSkills = ref<number[]>([])

const skillOptions = computed(() => {
  return skillAdminStore.skills.map((skill) => ({
    label: currentLang.value === Languages.English ? skill.name.en : skill.name.es,
    value: skill.id,
  }))
})

onMounted(async () => {
  const id = Number(route.params.id)

  await artistAdminStore.getArtist(id)
  await skillAdminStore.getSkills()
  currentArtist.value = artistAdminStore.selectedArtist
  currentArtistSkills.value = currentArtist.value?.skills.map((s) => s.id) || []
})
</script>

<template>
  <div v-if="currentArtist">
    <form class="flex flex-col gap-6 max-w-2xl" @submit.prevent="submitForm">
      <!-- Name -->
      <div>
        <label class="block font-semibold mb-1">{{ capitalizeFirstLetter(t('name')) }}</label>
        <InputText
          v-model="currentArtist.name"
          :placeholder="`${capitalizeFirstLetter(t('artistName'))}`"
          class="w-full"
        />
      </div>

      <!-- MiniBio (EN & ES) -->
      <div>
        <label class="block font-semibold mb-1">{{
          `${capitalizeFirstLetter(t('minibio'))}  ${capitalizeFirstLetter(t('english'))}`
        }}</label>
        <Textarea v-model="currentArtist.minibio.en" rows="2" class="w-full" />
      </div>
      <div>
        <label class="block font-semibold mb-1">{{
          `${capitalizeFirstLetter(t('minibio'))}  ${capitalizeFirstLetter(t('spanish'))}`
        }}</label>
        <Textarea v-model="currentArtist.minibio.es" rows="2" class="w-full" />
      </div>

      <!-- Bio (EN & ES) -->
      <div>
        <label class="block font-semibold mb-1">{{
          `${capitalizeFirstLetter(t('biography'))}  ${capitalizeFirstLetter(t('english'))}`
        }}</label>
        <Textarea v-model="currentArtist.bio.en" rows="5" class="w-full" />
      </div>
      <div>
        <label class="block font-semibold mb-1">{{
          `${capitalizeFirstLetter(t('biography'))}  ${capitalizeFirstLetter(t('spanish'))}`
        }}</label>
        <Textarea v-model="currentArtist.bio.es" rows="5" class="w-full" />
      </div>

      <!-- Skills -->
      <div>
        <label class="block font-semibold mb-1">{{ capitalizeFirstLetter(t('skills')) }}</label>
        <MultiSelect
          v-model="currentArtistSkills"
          :options="skillOptions"
          placeholder="Select Skills"
          class="w-full"
          option-label="label"
          option-value="value"
        />
      </div>

      <!-- Social Links -->
      <div>
        <label class="block font-semibold mb-1">Twitter</label>
        <InputText
          v-model="currentArtist.social_links.twitter"
          placeholder="@username"
          class="w-full"
        />
      </div>
      <div>
        <label class="block font-semibold mb-1">Instagram</label>
        <InputText
          v-model="currentArtist.social_links.instagram"
          placeholder="@username"
          class="w-full"
        />
      </div>
      <div>
        <label class="block font-semibold mb-1">{{ capitalizeFirstLetter(t('website')) }}</label>
        <InputText
          v-model="currentArtist.social_links.instagram"
          placeholder="@username"
          class="w-full"
        />
      </div>

      <!-- Submit -->
      <Button label="Submit" type="submit" class="w-full mt-4" />
    </form>

    <div class="flex flex-wrap mt-10 gap-3">
      <div v-for="(artwork, index) in currentArtist.artworks" :key="index" class="">
        <RouterLink :to="`/admin/artists/${currentArtist.id}/artwork/edit/${artwork.id}`">
          <p>{{ artwork.title }}</p>
          <!-- :src="getPrimaryImage(artwork)"  -->
          <Image
            src="https://primefaces.org/cdn/primevue/images/galleria/galleria10.jpg"
            alt="Image"
            width="250"
          />
        </RouterLink>
      </div>
    </div>
  </div>
  <div v-else>Loading...</div>
</template>
