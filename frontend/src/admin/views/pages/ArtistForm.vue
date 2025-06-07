<script setup lang="ts">
import { ref, computed, watchEffect, onMounted } from 'vue'
import { useAdminArtistStore } from '@/admin/stores/ArtistAdminStore'
import { useAdminSkillStore } from '@/admin/stores/SkillAdminStore'
import type { Artist, ArtistCreatePayload, ArtistUpdatePayload } from '@/shared/Interfaces/Artist'
import { Languages, locale } from '@/shared/services/Translation'
import { useI18n } from 'vue-i18n'
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import { useRoute } from 'vue-router'
import { useToast } from 'primevue/usetoast'
import type { ArtistFormErrors } from '@/admin/Interfaces/Error'

const emit = defineEmits<{
  (e: 'success', artist: Artist): void
}>()

const errors = ref<ArtistFormErrors>({})
const toast = useToast()
const route = useRoute()
const { t } = useI18n()
const id = Number(route.params.id)
const currentLang = locale
const artistAdminStore = useAdminArtistStore()
const skillAdminStore = useAdminSkillStore()
const isEditMode = computed(() => !!id)
const currentArtist = ref<Artist | null>(null)
const currentArtistSkills = ref<number[]>([])
const artist = ref<Artist | null>(null)
const skillOptions = computed(() =>
  skillAdminStore.skills.map((skill) => ({
    label: currentLang.value === Languages.English ? skill.name.en : skill.name.es,
    value: skill.id,
  })),
)

onMounted(async () => {
  await skillAdminStore.getSkills()

  if (route.params.id) {
    await artistAdminStore.getArtist(id)
    artist.value = artistAdminStore.selectedArtist

    currentArtist.value = JSON.parse(JSON.stringify(artist.value))
    currentArtistSkills.value = artist.value?.skills
      ? artist.value.skills.map((skill) => skill.id)
      : []
  } else {
    currentArtist.value = {
      id: 0,
      user_id: 0,
      name: '',
      user: {
        name: '',
        lastname: '',
        email: '',
      },
      minibio: { en: '', es: '' },
      bio: { en: '', es: '' },
      skills: [],
      social_links: { website: '', instagram: '', twitter: '', flickr: '' },
      artworks: [],
    }
  }
})

const handleSubmit = async () => {
  errors.value = {}; 
  if (!currentArtist.value) return
  
  try {
    const payload: ArtistCreatePayload | ArtistUpdatePayload = {
      user_id: currentArtist.value.user_id,
      user: currentArtist.value.user,
      minibio: currentArtist.value.minibio,
      bio: currentArtist.value.bio,
      skills: currentArtistSkills.value,
      social_links: currentArtist.value.social_links,
    }

    let result: Artist
    if (isEditMode.value) {
      result = await artistAdminStore.updateArtist(id, { ...payload, id } as ArtistUpdatePayload)
    } else {
      result = await artistAdminStore.createArtist(payload as ArtistCreatePayload)
    }

    emit('success', result)
    toast.add({
      severity: 'success',
      summary: capitalizeFirstLetter(t('success')),
      detail: capitalizeFirstLetter(t('artistSavedSuccessfully')),
      life: 3000,
    })
  } catch (err: unknown) {
    let errorMessage = t('errorSavingArtist')

    if (err && typeof err === 'object' && 'message' in err) {
      errorMessage = (err as { message?: string })?.message ?? errorMessage
    }
    toast.add({
      severity: 'error',
      summary: capitalizeFirstLetter(t('error')),
      detail: capitalizeFirstLetter(t(errorMessage)) || capitalizeFirstLetter(t('errorSavingArtist')),
      life: 5000
    })
  }
}
</script>

<template>
  <div v-if="currentArtist">
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Name -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{ capitalizeFirstLetter(t('name')) }}</label>
          <InputText
            v-model="currentArtist.user.name"
            :placeholder="`${capitalizeFirstLetter(t('artistName'))}`"
            class="w-full"
          />          
        </div>
        <div>
          <label class="block font-semibold mb-1">{{ capitalizeFirstLetter(t('lastName')) }}</label>
          <InputText
            v-model="currentArtist.user.lastname"
            :placeholder="`${capitalizeFirstLetter(t('artistLastName'))}`"
            class="w-full"
          />          
        </div>
      </div>

      <!-- MiniBio (EN & ES) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('minibio'))} ${capitalizeFirstLetter(t('english'))}`
          }}</label>
          <Textarea v-model="currentArtist.minibio.en" rows="2" class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('minibio'))} ${capitalizeFirstLetter(t('spanish'))}`
          }}</label>
          <Textarea v-model="currentArtist.minibio.es" rows="2" class="w-full" />          
        </div>
      </div>

      <!-- Bio (EN & ES) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('biography'))} ${capitalizeFirstLetter(t('english'))}`
          }}</label>
          <Textarea v-model="currentArtist.bio.en" rows="5" class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('biography'))} ${capitalizeFirstLetter(t('spanish'))}`
          }}</label>
          <Textarea v-model="currentArtist.bio.es" rows="5" class="w-full" />          
        </div>
      </div>

      <!-- Skills -->
      <div>
        <label class="block font-semibold mb-1">{{
          capitalizeFirstLetter(t('selectSkills'))
        }}</label>
        <MultiSelect
          v-model="currentArtistSkills"
          :options="skillOptions"
          :placeholder="capitalizeFirstLetter(t('selectSkills'))"
          class="w-full"
          option-label="label"
          option-value="value"
        />        
      </div>

      <!-- Social Links -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
            v-model="currentArtist.social_links.website"
            placeholder="http://www.website.com"
            class="w-full"
          />
        </div>
      </div>

      <!-- Submit -->
      <Button
        :label="capitalizeFirstLetter(t('saveArtist'))"
        type="submit"
        class="w-full md:w-auto"
      />
    </form>

    <div class="flex flex-wrap mt-10 gap-3">
      <div v-for="(artwork, index) in currentArtist.artworks" :key="index" class="">
        <RouterLink :to="`/admin/artists/${currentArtist.id}/artwork/edit/${artwork.id}`">
          <p>{{ artwork.title }}</p>
          <!-- :src="getPrimaryImage(artwork)"  -->
          <Image
            src="https://primefaces.org/cdn/primevue/images/galleria/galleria10.jpg"
            :alt="artwork.title"
            width="250"
          />
        </RouterLink>
      </div>
    </div>
  </div>
  <div v-else>Loading...</div>
</template>
