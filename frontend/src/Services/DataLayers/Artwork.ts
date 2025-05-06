import type { Artwork } from '@/Interfaces/Artwork'
import axiosInstance from './AxiosInstance'
import { BaseService } from './BaseService'
import type { ApiResponse } from '@/Interfaces/ApiResponse'

class ArtworkService extends BaseService {
  constructor() {
    super('/artworks')
  }

  async uploadArtwork(data: FormData): Promise<Artwork> {
    const response = await axiosInstance.post<Artwork>(this.baseUrl, data, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    return response.data
  }

  async getArtworks(params = {}): Promise<ApiResponse<Artwork[]>> {
    return this.getPaginated<ApiResponse<Artwork[]>>(params)
  }
}

export default new ArtworkService()