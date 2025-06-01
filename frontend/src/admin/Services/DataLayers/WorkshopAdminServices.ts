import type { ApiResponse } from '@/shared/Interfaces/ApiResponse'
import axiosInstance from '@/shared/services/DataLayers/AxiosInstance'
import { BaseService } from '@/shared/services/DataLayers/BaseService'
import type { Workshop, WorkshopCreatePayload, WorkshopUpdatePayload } from '@/shared/Interfaces/Workshop'
import axios from 'axios'

type WorkshopType = 'permanent' | 'temporary'

class WorkshopService extends BaseService {
  constructor() {
    super('/workshops')
  }

  async createWorkshop(payload: WorkshopCreatePayload): Promise<Workshop> {
    const formData = new FormData()
    
    // Required fields
    formData.append('artist_id', payload.artist_id.toString())
    formData.append('title', JSON.stringify(payload.title))
    formData.append('description', JSON.stringify(payload.description))
    formData.append('type', payload.type)
    formData.append('start_date', payload.start_date)
    formData.append('end_date', payload.end_date)
    formData.append('price', payload.price.toString())
    formData.append('max_students', payload.max_students.toString())

    // Optional fields
    if (payload.cover_image) {
      formData.append('cover_image', payload.cover_image)
    }
    if (payload.skills) {
      payload.skills.forEach(id => formData.append('skills[]', id.toString()))
    }

    const response = await axiosInstance.post<Workshop>(this.baseUrl, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    return response.data
  }

  async updateWorkshop(payload: WorkshopUpdatePayload): Promise<Workshop> {
    const formData = new FormData()
    
    // Append only provided fields
    if (payload.artist_id) formData.append('artist_id', payload.artist_id.toString())
    if (payload.title) formData.append('title', JSON.stringify(payload.title))
    if (payload.description) formData.append('description', JSON.stringify(payload.description))
    if (payload.type) formData.append('type', payload.type)
    if (payload.start_date) formData.append('start_date', payload.start_date)
    if (payload.end_date) formData.append('end_date', payload.end_date)
    if (payload.price) formData.append('price', payload.price.toString())
    if (payload.max_students) formData.append('max_students', payload.max_students.toString())
    if (payload.cover_image) {
      formData.append('cover_image', payload.cover_image)
    }
    if (payload.skills) {
      payload.skills.forEach(id => formData.append('skills[]', id.toString()))
    }
    
    formData.append('_method', 'PUT')

    const response = await axiosInstance.post<Workshop>(
      `${this.baseUrl}/${payload.id}`,
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }
    )
    return response.data
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

export default new WorkshopService()