<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAdminSkillStore } from '@/admin/stores/SkillAdminStore'
import type { Skill, SkillCreatePayload, SkillUpdatePayload } from '@/shared/Interfaces/Skill'
import { useI18n } from 'vue-i18n'
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'primevue/usetoast'
import { showErrorToast, showSuccessToast } from '@/admin/Services/Helpers'
import LoadingComponent from '@/shared/components/LoadingComponent.vue'
import TitleForm from '@/admin/components/TitleForm.vue'

const emit = defineEmits<{
  (e: 'success', skill: Skill): void
}>()

const baseUrl = import.meta.env.VITE_STORAGE_URL
const profileImageFile = ref<File | null>(null)
const profileImagePreview = ref<string | null>(null)
const toast = useToast()
const route = useRoute()
const router = useRouter()
const { t } = useI18n()
const id = computed(() => Number(route.params.id))
const skillAdminStore = useAdminSkillStore()
const isEditMode = computed(() => !Number.isNaN(id.value))
const currentSkill = ref<Skill | null>(null)
const skill = ref<Skill | null>(null)

const selectButtonValue = computed({
  get() {
    return currentSkill.value?.published ?? false
  },
  set(value: boolean) {
    if (currentSkill.value) {
      currentSkill.value.published = value
    }
  },
})

const selectButtonValues = computed(() => [
  { name: capitalizeFirstLetter(t('news.published')), value: true },
  { name: capitalizeFirstLetter(t('news.hide')), value: false },
])

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

onMounted(async () => {
  if (id.value) {
    await skillAdminStore.getSkill(id.value)

    skill.value = skillAdminStore.selectedSkill
    profileImagePreview.value = skill.value?.profile_image ? `${baseUrl}/${skill.value?.profile_image}`  : null
    currentSkill.value = JSON.parse(JSON.stringify(skill.value))

  } else {
    currentSkill.value = {
      id: 0,
      name: { en: '', es: '' },
      profile_image: undefined,
      published: false,
    }
  }
})

const handleSubmit = async () => {
  if (!currentSkill.value) return

  try {
    const payload: SkillCreatePayload | SkillUpdatePayload = {
      name: currentSkill.value.name,
      profile_image: profileImageFile.value ?? undefined,
      published: currentSkill.value.published,
    }

    let result: Skill
    if (isEditMode.value) {
      result = await skillAdminStore.updateSkill(id.value, { ...payload, id: id.value } as SkillUpdatePayload)
    } else {
      result = await skillAdminStore.createSkill(payload as SkillCreatePayload)

      if (result?.id) {
        router.push({ name: 'adminSkillEdit', params: { id: result.id } })
      }
    }

    emit('success', result)
    showSuccessToast(toast, t, 'skills.skillSavedSuccessfully', 3000)
  } catch (err: unknown) {
    showErrorToast(toast, t, err, 'skills.errorSavingSkill')
  }
}
</script>

<template>
  <TitleForm title="skills.skills" :isCreateMode="!isEditMode" :goBack="true" />
  <div v-if="currentSkill" class="card">
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Profile Image Upload -->
      <div class="flex flex-wrap justify-center flex-col">
        <label class="block font-semibold mb-1 text-center">{{
          capitalizeFirstLetter(t('artists.referenceImage'))
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

      <!-- Published -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{ `${capitalizeFirstLetter(t('commun.type'))}` }}</label>
          <SelectButton
            v-model="selectButtonValue"
            :options="selectButtonValues"
            optionLabel="name"
            optionValue="value"
          />
        </div>
       </div>
      
      <!-- Name -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('artists.name'))} ${capitalizeFirstLetter(t('navigation.english'))}`
          }}</label>
          <Textarea v-model="currentSkill.name.en" rows="2" class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('artists.name'))} ${capitalizeFirstLetter(t('navigation.spanish'))}`
          }}</label>
          <Textarea v-model="currentSkill.name.es" rows="2" class="w-full" />
        </div>
      </div>

      <!-- Submit -->
      <Button
        :label="capitalizeFirstLetter(t('skills.saveSkill'))"
        type="submit"
        class="w-full md:w-auto"
      />
    </form>
  </div>
  <div v-else><LoadingComponent /></div>
</template>
