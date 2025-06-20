import type { News, NewsCreatePayload, NewsUpdatePayload } from '@/shared/Interfaces/News'
import type { ApiResponse } from '@/shared/Interfaces/ApiResponse'
import { BaseService } from '@/shared/services/DataLayers/BaseService'
import axiosInstance from '@/shared/services/DataLayers/AxiosInstance'
import { buildNewsFormData } from '../Helpers/formNewsHelper'
import axios from 'axios'

class NewsAdminService extends BaseService {
  constructor() {
    super('/news')
  }

  async createNews(payload: NewsCreatePayload): Promise<News> {
    const formData = buildNewsFormData(payload, true)

    return await this.create<News>(formData)
  }

  async updateNews(id: number, payload: NewsUpdatePayload): Promise<News> {
    const formData = buildNewsFormData(payload, false)
    return await this.update<News>(id, formData)
  }

  async deleteNews(id: number): Promise<void> {
    try {
      await axiosInstance.delete(`${this.baseUrl}/${id}`)
    } catch (error) {
      if (axios.isAxiosError(error) && error.response?.status === 422) {
        throw new Error(error.response.data.message || 'Cannot delete news')
      }
      throw error
    }
  }

  async getNews(params?: {
  }): Promise<ApiResponse<News[]>> {
    return this.getPaginated<ApiResponse<News[]>>(params)
  }
}

export default new NewsAdminService()