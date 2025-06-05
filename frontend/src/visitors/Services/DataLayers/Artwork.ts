import type { Artwork, ArtworkCreatePayload, ArtworkUpdatePayload } from '@/shared/Interfaces/Artwork'
import type { ApiResponse } from '@/shared/Interfaces/ApiResponse'
import { BaseService } from "@/shared/services/DataLayers/BaseService"
import axiosInstance from '@/shared/services/DataLayers/AxiosInstance'

class ArtworkService extends BaseService {
  constructor() {
    super('/artworks')
  }

  async getArtworks(params = {}): Promise<ApiResponse<Artwork[]>> {
    return this.getPaginated<ApiResponse<Artwork[]>>(params)
  }
}

export default new ArtworkService()