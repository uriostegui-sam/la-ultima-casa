import type { News, NewsCreatePayload, NewsUpdatePayload } from '@/shared/Interfaces/News'
import type { ApiResponse } from '@/shared/Interfaces/ApiResponse'
import { BaseService } from '@/shared/services/DataLayers/BaseService'
import axiosInstance from '@/shared/services/DataLayers/AxiosInstance'

class NewsService extends BaseService {
  constructor() {
    super('/news')
  }

  async createNews(payload: NewsCreatePayload): Promise<News> {
    const formData = new FormData()

    // Append JSON fields
    formData.append('title', JSON.stringify(payload.title))
    formData.append('content', JSON.stringify(payload.content))

    if (payload.published_at) {
      formData.append('published_at', payload.published_at)
    }

    // Append image if exists
    if (payload.image) {
      formData.append('image', payload.image)
    }

    const response = await axiosInstance.post<News>(this.baseUrl, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    return response.data
  }

  async updateNews(payload: NewsUpdatePayload): Promise<News> {
    const formData = new FormData()

    if (payload.title) {
      formData.append('title', JSON.stringify(payload.title))
    }
    if (payload.content) {
      formData.append('content', JSON.stringify(payload.content))
    }
    if (payload.published_at) {
      formData.append('published_at', payload.published_at)
    }
    if (payload.image) {
      formData.append('image', payload.image)
    }
    formData.append('_method', 'PUT')

    const response = await axiosInstance.post<News>(`${this.baseUrl}/${payload.id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    return response.data
  }

  async deleteNews(id: number): Promise<void> {
    await axiosInstance.delete(`${this.baseUrl}/${id}`)
  }

  async getNews(params?: {
    published_from?: string
    published_to?: string
  }): Promise<ApiResponse<News[]>> {
    return this.getPaginated<ApiResponse<News[]>>(params)
  }
}

export default new NewsService()