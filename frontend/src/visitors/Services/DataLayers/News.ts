import type { News, NewsCreatePayload, NewsUpdatePayload } from '@/shared/Interfaces/News'
import type { ApiResponse } from '@/shared/Interfaces/ApiResponse'
import { BaseService } from '@/shared/services/DataLayers/BaseService'
import axiosInstance from '@/shared/services/DataLayers/AxiosInstance'

class NewsService extends BaseService {
  constructor() {
    super('/news')
  }

  async getNews(params?: {
  }): Promise<News[]> {
    const response = await axiosInstance.get<News[]>(
      `${this.baseUrl}/published`
    )
    return response.data
  }
}

export default new NewsService()