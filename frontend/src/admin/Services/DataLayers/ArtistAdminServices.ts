import type { ApiResponse } from "@/shared/Interfaces/ApiResponse"
import type { Artist, ArtistCreatePayload, ArtistUpdatePayload } from "@/shared/Interfaces/Artist"
import axiosInstance from "@/shared/services/DataLayers/AxiosInstance"
import { BaseService } from "@/shared/services/DataLayers/BaseService"
import axios from "axios"

class ArtistService extends BaseService {
  constructor() {
    super('/artists')
  }

  async createArtist(payload: ArtistCreatePayload): Promise<Artist> {
    const formData = new FormData()
    
    // Required fields
    formData.append('user_id', payload.user_id.toString())
    formData.append('minibio', JSON.stringify(payload.minibio))
    formData.append('bio', JSON.stringify(payload.bio))
    formData.append('social_links', JSON.stringify(payload.social_links))

    // Optional fields
    if (payload.profile_image) {
      formData.append('profile_image', payload.profile_image)
    }
    if (payload.skills) {
      payload.skills.forEach(id => formData.append('skills[]', id.toString()))
    }

    const response = await axiosInstance.post<Artist>(this.baseUrl, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    return response.data
  }

  async updateArtist(id: number, payload: ArtistUpdatePayload): Promise<Artist> {
    const formData = new FormData()
    
    // Append only provided fields
    if (payload.name) formData.append('name', payload.name) 
    if (payload.user_id) formData.append('user_id', payload.user_id.toString())
    if (payload.minibio) formData.append('minibio', JSON.stringify(payload.minibio))
    if (payload.bio) formData.append('bio', JSON.stringify(payload.bio))
    if (payload.social_links) {
      formData.append('social_links', JSON.stringify(payload.social_links))
    }
    if (payload.profile_image) {
      formData.append('profile_image', payload.profile_image)
    }
    if (payload.skills) {
      payload.skills.forEach(id => formData.append('skills[]', id.toString()))
    }
    
    formData.append('_method', 'PUT')

    const response = await axiosInstance.post<Artist>(
      `${this.baseUrl}/${id}`,
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }
    )
    return response.data
  }

  async deleteArtist(id: number): Promise<void> {
    try {
      await axiosInstance.delete(`${this.baseUrl}/${id}`)
    } catch (error) {
      if (axios.isAxiosError(error) && error.response?.status === 422) {
        throw new Error('Cannot delete artist with existing artworks')
      }
      throw error
    }
  }

  async getArtists(params = {}): Promise<ApiResponse<Artist[]>> {
    return this.getPaginated<ApiResponse<Artist[]>>(params)
  }
}

export default new ArtistService()