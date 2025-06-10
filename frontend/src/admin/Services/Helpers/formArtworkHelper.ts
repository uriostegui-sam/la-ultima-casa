import type { ArtworkCreatePayload, ArtworkUpdatePayload } from '@/shared/Interfaces/Artwork'

export function buildArtworkFormData(
  payload: ArtworkCreatePayload | ArtworkUpdatePayload,
): FormData {
  const formData = new FormData()

  // User fields
  if (payload.title) {
    formData.append('title', payload.title)
  }
  if (payload.artist_id) {
    formData.append('artist_id', payload.artist_id.toString())
  }
  if (payload.description?.en) {
    formData.append('description[en]', payload.description.en)
  }
  if (payload.description?.en) {
    formData.append('description[es]', payload.description.es)
  }
  if (payload.dimensions?.width) {
    formData.append('dimensions[width]', payload.dimensions.width.toString())
  }
  if (payload.dimensions?.height) {
    formData.append('dimensions[height]', payload.dimensions.height.toString())
  }
  if (payload.dimensions?.depth) {
    formData.append('dimensions[depth]', payload.dimensions.depth.toString())
  }
  if (payload.creation_date) {
    formData.append('creation_date', new Date(payload.creation_date).toISOString())
  }

  // Handle new images
  if ('new_images' in payload && payload.new_images) {
    payload.new_images.forEach((file) => {
      formData.append(`images[]`, file);
    });
  }

  // Handle images to delete (for updates)
  if ('images_to_delete' in payload && payload.images_to_delete) {
    formData.append('images_to_delete', JSON.stringify(payload.images_to_delete));
  }

  formData.append('_method', 'PUT')

  return formData
}
