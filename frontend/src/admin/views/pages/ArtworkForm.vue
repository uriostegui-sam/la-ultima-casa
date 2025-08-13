<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import type {
  Artwork,
  ArtworkCreatePayload,
  ArtworkUpdatePayload,
} from '@/shared/Interfaces/Artwork'
import { useRoute, useRouter } from 'vue-router'
import { useAdminArtistStore } from '@/admin/stores/ArtistAdminStore'
import { useAdminArtworkStore } from '@/admin/stores/ArtworkAdminStore'
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import { useI18n } from 'vue-i18n'
import { useToast } from 'primevue/usetoast'
import { showErrorToast, showSuccessToast } from '@/admin/Services/Helpers'
import ArtworkAdminServices from '@/admin/Services/DataLayers/ArtworkAdminServices'
import LoadingComponent from '@/shared/components/LoadingComponent.vue'
import type FileUpload from 'primevue/fileupload'
import TitleForm from '@/admin/components/TitleForm.vue'

interface ExtendedFileUpload extends InstanceType<typeof FileUpload> {
  clear: () => void
}

const emit = defineEmits<{
  (e: 'success', artwork: Artwork): void
}>()

const baseUrl = import.meta.env.VITE_STORAGE_URL
const { t } = useI18n()
const route = useRoute()
const router = useRouter()
const id = computed(() => Number(route.params.idArtwork))
const toast = useToast()
const artistAdminStore = useAdminArtistStore()
const artworkAdminStore = useAdminArtworkStore()
const isEditMode = computed(() => !Number.isNaN(id.value))
const currentArtwork = ref<Artwork | null>(null)
const artists = computed(() => artistAdminStore.artists)
const imageToDelete = ref<number | string | null>(null)
const displayConfirmation = ref(false)
const artwork = ref<Artwork | null>(null)
const uploader = ref<ExtendedFileUpload | null>(null)

const newImages = ref<File[]>([])
const imagesToDelete = ref<number[]>([])
const isUploading = ref(false)

const openConfirmation = (imageId: number | string) => {
  imageToDelete.value = imageId
  displayConfirmation.value = true
}

function closeConfirmation() {
  displayConfirmation.value = false
  imageToDelete.value = null
}

const allImages = computed(() => {
  const existing =
    currentArtwork.value?.images?.filter((img) => !imagesToDelete.value.includes(img.id)) || []
  const newPreviews = newImages.value.map((file, index) => ({
    id: `new-${index}`,
    path: URL.createObjectURL(file),
    is_primary: false,
    isNew: true,
    file,
  }))

  return [...existing, ...newPreviews]
})

const onImageSelect = (event: { files: File[] }) => {
  newImages.value = [...newImages.value, ...event.files]
}

const removeImage = (imageId: string | number) => {
  if (typeof imageId === 'string' && imageId.startsWith('new-')) {
    // Remove new image
    const index = parseInt(imageId.split('-')[1])
    newImages.value.splice(index, 1)
  } else if (typeof imageId === 'number') {
    // Mark existing image for deletion
    if (!imagesToDelete.value.includes(imageId)) {
      imagesToDelete.value.push(imageId)
    }
  }
}

const setPrimaryImage = async (imageId: number) => {
  if (!currentArtwork.value) return

  try {
    const updatedArtwork = await ArtworkAdminServices.setPrimaryImage(
      currentArtwork.value.id,
      imageId,
    )

    if (currentArtwork.value.images) {
      currentArtwork.value.images = currentArtwork.value.images.map((img) => ({
        ...img,
        is_primary: img.id === imageId,
      }))
    }

    showSuccessToast(toast, t, 'artworks.primaryImageUpdated', 3000)
  } catch (err) {
    showErrorToast(toast, t, err, 'artworks.errorUpdatingPrimaryImage')
  }
}

const confirmDeleteImage = async () => {
  if (!imageToDelete.value || typeof imageToDelete.value !== 'number' || !currentArtwork.value)
    return

  try {
    await ArtworkAdminServices.deleteImage(currentArtwork.value.id, imageToDelete.value)

    if (currentArtwork.value.images) {
      currentArtwork.value.images = currentArtwork.value.images.filter(
        (img) => img.id !== imageToDelete.value,
      )
    }

    showSuccessToast(toast, t, 'artworks.imageDeletedSuccessfully', 3000)
  } catch (err) {
    showErrorToast(toast, t, err, 'artworks.errorDeletingImage')
  } finally {
    closeConfirmation()
  }
}

