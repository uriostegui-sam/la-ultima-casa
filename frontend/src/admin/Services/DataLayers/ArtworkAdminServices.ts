import type { Artwork, ArtworkCreatePayload, ArtworkUpdatePayload } from '@/shared/Interfaces/Artwork'
import type { ApiResponse } from '@/shared/Interfaces/ApiResponse'
import { BaseService } from "@/shared/services/DataLayers/BaseService"
import axiosInstance from '@/shared/services/DataLayers/AxiosInstance'

class ArtworkService extends BaseService {
  constructor() {
    super('/artworks')
  }

  async createArtwork(payload: ArtworkCreatePayload): Promise<Artwork> {
    const formData = new FormData()
    
    // Append JSON fields
    formData.append('title', payload.title)
    formData.append('artist_id', payload.artist_id.toString())
    if (payload.description) {
      formData.append('description', JSON.stringify(payload.description))
    }
    if (payload.dimensions) {
      formData.append('dimensions', JSON.stringify(payload.dimensions))
    }
    if (payload.creation_date) {
      formData.append('creation_date', payload.creation_date)
    }
    
    // Append images
    payload.images.forEach(file => {
      formData.append('images[]', file)
    })

    const response = await axiosInstance.post<Artwork>(this.baseUrl, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    return response.data
  }

  async updateArtwork(
    id: number,
    payload: ArtworkUpdatePayload
  ): Promise<Artwork> {
    const formData = new FormData()
    
    // Append fields if they exist
    if (payload.title) formData.append('title', payload.title)
    if (payload.description) {
      formData.append('description', JSON.stringify(payload.description))
    }
    if (payload.dimensions) {
      formData.append('dimensions', JSON.stringify(payload.dimensions))
    }
    if (payload.creation_date) {
      formData.append('creation_date', payload.creation_date)
    }
    
    // Append new images
    if (payload.new_images) {
      payload.new_images.forEach(file => {
        formData.append('images[]', file)
      })
    }

    const response = await axiosInstance.post<Artwork>(
      `${this.baseUrl}/${id}?_method=PUT`,
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }
    )
    return response.data
  }

  async deleteImage(artworkId: number, imageId: number): Promise<void> {
    await axiosInstance.delete(
      `${this.baseUrl}/${artworkId}/images/${imageId}`
    )
  }

  async setPrimaryImage(artworkId: number, imageId: number): Promise<Artwork> {
    const response = await axiosInstance.patch<Artwork>(
      `${this.baseUrl}/${artworkId}/images/${imageId}/set-primary`
    )
    return response.data
  }

  async reorderImages(
    artworkId: number,
    imageIds: number[]
  ): Promise<Artwork> {
    const response = await axiosInstance.patch<Artwork>(
      `${this.baseUrl}/${artworkId}/reorder-images`,
      { image_ids: imageIds }
    )
    return response.data
  }

  async getArtworks(params = {}): Promise<ApiResponse<Artwork[]>> {
    return this.getPaginated<ApiResponse<Artwork[]>>(params)
  }
}

export default new ArtworkService()