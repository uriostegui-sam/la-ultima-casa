<script setup lang="ts">
import { ref, computed, watchEffect, onMounted, watch } from 'vue'
import { useAdminWorkshopStore } from '@/admin/stores/WorkshopAdminStore'
import { useAdminSkillStore } from '@/admin/stores/SkillAdminStore'
import { Languages, locale } from '@/shared/services/Translation'
import { useI18n } from 'vue-i18n'
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'primevue/usetoast'
import { showErrorToast, showSuccessToast } from '@/admin/Services/Helpers'
import type {
  Workshop,
  WorkshopCreatePayload,
  WorkshopUpdatePayload,
} from '@/shared/Interfaces/Workshop'
import { useAdminArtistStore } from '@/admin/stores/ArtistAdminStore'
import LoadingComponent from '@/shared/components/LoadingComponent.vue'
import TitleForm from '@/admin/components/TitleForm.vue'
import { useAuthStore } from '@/shared/stores/AuthStore'

const emit = defineEmits<{
  (e: 'success', workshop: Workshop): void
}>()

const baseUrl = import.meta.env.VITE_STORAGE_URL
const profileImageFile = ref<File | null>(null)
const profileImagePreview = ref<string | null>(null)
const toast = useToast()
const route = useRoute()
const router = useRouter()
const { t } = useI18n()
const id = computed(() => Number(route.params.id))
const currentLang = locale
const workshopAdminStore = useAdminWorkshopStore()
const skillAdminStore = useAdminSkillStore()
const artistAdminStore = useAdminArtistStore()
const authStore = useAuthStore()
const isAdmin = ref(false)
const artists = computed(() => artistAdminStore.artists)
const isEditMode = computed(() => !Number.isNaN(id.value))
const currentWorkshop = ref<Workshop | null>(null)
const currentWorkshopSkills = ref<number[]>([])
const workshop = ref<Workshop | null>(null)

const selectButtonValues = computed(() => [
  { name: capitalizeFirstLetter(t('workshop.permanent')), value: 'permanent' },
  { name: capitalizeFirstLetter(t('workshop.temporary')), value: 'temporary' },
])

