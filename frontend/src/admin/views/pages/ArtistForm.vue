<script setup lang="ts">
import { ref, computed, watchEffect, onMounted } from 'vue'
import { useAdminArtistStore } from '@/admin/stores/ArtistAdminStore'
import { useAdminSkillStore } from '@/admin/stores/SkillAdminStore'
import type { Artist, ArtistCreatePayload, ArtistUpdatePayload } from '@/shared/Interfaces/Artist'
import { Languages, locale } from '@/shared/services/Translation'
import { useI18n } from 'vue-i18n'
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'primevue/usetoast'
import { useAdminArtworkStore } from '@/admin/stores/ArtworkAdminStore'
import { showErrorToast, showSuccessToast } from '@/admin/Services/Helpers'
import type { Artwork } from '@/shared/Interfaces/Artwork'
import TitleForm from '@/admin/components/TitleForm.vue'
import LoadingComponent from '@/shared/components/LoadingComponent.vue'
import { useAuthStore } from '@/shared/stores/AuthStore'
import AuthService from '@/shared/services/DataLayers/AuthService'
import type { PasswordReset } from '@/shared/Interfaces/User'

const emit = defineEmits<{
  (e: 'success', artist: Artist): void
  (e: 'success', passwordReset: PasswordReset): void
}>()


const baseUrl = import.meta.env.VITE_STORAGE_URL
const profileImageFile = ref<File | null>(null)
const profileImagePreview = ref<string | null>(null)
const toast = useToast()
const route = useRoute()
const router = useRouter()
const { t } = useI18n()
const authStore = useAuthStore()
const artistId = authStore.user?.artist?.id
const isAdmin = computed(() => authStore.isAdmin)
const id = computed(() => Number(route.params.id))
const currentLang = locale
const artistAdminStore = useAdminArtistStore()
const artworkAdminStore = useAdminArtworkStore()
const skillAdminStore = useAdminSkillStore()
const isEditMode = computed(() => !Number.isNaN(id.value))
const currentArtist = ref<Artist | null>(null)
const currentArtistSkills = ref<number[]>([])
const artist = ref<Artist | null>(null)
const displayConfirmation = ref(false)
const artworkToDelete = ref<number | string | null>(null)
const isOwnersProfile = computed(() => id.value === artistId)
const token = ref<string | null>(null)
const skillOptions = computed(() =>
  skillAdminStore.skills.map((skill) => ({
    label: currentLang.value === Languages.English ? skill.name.en : skill.name.es,
    value: skill.id,
  })),
)

const openConfirmation = (id: number | string) => {
  artworkToDelete.value = id
  displayConfirmation.value = true
}

function closeConfirmation() {
  displayConfirmation.value = false
  artworkToDelete.value = null
}

const onProfileImageSelect = (event: any) => {
  const file = event.files?.[0]
  if (file) {
    profileImageFile.value = file
    profileImagePreview.value = URL.createObjectURL(file)
  }
}

const removeProfileImage = () => {
  profileImageFile.value = null
  profileImagePreview.value = null
}

const removeArtwork = (id: string | number) => {
  if (!currentArtist.value) return

  displayConfirmation.value = false
  try {
    artworkAdminStore.deleteArtwork(id)

    currentArtist.value.artworks =
      currentArtist.value.artworks?.filter((art) => art.id !== id) || []

    showSuccessToast(toast, t, 'artistSavedSuccessfully', 3000)
  } catch (err: unknown) {
    showErrorToast(toast, t, err, 'errorSavingArtist')
  }
}

const generateResetToken = async () => {
  if (!currentArtist.value) return
  if (!authStore.isAdmin) {
    showErrorToast(toast, t, 'notAuthorized', 'errorNotAuthorized')
    return
  }
  
  try {
    const payload: PasswordReset = {
      id: currentArtist.value.user_id,
      id_admin: authStore.user?.id as number ?? 0,
      token: '',
    }
    let result: PasswordReset

    result = await AuthService.generateResetToken(payload)
    token.value = result.token

    emit('success', result)
    showSuccessToast(toast, t, 'artistSavedSuccessfully', 3000)
  } catch (err: unknown) {
    showErrorToast(toast, t, err, 'errorSavingArtist')
  }
}

const getPrimaryImage = (artwork: Artwork) =>
  artwork.images.find((img) => img.is_primary)?.path ?? ''

onMounted(async () => {
  await skillAdminStore.getSkills()

  if (id.value) {
    await artistAdminStore.getArtist(id.value)

    artist.value = artistAdminStore.selectedArtist
    profileImagePreview.value = artist.value?.profile_image_url ?? null
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
      profile_image_url: '',
    }
  }
})

const handleSubmit = async () => {
  if (!currentArtist.value) return

  try {
    const payload: ArtistCreatePayload | ArtistUpdatePayload = {
      user_id: currentArtist.value.user_id,
      user: currentArtist.value.user,
      minibio: currentArtist.value.minibio,
      bio: currentArtist.value.bio,
      skills: currentArtistSkills.value,
      social_links: currentArtist.value.social_links,
      profile_image: profileImageFile.value ?? undefined,
    }

    let result: Artist
    if (isEditMode.value) {
      result = await artistAdminStore.updateArtist(id.value, {
        ...payload,
        id: id.value,
      } as ArtistUpdatePayload)
    } else {
      result = await artistAdminStore.createArtist(payload as ArtistCreatePayload)

      if (result?.id) {
        router.push({ name: 'adminArtistEdit', params: { id: result.id } })
      }
    }

    emit('success', result)
    showSuccessToast(toast, t, 'artistSavedSuccessfully', 3000)
  } catch (err: unknown) {
    showErrorToast(toast, t, err, 'errorSavingArtist')
  }
}
</script>

