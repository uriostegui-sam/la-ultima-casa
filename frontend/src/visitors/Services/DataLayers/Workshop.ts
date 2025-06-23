import type { ApiResponse } from '@/shared/Interfaces/ApiResponse'
import axiosInstance from '@/shared/services/DataLayers/AxiosInstance'
import { BaseService } from '@/shared/services/DataLayers/BaseService'
import type {
  Workshop,
} from '@/shared/Interfaces/Workshop'

class WorkshopService extends BaseService {
  constructor() {
    super('/workshops')
  }

  async getWorkshops(params = {}): Promise<ApiResponse<Workshop[]>> {
    return this.getPaginated<ApiResponse<Workshop[]>>(params)
  }
  
  async getFeaturedWorkshops(): Promise<Workshop[]> {
    const response = await axiosInstance.get<ApiResponse<Workshop[]>>(`${this.baseUrl}/featured`)
    return response.data.data
  }
}

export default new WorkshopService()
