<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAdminNewsStore } from '@/admin/stores/NewsAdminStore'
import type { News, NewsCreatePayload, NewsUpdatePayload } from '@/shared/Interfaces/News'
import { useI18n } from 'vue-i18n'
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'primevue/usetoast'
import { showErrorToast, showSuccessToast } from '@/admin/Services/Helpers'
import LoadingComponent from '@/shared/components/LoadingComponent.vue'
import TitleForm from '@/admin/components/TitleForm.vue'

const emit = defineEmits<{
  (e: 'success', news: News): void
}>()

const profileImageFile = ref<File | null>(null)
const profileImagePreview = ref<string | null>(null)
const toast = useToast()
const route = useRoute()
const router = useRouter()
const { t } = useI18n()
const id = computed(() => Number(route.params.id))
const newsAdminStore = useAdminNewsStore()
const isEditMode = computed(() => !Number.isNaN(id.value))
const currentNews = ref<News | null>(null)
const news = ref<News | null>(null)

const selectButtonValue = computed({
  get() {
    return currentNews.value?.published ?? false
  },
  set(value: boolean) {
    if (currentNews.value) {
      currentNews.value.published = value
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
    await newsAdminStore.getNewsById(id.value)
    
    news.value = newsAdminStore.selectedNews
    profileImagePreview.value = news.value?.image_url ? `${news.value?.image_url}`  : null
    currentNews.value = JSON.parse(JSON.stringify(news.value))

  } else {
    currentNews.value = {
      id: 0,
      title: { en: '', es: '' },
      content: { en: '', es: '' },
      image_url: '',
      published: false,
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
      published: currentNews.value.published,
    }

    let result: News
    if (isEditMode.value) {
      result = await newsAdminStore.updateNews(id.value, { ...payload, id: id.value } as NewsUpdatePayload)
    } else {
      result = await newsAdminStore.createNews(payload as NewsCreatePayload)

      if (result?.id) {
        router.push({ name: 'adminNewsEdit', params: { id: result.id } })
      }
    }


    emit('success', result)
    showSuccessToast(toast, t, 'news.newsSavedSuccessfully', 3000)
  } catch (err: unknown) {
    showErrorToast(toast, t, err, 'news.errorSavingNews')
  }
}
</script>

<template>
  <TitleForm title="news.news" :isCreateMode="!isEditMode" :goBack="true" />
  <div v-if="currentNews">
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
      <!-- Title -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('commun.title'))} ${capitalizeFirstLetter(t('navigation.english'))}`
          }}</label>
          <InputText v-model="currentNews.title.en" class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('commun.title'))} ${capitalizeFirstLetter(t('navigation.spanish'))}`
          }}</label>
          <InputText v-model="currentNews.title.es" class="w-full" />
        </div>
      </div>

      <!-- Content -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('commun.content'))} ${capitalizeFirstLetter(t('navigation.english'))}`
          }}</label>
          <Editor v-model="currentNews.content.en" class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">{{
            `${capitalizeFirstLetter(t('commun.content'))} ${capitalizeFirstLetter(t('navigation.spanish'))}`
          }}</label>
          <Editor v-model="currentNews.content.es" class="w-full" />
        </div>
      </div>

      <!-- Submit -->
      <Button
        :label="capitalizeFirstLetter(t('news.saveNews'))"
        type="submit"
        class="w-full md:w-auto"
      />
    </form>
  </div>
  <div v-else><LoadingComponent /></div>
</template>
