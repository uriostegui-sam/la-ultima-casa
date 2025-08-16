import type { ArtworkOrderPayload } from '@/shared/Interfaces/Artwork'

export function buildArtworkOrderFormData(payload: ArtworkOrderPayload): FormData {
  const formData = new FormData()
  payload.artworks.forEach((artwork, index) => {
    formData.append(`artworks[${index}][id]`, artwork.id.toString())
    formData.append(`artworks[${index}][order]`, artwork.order.toString())
  })
  
  formData.append('_method', 'PUT')

  return formData
}
