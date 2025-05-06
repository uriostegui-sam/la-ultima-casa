import type { News } from '@/Interfaces/News'
import axiosInstance from './AxiosInstance'
import { BaseService } from './BaseService'
import type { ApiResponse } from '@/Interfaces/ApiResponse'

class NewsService extends BaseService {
  constructor() {
    super('/news')
  }

  async createNews(data: FormData): Promise<News> {
    const response = await axiosInstance.post<News>(this.baseUrl, data)
    return response.data
  }

  async updateNews(id: number, data: FormData): Promise<News> {
    const response = await axiosInstance.post<News>(`${this.baseUrl}/${id}?_method=PUT`, data)
    return response.data
  }

  async getNews(params = {}): Promise<ApiResponse<News[]>> {
    return this.getPaginated<ApiResponse<News[]>>(params)
  }
}

export default new NewsService()