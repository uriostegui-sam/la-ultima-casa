<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAdminArtistStore } from '@/admin/stores/ArtistAdminStore'
import { useAdminSkillStore } from '@/admin/stores/SkillAdminStore'
import type { Artist, ArtistCreatePayload, ArtistUpdatePayload } from '@/shared/Interfaces/Artist'
import { locale } from '@/shared/services/Translation'
import { useI18n } from 'vue-i18n'
import { capitalizeFirstLetter, choseCurrentLanguage } from '@/shared/services/Helpers'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'primevue/usetoast'
import { showErrorToast, showSuccessToast } from '@/admin/Services/Helpers'
import TitleForm from '@/admin/components/TitleForm.vue'
import LoadingComponent from '@/shared/components/LoadingComponent.vue'
import { useAuthStore } from '@/shared/stores/AuthStore'
import AuthService from '@/shared/services/DataLayers/AuthService'
import type { PasswordReset } from '@/shared/Interfaces/User'
import ArtworkDragDrop from '@/admin/components/ArtworkDragDrop.vue'

const emit = defineEmits<{
  (e: 'success', artist: Artist): void
  (e: 'success', passwordReset: PasswordReset): void
}>()

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
const skillAdminStore = useAdminSkillStore()
const isEditMode = computed(() => !Number.isNaN(id.value))
const currentArtist = ref<Artist | null>(null)
const currentArtistSkills = ref<number[]>([])
const artist = ref<Artist | null>(null)
const isOwnersProfile = computed(() => id.value === artistId)
const token = ref<string | null>(null)
const skillOptions = computed(() =>
  skillAdminStore.skills.map((skill) => ({
    label: choseCurrentLanguage(skill.name, currentLang.value),
    value: skill.id,
  })),
)

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

const generateResetToken = async () => {
  if (!currentArtist.value) return
  if (!authStore.isAdmin) {
    showErrorToast(toast, t, 'divers.notAuthorized', 'divers.errorNotAuthorized')
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
    showSuccessToast(toast, t, 'authentication.resetPasswordSuccess', 3000)
  } catch (err: unknown) {
    showErrorToast(toast, t, err, 'authentication.resetPasswordError')
  }
}

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
    showSuccessToast(toast, t, 'artists.artistSavedSuccessfully', 3000)
  } catch (err: unknown) {
    showErrorToast(toast, t, err, 'artists.errorSavingArtist')
  }
}
</script>

<template>
  <TitleForm title="artists.artist" :isCreateMode="!isEditMode" :goBack="true" />
  <div v-if="currentArtist" class="card">
    <div v-if="isAdmin && isEditMode" class="mb-5">
      <Button
      :label="capitalizeFirstLetter(t('authentication.resetPassword'))"
      @click="generateResetToken"
      class="w-full md:w-auto"
      severity="warn"
      variant="outlined"
      />
      <div v-if="token" class="mt-2">
        <p>{{ capitalizeFirstLetter(t('authentication.sendToken')) }}: <span class="font-bold">{{ token }}</span></p>
      </div>
    </div>
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Profile Image Upload -->
      <div class="flex flex-wrap justify-center flex-col">
        <label class="block font-semibold mb-1 text-center">{{
          capitalizeFirstLetter(t('artists.profileImage'))
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
            :chooseLabel="capitalizeFirstLetter(t('commun.selectImages'))"
            :uploadLabel="capitalizeFirstLetter(t('commun.upload'))"
            :cancelLabel="capitalizeFirstLetter(t('commun.cancel'))"
          >
            <template #empty>
              <p>{{ capitalizeFirstLetter(t('artworks.dragDrop')) }}</p>
            </template>
          </FileUpload>
        </div>
      </div>

      <!-- Name -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{ capitalizeFirstLetter(t('artists.name')) }}</label>
          <InputText
            v-model="currentArtist.user.name"
            :placeholder="`${capitalizeFirstLetter(t('artists.artistName'))}`"
            class="w-full"
          />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{ capitalizeFirstLetter(t('artists.lastName')) }}</label>
          <InputText
            v-model="currentArtist.user.lastname"
            :placeholder="`${capitalizeFirstLetter(t('artists.artistLastName'))}`"
            class="w-full"
          />
        </div>
      </div>

      <!-- MiniBio (EN & ES) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('artists.minibio'))} ${capitalizeFirstLetter(t('navigation.english'))}`
          }}</label>
          <Editor v-model="currentArtist.minibio.en" class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('artists.minibio'))} ${capitalizeFirstLetter(t('navigation.spanish'))}`
          }}</label>
          <Editor v-model="currentArtist.minibio.es" class="w-full" />
        </div>
      </div>

      <!-- Bio (EN & ES) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('artists.biography'))} ${capitalizeFirstLetter(t('navigation.english'))}`
          }}</label>
          <Editor v-model="currentArtist.bio.en" class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('artists.biography'))} ${capitalizeFirstLetter(t('navigation.spanish'))}`
          }}</label>
          <Editor v-model="currentArtist.bio.es" class="w-full" />
        </div>
      </div>

      <!-- Skills -->
      <div>
        <label class="block font-semibold mb-1">{{
          capitalizeFirstLetter(t('artists.selectSkills'))
        }}</label>
        <MultiSelect
          v-model="currentArtistSkills"
          :options="skillOptions"
          :placeholder="capitalizeFirstLetter(t('artists.selectSkills'))"
          class="w-full"
          option-label="label"
          option-value="value"
        />
      </div>

      <!-- User -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block font-semibold mb-1">{{ capitalizeFirstLetter(t('authentication.email')) }}</label>
          <InputText
            v-model="currentArtist.user.email"
            :placeholder="capitalizeFirstLetter(t('authentication.email'))"
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
          <label class="block font-semibold mb-1">{{ capitalizeFirstLetter(t('divers.website')) }}</label>
          <InputText
            v-model="currentArtist.social_links.website"
            placeholder="http://www.website.com"
            class="w-full"
          />
        </div>
      </div>

      <template v-if="isOwnersProfile">
        <label class="block font-semibold mb-3">
          *{{ capitalizeFirstLetter(t('authentication.modifyInUserProfile')) }}
          <router-link to="/admin/user" class="underline text-(--color-salmon) cursor-pointer">
            <a href="">{{ capitalizeFirstLetter(t('divers.profileUser')) }}</a>
          </router-link>
        </label>
      </template>

      <!-- Submit -->
      <Button
        :label="capitalizeFirstLetter(t('artists.saveArtist'))"
        type="submit"
        class="w-full md:w-auto"
      />
    </form>

    <div v-if="isEditMode" class="pt-5 mt-10">
      <ArtworkDragDrop
        :currentArtist="currentArtist"
        @success="handleSubmit"
      />
    </div>
  </div>
  <div v-else><LoadingComponent /></div>
</template>
