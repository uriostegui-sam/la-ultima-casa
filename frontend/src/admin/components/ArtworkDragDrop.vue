<script lang="ts" setup>
import type { Artwork } from '@/shared/Interfaces/Artwork'
import type { ArtworkImage } from '@/shared/Interfaces/ArtworkImage'
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import { useI18n } from 'vue-i18n'
import { showErrorToast, showSuccessToast } from '../Services/Helpers'
import { useToast } from 'primevue/usetoast'
import { computed, ref } from 'vue'
import { useAdminArtworkStore } from '../stores/ArtworkAdminStore'
import type { Artist } from '@/shared/Interfaces/Artist'

const props = defineProps<{
  currentArtist: Artist
}>()
const emit = defineEmits<{
  (e: 'success', result: { success: boolean }): void
}>()
const { t } = useI18n()
const baseUrl = import.meta.env.VITE_STORAGE_URL
const toast = useToast()
const displayConfirmation = ref(false)
const artworkToDelete = ref<number | string | null>(null)
const artworkAdminStore = useAdminArtworkStore()
const draggedIndex = ref<number | null>(null)

const artworks = ref<Artwork[]>(
  [...(props.currentArtist.artworks || [])].sort((a, b) => (a.order ?? 0) - (b.order ?? 0)),
)

const getPrimaryImage = (artwork: Artwork) =>
  artwork.images.find((img) => img.is_primary)?.path ?? ''

const openConfirmation = (id: number | string) => {
  artworkToDelete.value = id
  displayConfirmation.value = true
}

const closeConfirmation = () => {
  displayConfirmation.value = false
  artworkToDelete.value = null
}

const removeArtwork = async (id: string | number) => {
  if (!props.currentArtist) return

  displayConfirmation.value = false
  try {
    const result = await artworkAdminStore.deleteArtwork(id)

    props.currentArtist.artworks =
      props.currentArtist.artworks?.filter((art) => art.id !== id) || []

    emit('success', result)
    showSuccessToast(toast, t, 'artworks.artworkDeletedSuccessfully', 3000)
  } catch (err: unknown) {
    showErrorToast(toast, t, err, 'artworks.errorDeletingArtworks')
  }
}

const startDrag = (evt: DragEvent, index: number) => {
  if (!evt.dataTransfer) return

  draggedIndex.value = index

  evt.dataTransfer.effectAllowed = 'move'
}

const onDrop = (dropIndex: number) => {
  if (draggedIndex.value === null) return

  const [movedArtwork] = artworks.value.splice(draggedIndex.value, 1)
  artworks.value.splice(dropIndex, 0, movedArtwork)

  artworks.value.forEach((artwork, index) => {
    artwork.order = index + 1
  })

  draggedIndex.value = null
}
</script>

<template>
  <div class="flex justify-between mb-5">
    <label class="block font-semibold mb-1">{{
      capitalizeFirstLetter(t('artworks.artworks'))
    }}</label>
    <RouterLink :to="`/admin/artists/${currentArtist.id}/artwork/create`">
      <Button
        icon="pi pi-plus"
        :label="capitalizeFirstLetter(t('artworks.addArtwork'))"
        class="w-full md:w-auto"
      />
    </RouterLink>
  </div>
  <div class="flex flex-wrap gap-3 justify-around drop-zone">
    <div
      v-for="(artwork, index) in artworks"
      :key="index"
      class="drag-el"
      draggable="true"
      @dragstart="startDrag($event, index)"
      @drop.prevent="onDrop(index)"
      @dragover.prevent
    >
      <p>{{ artwork.title }} {{ artwork.order }}</p>
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
          :header="capitalizeFirstLetter(t('commun.confirmation'))"
          v-model:visible="displayConfirmation"
          :style="{ width: '350px' }"
          :modal="true"
        >
          <div class="flex items-center justify-center">
            <i class="pi pi-exclamation-triangle mr-4" style="font-size: 2rem" />
            <span>{{ capitalizeFirstLetter(t('artists.sureDelete')) }}</span>
          </div>
          <template #footer>
            <Button
              :label="capitalizeFirstLetter(t('commun.no'))"
              icon="pi pi-times"
              @click="closeConfirmation"
              text
              severity="secondary"
            />
            <Button
              :label="capitalizeFirstLetter(t('commun.yes'))"
              icon="pi pi-check"
              @click="removeArtwork(artwork.id)"
              severity="danger"
              outlined
              autofocus
            />
          </template>
        </Dialog>
        <RouterLink :to="`/admin/artists/${props.currentArtist.id}/artwork/edit/${artwork.id}`">
          <Button icon="pi pi-pencil" rounded class="mr-2" />
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
