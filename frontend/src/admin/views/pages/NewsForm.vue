<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAdminNewsStore } from '@/admin/stores/NewsAdminStore'
import type { News, NewsCreatePayload, NewsUpdatePayload } from '@/shared/Interfaces/News'
import { useI18n } from 'vue-i18n'
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'primevue/usetoast'
import { showErrorToast, showSuccessToast } from '@/admin/Services/Helpers'

const emit = defineEmits<{
  (e: 'success', news: News): void
}>()

const profileImageFile = ref<File | null>(null)
const profileImagePreview = ref<string | null>(null)
const toast = useToast()
const route = useRoute()
const router = useRouter()
const { t } = useI18n()
const id = Number(route.params.id)
const newsAdminStore = useAdminNewsStore()
const isEditMode = computed(() => !!id)
const currentNews = ref<News | null>(null)
const news = ref<News | null>(null)

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
  if (id) {
    await newsAdminStore.getNewsById(id)
    
    news.value = newsAdminStore.selectedNews
    profileImagePreview.value = news.value?.image_url ? `${news.value?.image_url}`  : null
    currentNews.value = JSON.parse(JSON.stringify(news.value))

  } else {
    currentNews.value = {
      id: 0,
      title: { en: '', es: '' },
      content: { en: '', es: '' },
      image_url: '',
    }
  }
})

const handleSubmit = async () => {
  if (!currentNews.value) return

  try {
    const payload: NewsCreatePayload | NewsUpdatePayload = {
      title: currentNews.value.title,
      content: currentNews.value.content,
      cover_image: profileImageFile.value ?? undefined,
    }

    let result: News
    if (isEditMode.value) {
      result = await newsAdminStore.updateNews(id, { ...payload, id } as NewsUpdatePayload)
    } else {
      result = await newsAdminStore.createNews(payload as NewsCreatePayload)

      if (result?.id) {
        router.push({ name: 'adminNewsEdit', params: { id: result.id } })
      }
    }


    emit('success', result)
    showSuccessToast(toast, t, 'newsSavedSuccessfully', 3000)
  } catch (err: unknown) {
    showErrorToast(toast, t, err, 'errorSavingNews')
  }
}
</script>

<template>
  <div v-if="currentNews">
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Profile Image Upload -->
      <div class="flex flex-wrap justify-center flex-col">
        <label class="block font-semibold mb-1 text-center">{{
          capitalizeFirstLetter(t('profileImage'))
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
          >
            <template #empty>
              <p>{{ capitalizeFirstLetter(t('dragDrop')) }}</p>
            </template>
          </FileUpload>
        </div>
      </div>

      <!-- Title -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('title'))} ${capitalizeFirstLetter(t('english'))}`
          }}</label>
          <Textarea v-model="currentNews.title.en" rows="2" class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('title'))} ${capitalizeFirstLetter(t('spanish'))}`
          }}</label>
          <Textarea v-model="currentNews.title.es" rows="2" class="w-full" />
        </div>
      </div>

      <!-- Content -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('content'))} ${capitalizeFirstLetter(t('english'))}`
          }}</label>
          <Textarea v-model="currentNews.content.en" rows="2" class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('content'))} ${capitalizeFirstLetter(t('spanish'))}`
          }}</label>
          <Textarea v-model="currentNews.content.es" rows="2" class="w-full" />
        </div>
      </div>

      <!-- Submit -->
      <Button
        :label="capitalizeFirstLetter(t('saveNews'))"
        type="submit"
        class="w-full md:w-auto"
      />
    </form>
  </div>
  <div v-else><LoadingComponent /></div>
</template>
