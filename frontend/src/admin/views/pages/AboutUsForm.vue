<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAdminAboutUsStore } from '@/admin/stores/AboutUsAdminStore'
import type {
  AboutUs,
  AboutUsCreatePayload,
  AboutUsUpdatePayload,
} from '@/shared/Interfaces/AboutUs'
import { useI18n } from 'vue-i18n'
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import { useToast } from 'primevue/usetoast'
import { showErrorToast, showSuccessToast } from '@/admin/Services/Helpers'
import LoadingComponent from '@/shared/components/LoadingComponent.vue'
import TitleForm from '@/admin/components/TitleForm.vue'

const emit = defineEmits<{
  (e: 'success', aboutUs: AboutUs): void
}>()

const existingId = computed(() => {
  return Number(aboutUsAdminStore.aboutUs.length > 0 ? aboutUsAdminStore.aboutUs[0].id : null)
})

const baseUrl = import.meta.env.VITE_STORAGE_URL
const profileImageFile = ref<File | null>(null)
const profileImagePreview = ref<string | null>(null)
const logoFile = ref<File | null>(null)
const logoPreview = ref<string | null>(null)
const toast = useToast()
const { t } = useI18n()
const aboutUsAdminStore = useAdminAboutUsStore()
const isEditMode = computed(() => !!existingId.value)
const currentAboutUs = ref<AboutUs | null>(null)
const aboutUs = ref<AboutUs | null>(null)

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

const onLogoSelect = (event: any) => {
  const file = event.files?.[0]
  if (file) {
    logoFile.value = file
    logoPreview.value = URL.createObjectURL(file)
  }
}

const removeLogo = () => {
  logoFile.value = null
  logoPreview.value = null
}

onMounted(async () => {
  await aboutUsAdminStore.getAboutUs()

  if (existingId.value) {
    await aboutUsAdminStore.getAboutUsById(existingId.value)

    aboutUs.value = aboutUsAdminStore.selectedAboutUs

    profileImagePreview.value = aboutUs.value?.cover_image
      ? `${baseUrl}/${aboutUs.value?.cover_image}`
      : null

    logoPreview.value = aboutUs.value?.logo
      ? `${baseUrl}/${aboutUs.value?.logo}`
      : null

    currentAboutUs.value = JSON.parse(JSON.stringify(aboutUs.value))
  } else {
    currentAboutUs.value = {
      id: 0,
      address: { text: '', map: '' },
      description: { es: '', en: '' },
      cover_image: '',
      logo: '',
      mail: '',
      number: '',
    }
  }
})

const handleSubmit = async () => {
  if (!currentAboutUs.value) return

  try {
    const payload: AboutUsCreatePayload | AboutUsUpdatePayload = {
      address: currentAboutUs.value.address,
      description: currentAboutUs.value.description,
      cover_image: profileImageFile.value ?? undefined,
      logo: logoFile.value ?? undefined,
      mail: currentAboutUs.value.mail,
      number: currentAboutUs.value.number,
    }

    let result: AboutUs
    if (isEditMode.value && existingId.value) {
      const updatePayload: AboutUsUpdatePayload = {
        ...payload,
        id: existingId.value,
      }
      result = await aboutUsAdminStore.updateAboutUs(existingId.value, updatePayload)
    } else {
      result = await aboutUsAdminStore.createAboutUs(payload as AboutUsCreatePayload)
    }

    emit('success', result)
    showSuccessToast(toast, t, 'divers.aboutUsSavedSuccessfully', 3000)
  } catch (err: unknown) {
    showErrorToast(toast, t, err, 'divers.errorSavingAboutUs')
  }
}
</script>

<template>
  <TitleForm title="navigation.aboutUs" :isCreateMode="!existingId" />
  <div v-if="currentAboutUs" class="card">
    <form @submit.prevent="handleSubmit" class="space-y-6">
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Logo Upload -->
        <div class="flex flex-wrap justify-center flex-col">
          <label class="block font-semibold mb-1 text-center">{{
            capitalizeFirstLetter(t('commun.logo'))
          }}</label>
          <div v-if="profileImagePreview" class="my-4 mb-10 relative w-32 h-32 m-auto">
            <img :src="`${logoPreview}`" class="w-full h-full object-cover rounded-full" />
            <Button
              icon="pi pi-trash"
              outlined
              severity="danger"
              rounded
              @click="removeLogo"
            />
          </div>
          <div v-if="!logoPreview" class="">
            <FileUpload
              name="logo"
              accept="image/*"
              :maxFileSize="5000000"
              @uploader="onLogoSelect"
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

        <!-- Profile Image Upload -->
        <div class="flex flex-wrap justify-center flex-col">
          <label class="block font-semibold mb-1 text-center">{{
            capitalizeFirstLetter(t('commun.coverImage'))
          }}</label>
          <div v-if="profileImagePreview" class="my-4 mb-10 relative w-32 h-32 m-auto">
            <img :src="`${profileImagePreview}`" class="w-full h-full object-cover rounded-full" />
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
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block font-semibold mb-1">{{capitalizeFirstLetter(t('authentication.email'))}}</label>
          <InputText
            v-model="currentAboutUs.mail"
            :placeholder="capitalizeFirstLetter(t('authentication.email'))"
            class="w-full"
          />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{
            capitalizeFirstLetter(t('divers.phoneNumber'))
          }}</label>
          <InputMask
            v-model="currentAboutUs.number"
            placeholder="044 312 000 00 00"
            mask="999 999 999 9999"
            class="w-full"
          />
        </div>
      </div>

      <!-- Address -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('divers.address'))}`
          }}</label>
          <Editor v-model="currentAboutUs.address.text" class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('divers.address'))} ${capitalizeFirstLetter(t('divers.map'))}`
          }}</label>
          <Textarea v-model="currentAboutUs.address.map" rows="2" class="w-full" />
        </div>
      </div>

      <!-- Description -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('artworks.descriptionSp'))}`
          }}</label>
          <Editor v-model="currentAboutUs.description.es" class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('artworks.descriptionEn'))}`
          }}</label>
          <Editor v-model="currentAboutUs.description.en" class="w-full" />
        </div>
      </div>

      <!-- Submit -->
      <Button
        :label="capitalizeFirstLetter(t('divers.saveAboutUs'))"
        type="submit"
        class="w-full md:w-auto"
      />
    </form>
  </div>
  <div v-else><LoadingComponent /></div>
</template>
