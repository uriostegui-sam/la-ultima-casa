import type { Artist, ArtistCreatePayload, ArtistUpdatePayload } from '@/Interfaces/Artist'
import axiosInstance from './AxiosInstance'
import { BaseService } from './BaseService'
import type { ApiResponse } from '@/Interfaces/ApiResponse'

class ArtistService extends BaseService {
  constructor() {
    super('/artists')
  }

  async createArtist(data: ArtistCreatePayload
  ): Promise<Artist> {
    const formData = new FormData()
    Object.entries(data).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        if (key === 'skills') {
          (value as number[]).forEach(id => formData.append('skills[]', String(id)))
        } else if (key === 'profile_image' && value instanceof File) {
          formData.append('profile_image', value)
        } else {
          formData.append(key, typeof value === 'object' ? JSON.stringify(value) : String(value))
        }
      }
    })

    const response = await axiosInstance.post<Artist>('/artists', formData)
    return response.data
  }

  async updateArtist(id: number, data: ArtistUpdatePayload): Promise<Artist> {
    const formData = new FormData()
    Object.entries(data).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        if (key === 'skills') {
          (value as number[]).forEach(id => formData.append('skills[]', String(id)))
        } else if (key === 'profile_image' && value instanceof File) {
          formData.append('profile_image', value)
        } else {
          formData.append(key, typeof value === 'object' ? JSON.stringify(value) : String(value))
        }
      }
    })

    const response = await axiosInstance.post<Artist>(`/artists/${id}?_method=PUT`, formData)
    return response.data
  }

  async getArtists(params = {}): Promise<ApiResponse<Artist[]>> {
    return this.getPaginated<ApiResponse<Artist[]>>(params)
  }
}

export default new ArtistService()