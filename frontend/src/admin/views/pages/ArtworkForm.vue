<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import type { Artwork } from '@/shared/Interfaces/Artwork'
import { useAdminArtistStore } from '../../stores/ArtistAdminStore'
import { useAdminArtworkStore } from '../../stores/ArtworkAdminStore'
import { useRoute } from 'vue-router'

const props = defineProps<{
  artwork?: Artwork | null
}>()

const route = useRoute()
const emit = defineEmits(['success'])
const currentArtwork = ref<Artwork | null>(null)
const artistAdminStore = useAdminArtistStore()
const artworkAdminStore = useAdminArtworkStore()

const loading = ref(false)
const form = ref({
  id: props.artwork?.id || null,
  title: props.artwork?.title || '',
  artist_id: props.artwork?.artist_id || null,
  description: {
    en: props.artwork?.description?.en || '',
    es: props.artwork?.description?.es || ''
  },
  dimensions: {
    width: props.artwork?.dimensions?.width || null,
    height: props.artwork?.dimensions?.height || null,
    depth: props.artwork?.dimensions?.depth || null
  },
  creation_date: props.artwork?.creation_date ? new Date(props.artwork.creation_date) : null,
  images: [] as File[],
  existingImages: props.artwork?.images || [],
  imagesToDelete: [] as number[]
})

// Image handling
const imagePreviews = computed(() => {
  const previews = form.value.existingImages
    .filter(img => !form.value.imagesToDelete.includes(img.id))
    .map(img => ({
      id: img.id,
      url: `/storage/${img.path}`,
      isPrimary: img.is_primary
    }))

  form.value.images.forEach(file => {
    previews.push({
      id: null,
      url: URL.createObjectURL(file),
      isPrimary: previews.length === 0 // Auto-set first as primary
    })
  })

  return previews
})

const onImageSelect = (event: { files: File[] }) => {
  form.value.images = [...form.value.images, ...event.files]
}

const removeImage = (index: number) => {
  const preview = imagePreviews.value[index]
  if (preview.id) {
    // Mark existing image for deletion
    form.value.imagesToDelete.push(preview.id)
  } else {
    // Remove new image
    const newImageIndex = index - form.value.existingImages.length
    form.value.images.splice(newImageIndex, 1)
  }
}

const setPrimaryImage = (index: number) => {
  imagePreviews.value.forEach((img, i) => {
    img.isPrimary = i === index
  })
}

// Form submission
const handleSubmit = async () => {
  if (!form.value.artist_id) {
    alert('Artist is required')
    return
  }

  loading.value = true

  try {
    const formData = new FormData()
    formData.append('title', form.value.title)
    formData.append('artist_id', form.value.artist_id.toString())
    formData.append('description', JSON.stringify(form.value.description))

    if (form.value.dimensions.width && form.value.dimensions.height) {
      formData.append('dimensions', JSON.stringify(form.value.dimensions))
    }

    if (form.value.creation_date) {
      formData.append('creation_date', form.value.creation_date.toISOString().split('T')[0])
    }

    // Append new images
    form.value.images.forEach(file => {
      formData.append('images[]', file)
    })

    // Append images to delete
    form.value.imagesToDelete.forEach(id => {
      formData.append('delete_images[]', id.toString())
    })

    // Set primary image
    const primaryIndex = imagePreviews.value.findIndex(img => img.isPrimary)
    if (primaryIndex >= 0) {
      const primaryId = imagePreviews.value[primaryIndex].id
      if (primaryId) {
        formData.append('primary_image_id', primaryId.toString())
      }
    }

    if (form.value.id) {
      await artworkAdminStore.updateArtwork(form.value.id, formData)
    } else {
      await artworkAdminStore.createArtwork(formData)
    }

    emit('success')
  } finally {
    loading.value = false
  }
}

// Load artists and artwork
onMounted(async () => {
  if (!artistAdminStore.artists.length) {
    await artistAdminStore.getArtists()
  }
  
  if (route.params.id) {
    const id = Number(route.params.id)
    await artworkAdminStore.getArtwork(id)
    currentArtwork.value = artworkAdminStore.selectedArtwork
    
    if (currentArtwork.value) {
      form.value = {
        ...form.value,
        id: currentArtwork.value.id,
        title: currentArtwork.value.title,
        artist_id: currentArtwork.value.artist_id,
        description: currentArtwork.value.description,
        dimensions: currentArtwork.value.dimensions,
        creation_date: currentArtwork.value.creation_date ? new Date(currentArtwork.value.creation_date) : null,
        existingImages: currentArtwork.value.images || []
      }
    }
  }
})

const artists = computed(() => artistAdminStore.artists)
</script>

<template>
  <div v-if="currentArtwork || !route.params.id">
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Basic Info -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Title -->
        <div>
          <label class="block mb-2 font-medium">Title*</label>
          <InputText v-model="form.title" required class="w-full" />
        </div>

        <!-- Artist Selection -->
        <div>
          <label class="block mb-2 font-medium">Artist*</label>
          <Dropdown
            v-model="form.artist_id"
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
          <label class="block mb-2 font-medium">Description (English)</label>
          <Textarea v-model="form.description.en" rows="5" class="w-full" />
        </div>
        <div>
          <label class="block mb-2 font-medium">Description (Spanish)</label>
          <Textarea v-model="form.description.es" rows="5" class="w-full" />
        </div>
      </div>

      <!-- Dimensions -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block mb-2 font-medium">Width (cm)</label>
          <InputNumber v-model="form.dimensions.width" class="w-full" />
        </div>
        <div>
          <label class="block mb-2 font-medium">Height (cm)</label>
          <InputNumber v-model="form.dimensions.height" class="w-full" />
        </div>
        <div>
          <label class="block mb-2 font-medium">Depth (cm)</label>
          <InputNumber v-model="form.dimensions.depth" class="w-full" />
        </div>
      </div>

      <!-- Creation Date -->
      <div>
        <label class="block mb-2 font-medium">Creation Date</label>
        <Calendar 
          v-model="form.creation_date" 
          dateFormat="yy-mm-dd" 
          class="w-full" 
          :showIcon="true"
        />
      </div>

      <!-- Image Upload -->
      <div>
        <label class="block mb-2 font-medium">Images</label>
        <FileUpload 
          name="images[]"
          multiple
          accept="image/*"
          :maxFileSize="10000000"
          @select="onImageSelect"
        >
          <template #empty>
            <p>Drag & drop images here or click to browse</p>
          </template>
        </FileUpload>

        <!-- Image Preview -->
        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
          <div 
            v-for="(image, index) in imagePreviews" 
            :key="index"
            class="relative group"
          >
            <img 
              :src="image.url" 
              class="w-full h-32 object-cover rounded"
            />
            <Button 
              icon="pi pi-times" 
              class="absolute top-2 right-2 p-1 w-8 h-8 !bg-red-500 !text-white opacity-0 group-hover:opacity-100"
              @click="removeImage(index)"
            />
            <Button
              icon="pi pi-star"
              class="absolute top-2 left-2 p-1 w-8 h-8"
              :class="[image.isPrimary ? '!bg-yellow-500' : '!bg-gray-500']"
              @click="setPrimaryImage(index)"
            />
          </div>
        </div>
      </div>

      <!-- Submit -->
      <Button 
        type="submit" 
        label="Save Artwork" 
        :loading="loading" 
        class="w-full md:w-auto"
      />
    </form>
  </div>
  <div v-else>Loading...</div>
</template>