onMounted(async () => {
  await artistAdminStore.getArtists()

  if (id.value) {
    await artworkAdminStore.getArtwork(id.value)

    artwork.value = artworkAdminStore.selectedArtwork
    currentArtwork.value = JSON.parse(JSON.stringify(artwork.value))
  } else {
    currentArtwork.value = {
      id: 0,
      artist_id: route.params.id ? Number(route.params.id) : 0,
      title: '',
      artist: null,
      description: {
        en: '',
        es: '',
      },
      dimensions: { width: 0, height: 0, depth: 0 },
      creation_date: new Date(),
      images: [],
      primary_image: undefined,
    }
  }
})

// Form submission
const handleSubmit = async () => {
  if (!currentArtwork.value) return

  try {
    const basePayload = {
      artist_id: currentArtwork.value.artist_id,
      title: currentArtwork.value.title,
      description: currentArtwork.value.description,
      dimensions: currentArtwork.value.dimensions,
      creation_date: currentArtwork.value.creation_date,
    }

    let result: Artwork
    if (isEditMode.value) {
      const updatePayload: ArtworkUpdatePayload = {
        ...basePayload,
        id: id.value,
        images_to_delete: imagesToDelete.value,
        new_images: newImages.value,
      }
      result = await artworkAdminStore.updateArtwork(id.value, updatePayload)

      artwork.value = result
      currentArtwork.value = JSON.parse(JSON.stringify(result))
    } else {
      const createPayload: ArtworkCreatePayload = {
        ...basePayload,
        new_images: newImages.value,
      }

      result = await artworkAdminStore.createArtwork(createPayload)

      if (result?.id) {
        router.push({ name: 'adminArtistArtworkEdit', params: { idArtwork: result.id, id: result.artist_id } })
      }
    }
    
    uploader.value?.clear()
    newImages.value = []
    imagesToDelete.value = []

    emit('success', result)
    showSuccessToast(toast, t, 'artworks.artworkSavedSuccessfully', 3000)
  } catch (err: unknown) {
    showErrorToast(toast, t, err, 'artworks.errorSavingArtwork')
  }
}
</script>