const skillOptions = computed(() =>
  skillAdminStore.skills.map((skill) => ({
    label: currentLang.value === Languages.English ? skill.name.en : skill.name.es,
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

const selectButtonValue = computed({
  get() {
    return currentWorkshop.value?.type || 'temporary'
  },
  set(value: string) {
    if (currentWorkshop.value) {
      currentWorkshop.value.type = value as Workshop['type']
      currentWorkshop.value.end_date =
        value === 'permanent' ? null : (new Date().toISOString().split('T')[0] as unknown as Date)
    }
  },
})

const isFeatured = ref(false)
const featuredPosition = ref<number | false>(false)

watch(isFeatured, (val) => {
  if (!val) {
    featuredPosition.value = false
  }
})

const isFeaturedButtonValues = computed(() => [
  { name: capitalizeFirstLetter(t('commun.yes')), value: true },
  { name: capitalizeFirstLetter(t('commun.no')), value: false },
])


onMounted(async () => {
  isAdmin.value = authStore.user?.role === 'admin' ? true : false
  await artistAdminStore.getArtists()
  await skillAdminStore.getSkills()

  if (id.value) {
    await workshopAdminStore.getWorkshop(id.value)

    workshop.value = workshopAdminStore.selectedWorkshop
    isFeatured.value = typeof workshop.value?.featured_position === 'number'
    featuredPosition.value = workshop.value?.featured_position ?? false
    profileImagePreview.value = workshop.value?.cover_image_path
      ? `${baseUrl}/${workshop.value?.cover_image_path}`
      : null
    currentWorkshop.value = JSON.parse(JSON.stringify(workshop.value))

    currentWorkshopSkills.value = workshop.value?.skills
      ? workshop.value.skills.map((skill) => skill.id)
      : []
  } else {
    currentWorkshop.value = {
      id: 0,
      artist_id: 0,
      artist: undefined,
      title: { en: '', es: '' },
      description: { en: '', es: '' },
      type: 'temporary',
      start_date: new Date(),
      end_date: new Date(),
      price: 0,
      cover_image_path: '',
      skills: [],
      max_students: 0,
      featured_position: false
    }
  }
})

const handleSubmit = async () => {
  if (!currentWorkshop.value) return

  try {
    const basePayload = {
      artist_id: currentWorkshop.value.artist_id,
      title: currentWorkshop.value.title,
      description: currentWorkshop.value.description,
      type: currentWorkshop.value.type,
      start_date: currentWorkshop.value.start_date,
      end_date: currentWorkshop.value.end_date,
      price: currentWorkshop.value.price,
      max_students: currentWorkshop.value.max_students,
      skills: currentWorkshopSkills.value,
      cover_image: profileImageFile.value ?? undefined,
      featured_position: featuredPosition.value,
    }

    let result: Workshop
    if (isEditMode.value) {
      const updatePayload: WorkshopUpdatePayload = {
        ...basePayload,
        id: currentWorkshop.value.id,
      }
      result = await workshopAdminStore.updateWorkshop(id.value, updatePayload)
      workshop.value = result
      currentWorkshop.value = JSON.parse(JSON.stringify(result))
    } else {
      const createPayload: WorkshopCreatePayload = {
        ...basePayload,
      }
      result = await workshopAdminStore.createWorkshop(createPayload)

      if (result?.id) {
        router.push({ name: 'adminWorkshopEdit', params: { id: result.id } })
      }
    }

    emit('success', result)
    showSuccessToast(toast, t, 'workshop.workshopSavedSuccessfully', 3000)
  } catch (err: unknown) {
    showErrorToast(toast, t, err, 'workshop.errorSavingWorkshop')
  }
}
</script>

<template>
  <TitleForm title="workshop.workshop" :isCreateMode="!isEditMode" :goBack="true" />
  <div v-if="currentWorkshop" class="card">
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Profile Image Upload -->
      <div class="flex flex-wrap justify-center flex-col">
        <label class="block font-semibold mb-1 text-center">{{
          capitalizeFirstLetter(t('commun.coverImage'))
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

      <div v-if="isAdmin"
        class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{ `${capitalizeFirstLetter(t('workshop.inFrontPage'))}` }}</label>
          <SelectButton
            v-model="isFeatured"
            :options="isFeaturedButtonValues"
            optionLabel="name"
            optionValue="value"
          />
        </div>

        <div v-if="isFeatured" class="mt-2">
          <label class="block font-semibold mb-1">{{ `${capitalizeFirstLetter(t('workshop.positionInFrontPage'))}` }}</label>
          <Select
            v-model="featuredPosition"
            :options="[1, 2]"
            class="w-full"
          />
        </div>
      </div>

      <!-- Title -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('commun.title'))} ${capitalizeFirstLetter(t('navigation.english'))}`
          }}</label>
          <InputText
            v-model="currentWorkshop.title.en"
            :placeholder="`${capitalizeFirstLetter(t('workshop.workshopName'))} ${capitalizeFirstLetter(t('navigation.english'))}`"
            class="w-full"
          />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('commun.title'))} ${capitalizeFirstLetter(t('navigation.spanish'))}`
          }}</label>
          <InputText
            v-model="currentWorkshop.title.es"
            :placeholder="`${capitalizeFirstLetter(t('workshop.workshopName'))} ${capitalizeFirstLetter(t('navigation.spanish'))}`"
            class="w-full"
          />
        </div>
      </div>

      <!-- Description (EN & ES) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('commun.description'))} ${capitalizeFirstLetter(t('navigation.english'))}`
          }}</label>
          <Textarea v-model="currentWorkshop.description.en" rows="2" class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('commun.description'))} ${capitalizeFirstLetter(t('navigation.spanish'))}`
          }}</label>
          <Textarea v-model="currentWorkshop.description.es" rows="2" class="w-full" />
        </div>
      </div>

      <!-- Type -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('commun.type'))}`
          }}</label>
          <SelectButton
            v-model="selectButtonValue"
            :options="selectButtonValues"
            optionLabel="name"
            optionValue="value"
          />
        </div>
        <!-- Artist Selection -->
        <div>
          <label class="block mb-2 font-medium">{{ capitalizeFirstLetter(t('artists.artist')) }}</label>
          <Select
            v-model="currentWorkshop.artist_id"
            :options="artists"
            optionLabel="name"
            optionValue="id"
            class="w-full"
            required
          />
        </div>
      </div>

      <!-- Dates -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('workshop.startDate'))}`
          }}</label>
          <Calendar
            v-model="currentWorkshop.start_date"
            dateFormat="yy-mm-dd"
            class="w-full"
            :showIcon="true"
          />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('workshop.endDate'))}`
          }}</label>
          <Calendar
            v-model="currentWorkshop.end_date"
            dateFormat="yy-mm-dd"
            class="w-full"
            :showIcon="true"
          />
        </div>
      </div>

      <!-- Skills -->
      <div>
        <label class="block font-semibold mb-1">{{
          capitalizeFirstLetter(t('skills.selectSkillsWorkshop'))
        }}</label>
        <MultiSelect
          v-model="currentWorkshopSkills"
          :options="skillOptions"
          :placeholder="capitalizeFirstLetter(t('skills.selectSkillsWorkshop'))"
          class="w-full"
          option-label="label"
          option-value="value"
        />
      </div>

      <!-- Details -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block font-semibold mb-1">{{ capitalizeFirstLetter(t('workshop.price')) }}</label>
          <InputNumber
            v-model="currentWorkshop.price"
            :placeholder="capitalizeFirstLetter(t('workshop.price'))"
            class="w-full"
          />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{
            capitalizeFirstLetter(t('workshop.maxStudents'))
          }}</label>
          <InputNumber
            v-model="currentWorkshop.max_students"
            :placeholder="capitalizeFirstLetter(t('workshop.maxStudents'))"
            class="w-full"
          />
        </div>
      </div>

      <!-- Submit -->
      <Button
        :label="capitalizeFirstLetter(t('workshop.saveWorkshop'))"
        type="submit"
        class="w-full md:w-auto"
      />
    </form>
  </div>
  <div v-else><LoadingComponent /></div>
</template>
