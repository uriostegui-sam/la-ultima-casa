import type { News, NewsCreatePayload, NewsUpdatePayload } from '@/shared/Interfaces/News'
import type { ApiResponse } from '@/shared/Interfaces/ApiResponse'
import { BaseService } from '@/shared/services/DataLayers/BaseService'

class NewsService extends BaseService {
  constructor() {
    super('/news')
  }

  async getNews(params?: {
    published_from?: string
    published_to?: string
  }): Promise<ApiResponse<News[]>> {
    return this.getPaginated<ApiResponse<News[]>>(params)
  }
}

export default new NewsService()