<template>
  <TitleForm title="artworks.artwork" :isCreateMode="!isEditMode" :goBack="true"/>
  <div v-if="currentArtwork" class="card">
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Basic Info -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Title -->
        <div>
          <label class="block mb-2 font-medium">{{ capitalizeFirstLetter(t('commun.title')) }}</label>
          <InputText v-model="currentArtwork.title" required class="w-full" />
        </div>

        <!-- Artist Selection -->
        <div>
          <label class="block mb-2 font-medium">{{ capitalizeFirstLetter(t('artists.artist')) }}</label>
          <Dropdown
            v-model="currentArtwork.artist_id"
            :options="artists"
            optionLabel="name"
            optionValue="id"
            class="w-full"
            required
          />
        </div>
      </div>

      <!-- Descriptions -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block mb-2 font-medium">{{
            capitalizeFirstLetter(t('artworks.descriptionEn'))
          }}</label>
          <Editor v-model="currentArtwork.description.en" rows="5" class="w-full" />
        </div>
        <div>
          <label class="block mb-2 font-medium">{{
            capitalizeFirstLetter(t('artworks.descriptionSp'))
          }}</label>
          <Editor v-model="currentArtwork.description.es" rows="5" class="w-full" />
        </div>
      </div>

      <!-- Dimensions -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block mb-2 font-medium">{{ capitalizeFirstLetter(t('artworks.width')) }}</label>
          <InputNumber v-model="currentArtwork.dimensions.width" class="w-full" />
        </div>
        <div>
          <label class="block mb-2 font-medium">{{ capitalizeFirstLetter(t('artworks.height')) }}</label>
          <InputNumber v-model="currentArtwork.dimensions.height" class="w-full" />
        </div>
        <div>
          <label class="block mb-2 font-medium">{{ capitalizeFirstLetter(t('artworks.depth')) }}</label>
          <InputNumber v-model="currentArtwork.dimensions.depth" class="w-full" />
        </div>
      </div>

      <!-- Creation Date -->
      <div>
        <label class="block mb-2 font-medium">{{ capitalizeFirstLetter(t('artworks.creationDate')) }}</label>
        <Calendar
          v-model="currentArtwork.creation_date"
          dateFormat="yy-mm-dd"
          class="w-full"
          :showIcon="true"
        />
      </div>

      <!-- Image Upload -->
      <div>
        <label class="block mb-2 font-medium">{{ capitalizeFirstLetter(t('artworks.images')) }}</label>
        <FileUpload
          name="images[]"
          multiple
          accept="image/*"
          :maxFileSize="10000000"
          @select="onImageSelect"
          mode="advanced"
          ref="uploader"
          :auto="false"
          :chooseLabel="capitalizeFirstLetter(t('commun.selectImages'))"
          :uploadLabel="capitalizeFirstLetter(t('commun.upload'))"
          :cancelLabel="capitalizeFirstLetter(t('commun.cancel'))"
        >
          <template #empty>
            <p>{{ capitalizeFirstLetter(t('artworks.dragDrop')) }}</p>
          </template>
        </FileUpload>

        <!-- Image Preview Grid -->
        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
          <div
            v-for="(image, index) in allImages"
            :key="image.id"
            class="relative group rounded-lg overflow-hidden border"
            :class="{ 'ring-2 ring-primary-500': image.is_primary }"
          >
            <img
              :src="`${baseUrl}/${image.path}`"
              class="w-full h-40 object-cover"
              :alt="`Artwork image ${index + 1}`"
            />

            <!-- Image actions overlay -->
            <div
              class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100"
            >
              <!-- Set as primary button -->
              <Button
                icon="pi pi-star"
                class="p-2 w-10 h-10 mr-2"
                :class="[image.is_primary ? 'bg-yellow-500' : 'bg-gray-700']"
                :disabled="image.is_primary"
                @click.stop="typeof image.id === 'number' ? setPrimaryImage(image.id) : null"
                v-tooltip.top="
                  image.is_primary
                    ? capitalizeFirstLetter(t('artworks.primaryImage'))
                    : capitalizeFirstLetter(t('artworks.setPrimary'))
                "
              />

              <!-- Delete button -->
              <Button
                icon="pi pi-trash"
                class="p-2 w-10 h-10 bg-red-500"
                @click.stop="
                  typeof image.id === 'number' ? openConfirmation(image.id) : removeImage(image.id)
                "
                v-tooltip.top="capitalizeFirstLetter(t('divers.deleteImage'))"
              />
            </div>

            <!-- Primary badge -->
            <span
              v-if="image.is_primary"
              class="absolute top-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded"
            >
              {{ capitalizeFirstLetter(t('artworks.primaryImage')) }}
            </span>
          </div>
        </div>
      </div>

      <!-- Submit Button -->
      <Button
        type="submit"
        :label="capitalizeFirstLetter(t('artworks.saveArtwork'))"
        class="w-full md:w-auto"
        :loading="isUploading"
        :disabled="isUploading"
      />
    </form>
    <Dialog
      v-model:visible="displayConfirmation"
      :style="{ width: '450px' }"
      :header="capitalizeFirstLetter(t('commun.confirm'))"
      :modal="true"
    >
      <div class="confirmation-content">
        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
        <span>{{ capitalizeFirstLetter(t('artists.sureDelete')) }}</span>
      </div>
      <template #footer>
        <Button
          :label="capitalizeFirstLetter(t('commun.no'))"
          icon="pi pi-times"
          @click="closeConfirmation"
          class="p-button-text"
        />
        <Button
          :label="capitalizeFirstLetter(t('commun.yes'))"
          icon="pi pi-check"
          @click="confirmDeleteImage"
          class="p-button-danger"
          autofocus
        />
      </template>
    </Dialog>
  </div>
  <div v-else><LoadingComponent /></div>
</template>
