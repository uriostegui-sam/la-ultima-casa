import type { Artwork, ArtworkCreatePayload, ArtworkOrderPayload, ArtworkUpdatePayload } from '@/shared/Interfaces/Artwork'
import type { ApiResponse } from '@/shared/Interfaces/ApiResponse'
import { BaseService } from "@/shared/services/DataLayers/BaseService"
import axiosInstance from '@/shared/services/DataLayers/AxiosInstance'
import { buildArtworkFormData } from '../Helpers/formArtworkHelper'
import { buildArtworkOrderFormData } from '../Helpers/formArtworkOrderHelper'

class ArtworkAdminService extends BaseService {
  constructor() {
    super('/artworks')
  }

  async createArtwork(payload: ArtworkCreatePayload): Promise<Artwork> {
    const formData = buildArtworkFormData(payload, true);
    
    return await this.create<Artwork>(formData)
  }

  async updateArtwork(id: number, payload: ArtworkUpdatePayload): Promise<Artwork> {
   const formData = buildArtworkFormData(payload, false);

   return await this.update<Artwork>(id, formData);
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

  async updateArtworksOrder(payload: ArtworkOrderPayload): Promise<Artwork[]> {
    const formData = buildArtworkOrderFormData(payload)
    console.log(formData)

    const response = await axiosInstance.post<Artwork[]>(
      `${this.baseUrl}/reorder-artwork`,
      formData
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

export default new ArtworkAdminService()