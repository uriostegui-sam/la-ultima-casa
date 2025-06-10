import type { ApiResponse } from "@/shared/Interfaces/ApiResponse"
import type { Artist, ArtistCreatePayload, ArtistUpdatePayload } from "@/shared/Interfaces/Artist"
import axiosInstance from "@/shared/services/DataLayers/AxiosInstance"
import { BaseService } from "@/shared/services/DataLayers/BaseService"
import axios from "axios"
import { buildArtistFormData } from "../Helpers/formArtistHelper"

class ArtistAdminService extends BaseService {
  constructor() {
    super('/artists')
  }

  async createArtist(payload: ArtistCreatePayload): Promise<Artist> {
    const formData = buildArtistFormData(payload);

    return await this.create<Artist>(formData)
  }

  async updateArtist(id: number, payload: ArtistUpdatePayload): Promise<Artist> {
    const formData = buildArtistFormData(payload);
    
    return await this.update<Artist>(id, formData)
  }

  async deleteArtist(id: number): Promise<void> {
    try {
      await axiosInstance.delete(`${this.baseUrl}/${id}`)
    } catch (error) {
      if (axios.isAxiosError(error) && error.response?.status === 422) {
        throw new Error('cannotArtistWArtwork')
      }
      throw error
    }
  }

  async getArtists(params = {}): Promise<ApiResponse<Artist[]>> {
    return this.getPaginated<ApiResponse<Artist[]>>(params)
  }
}

export default new ArtistAdminService()