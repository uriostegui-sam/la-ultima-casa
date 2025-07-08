import type { ApiResponse } from '@/shared/Interfaces/ApiResponse'
import axiosInstance from '@/shared/services/DataLayers/AxiosInstance'
import { BaseService } from '@/shared/services/DataLayers/BaseService'
import type { Workshop, WorkshopCreatePayload, WorkshopUpdatePayload } from '@/shared/Interfaces/Workshop'
import axios from 'axios'
import { buildWorkshopFormData } from '../Helpers/formWorkshopHelper'

class WorkshopAdminService extends BaseService {
  constructor() {
    super('/workshops')
  }

  async createWorkshop(payload: WorkshopCreatePayload): Promise<Workshop> {
    const formData = buildWorkshopFormData(payload, true);
    return await this.create<Workshop>(formData)
  }

  async updateWorkshop(id: number, payload: WorkshopUpdatePayload): Promise<Workshop> {
    const formData = buildWorkshopFormData(payload, false);
    return await this.update<Workshop>(id, formData)
  }

  async deleteWorkshop(id: number): Promise<void> {
    try {
      await axiosInstance.delete(`${this.baseUrl}/${id}`)
    } catch (error) {
      if (axios.isAxiosError(error) && error.response?.status === 422) {
        throw new Error(error.response.data.message || 'Cannot delete workshop')
      }
      throw error
    }
  }

  async getWorkshops(params = {}): Promise<ApiResponse<Workshop[]>> {
    return this.getPaginated<ApiResponse<Workshop[]>>(params)
  }
}

export default new WorkshopAdminService()