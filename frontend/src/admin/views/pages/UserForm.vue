<script setup lang="ts">
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import LoadingComponent from '@/shared/components/LoadingComponent.vue'
import { onMounted, ref } from 'vue'
import type { Artist } from '@/shared/Interfaces/Artist'
import { useI18n } from 'vue-i18n'
import TitleForm from '@/admin/components/TitleForm.vue'
import { useAuthStore } from '@/shared/stores/AuthStore'
import { useAdminArtistStore } from '@/admin/stores/ArtistAdminStore'
import type { UserPasswordUpdate, UserPasswordUpdatePayload } from '@/shared/Interfaces/User'
import { showErrorToast, showSuccessToast } from '@/admin/Services/Helpers'
import { useToast } from 'primevue/usetoast'
import AuthService from '@/shared/services/DataLayers/AuthService'

const emit = defineEmits<{
  (e: 'success', user: UserPasswordUpdate): void
}>()

const { t } = useI18n()
const currentArtist = ref<Artist | null>(null)
const authStore = useAuthStore()
const artistId = authStore.user?.artist?.id
const currentUser = ref<UserPasswordUpdate>({
    id: artistId ?? 0,
    password: '',
    newPassword: '',
    newPassword_confirmation: ''
  })
const artistAdminStore = useAdminArtistStore()
const toast = useToast()

onMounted(async () => {
  if (artistId) {
    await artistAdminStore.getArtist(artistId)
    currentArtist.value = artistAdminStore.selectedArtist
    currentUser.value.id = artistId
  }
})

const handleSubmit = async () => {
  if (!currentArtist.value || !currentUser?.value) return

  try {
    const payload: UserPasswordUpdatePayload = {
        id: authStore.user?.id as number,
        password: currentUser.value.password,
        newPassword: currentUser.value.newPassword,
        newPassword_confirmation: currentUser.value.newPassword_confirmation
    }

    let result: UserPasswordUpdate

    result = await AuthService.updatePassword(payload)

    currentUser.value = {
      id: currentArtist.value.user_id,
      password: '',
      newPassword: '',
      newPassword_confirmation: ''
    }

    emit('success', result)
    showSuccessToast(toast, t, 'authentication.successPassword', 3000)
  } catch (err: unknown) {
    showErrorToast(toast, t, err, 'authentication.errorSavingPassword')
  }
}

</script>

<template>
  <TitleForm title="authentication.user" />
  <div v-if="currentArtist" class="card">
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Name -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{ capitalizeFirstLetter(t('artists.name')) }}</label>
          <InputText
            v-model="currentArtist.user.name"
            :disabled="true"
            :placeholder="`${capitalizeFirstLetter(t('artists.artistName'))}`"
            class="w-full"
          />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{ capitalizeFirstLetter(t('artists.lastName')) }}</label>
          <InputText
            v-model="currentArtist.user.lastname"
            :disabled="true"
            :placeholder="`${capitalizeFirstLetter(t('artists.artistLastName'))}`"
            class="w-full"
          />
        </div>
      </div>

      <!-- User -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block font-semibold mb-1">{{ capitalizeFirstLetter(t('authentication.email')) }}</label>
          <InputText
            v-model="currentArtist.user.email"
            disabled="true"
            :placeholder="capitalizeFirstLetter(t('authentication.email'))"
            class="w-full"
          />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{
            capitalizeFirstLetter(t('authentication.actualPassword'))
          }}</label>
          <Password
            v-model="currentUser.password"
            :placeholder="capitalizeFirstLetter(t('authentication.writeActualPassword'))"
            class="w-full"
            :style="{ width: '100%' }"
            :inputStyle="{ width: '100%' }"
            :weakLabel="capitalizeFirstLetter(t('authentication.weakLabel'))"
            :mediumLabel="capitalizeFirstLetter(t('authentication.mediumLabel'))"
            :strongLabel="capitalizeFirstLetter(t('authentication.strongLabel'))"
            :feedback="false"
            :toggleMask="true" 
          />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block font-semibold mb-1">{{
            capitalizeFirstLetter(t('authentication.newPassword'))
          }}</label>
          <Password
            v-model="currentUser.newPassword"
            :placeholder="capitalizeFirstLetter(t('authentication.writeNewPassword'))"
            class="w-full"
            :style="{ width: '100%' }"
            :inputStyle="{ width: '100%' }"
            :weakLabel="capitalizeFirstLetter(t('authentication.weakLabel'))"
            :mediumLabel="capitalizeFirstLetter(t('authentication.mediumLabel'))"
            :strongLabel="capitalizeFirstLetter(t('authentication.strongLabel'))"
            :toggleMask="true" 
          />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{
            capitalizeFirstLetter(t('authentication.newPasswordAgain'))
          }}</label>
          <Password
            v-model="currentUser.newPassword_confirmation"
            :placeholder="capitalizeFirstLetter(t('authentication.writeNewPasswordAgain'))"
            class="w-full"
            :style="{ width: '100%' }"
            :inputStyle="{ width: '100%' }"
            :toggleMask="true" 
          />
        </div>
      </div>

      <label class="block font-semibold mb-3">
        *{{ capitalizeFirstLetter(t('artists.modifyInArtistProfile')) }}
        <router-link
          :to="`/admin/artists/edit/${artistId}`"
          class="underline text-(--color-salmon) cursor-pointer"
        >
          <a href="">{{ capitalizeFirstLetter(t('artists.profileArtist')) }}</a>
        </router-link>
      </label>

      <!-- Submit -->
      <Button
        :label="capitalizeFirstLetter(t('authentication.saveUser'))"
        type="submit"
        class="w-full md:w-auto"
      />
    </form>
  </div>
  <div v-else>
    <LoadingComponent />
  </div>
</template>
