import type { ApiResponse } from "@/shared/Interfaces/ApiResponse"
import type { Artist } from "@/shared/Interfaces/Artist"
import { BaseService } from "@/shared/services/DataLayers/BaseService"

class ArtistService extends BaseService {
  constructor() {
    super('/artists')
  }

  async getArtists(params = {}): Promise<ApiResponse<Artist[]>> {
    return this.getPaginated<ApiResponse<Artist[]>>(params)
  }
}

export default new ArtistService()