<template>
  <TitleForm title="artist" :isCreateMode="!isEditMode" />
  <div v-if="currentArtist" class="card">
    <div v-if="isAdmin" class="mb-5">
      <Button
      :label="capitalizeFirstLetter(t('resetPassword'))"
      @click="generateResetToken"
      class="w-full md:w-auto"
      severity="warn"
      variant="outlined"
      />
      <div v-if="token" class="mt-2">
        <p>{{ capitalizeFirstLetter(t('sendToken')) }}: <span class="font-bold">{{ token }}</span></p>
      </div>
    </div>
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Profile Image Upload -->
      <div class="flex flex-wrap justify-center flex-col">
        <label class="block font-semibold mb-1 text-center">{{
          capitalizeFirstLetter(t('profileImage'))
        }}</label>
        <div v-if="profileImagePreview" class="my-4 mb-10 relative w-32 h-32 m-auto">
          <img :src="profileImagePreview" class="w-full h-full object-cover rounded-full" />
          <Button
            icon="pi pi-trash"
            outlined
            severity="danger"
            rounded
            @click="removeProfileImage"
          />
        </div>
        <div v-if="!profileImagePreview" class="">
          <FileUpload
            name="profile"
            accept="image/*"
            :maxFileSize="5000000"
            @uploader="onProfileImageSelect"
            mode="advanced"
            :auto="false"
            customUpload
            :chooseLabel="capitalizeFirstLetter(t('selectImages'))"
            :uploadLabel="capitalizeFirstLetter(t('upload'))"
            :cancelLabel="capitalizeFirstLetter(t('cancel'))"
          >
            <template #empty>
              <p>{{ capitalizeFirstLetter(t('dragDrop')) }}</p>
            </template>
          </FileUpload>
        </div>
      </div>

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

      <!-- User -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block font-semibold mb-1">Email</label>
          <InputText
            v-model="currentArtist.user.email"
            :placeholder="capitalizeFirstLetter(t('email'))"
            class="w-full"
          />
        </div>
      </div>

      <!-- Social Links -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block font-semibold mb-1">Instagram</label>
          <InputText
            v-model="currentArtist.social_links.instagram"
            placeholder="@username"
            class="w-full"
          />
        </div>
        <div>
          <label class="block font-semibold mb-1">Twitter</label>
          <InputText
            v-model="currentArtist.social_links.twitter"
            placeholder="@username"
            class="w-full"
          />
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block font-semibold mb-1">Tiktok</label>
          <InputText
            v-model="currentArtist.social_links.flickr"
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

      <template v-if="isOwnersProfile">
        <label class="block font-semibold mb-3">
          *{{ capitalizeFirstLetter(t('modifyInUserProfile')) }}
          <router-link to="/admin/user" class="underline text-(--color-salmon) cursor-pointer">
            <a href="">{{ capitalizeFirstLetter(t('profileUser')) }}</a>
          </router-link>
        </label>
      </template>

      <!-- Submit -->
      <Button
        :label="capitalizeFirstLetter(t('saveArtist'))"
        type="submit"
        class="w-full md:w-auto"
      />
    </form>

    <div class="pt-5 mt-10">
      <div class="flex justify-between mb-5">
        <label class="block font-semibold mb-1">{{ capitalizeFirstLetter(t('artworks')) }}</label>
        <RouterLink :to="`/admin/artists/${currentArtist.id}/artwork/create`">
          <Button icon="pi pi-plus" label="Add Artwork" class="w-full md:w-auto" />
        </RouterLink>
      </div>
      <div class="flex flex-wrap gap-3 justify-around">
        <div v-for="(artwork, index) in currentArtist.artworks" :key="index" class="">
          <p>{{ artwork.title }}</p>
          <Image
            :src="`${baseUrl}/` + (getPrimaryImage(artwork) || artwork.images[0]?.path)"
            :alt="artwork.title"
            width="250"
          />
          <div class="flex justify-around pt-2">
            <Button
              icon="pi pi-trash"
              severity="danger"
              rounded
              @click="openConfirmation(artwork.id)"
            />
            <Dialog
              :header="capitalizeFirstLetter(t('confirmation'))"
              v-model:visible="displayConfirmation"
              :style="{ width: '350px' }"
              :modal="true"
            >
              <div class="flex items-center justify-center">
                <i class="pi pi-exclamation-triangle mr-4" style="font-size: 2rem" />
                <span>{{ capitalizeFirstLetter(t('sureDelete')) }}</span>
              </div>
              <template #footer>
                <Button
                  :label="capitalizeFirstLetter(t('no'))"
                  icon="pi pi-times"
                  @click="closeConfirmation"
                  text
                  severity="secondary"
                />
                <Button
                  :label="capitalizeFirstLetter(t('yes'))"
                  icon="pi pi-check"
                  @click="removeArtwork(artwork.id)"
                  severity="danger"
                  outlined
                  autofocus
                />
              </template>
            </Dialog>
            <RouterLink :to="`/admin/artists/${currentArtist.id}/artwork/edit/${artwork.id}`">
              <Button icon="pi pi-pencil" rounded class="mr-2" />
            </RouterLink>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-else><LoadingComponent /></div>
</template>
