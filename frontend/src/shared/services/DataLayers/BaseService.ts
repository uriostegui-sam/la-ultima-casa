import type { AxiosRequestConfig } from 'axios'
import axiosInstance from './AxiosInstance'

export class BaseService {
  protected baseUrl: string

  constructor(baseUrl: string) {
    this.baseUrl = baseUrl
  }

  async get<T>(url: string = '', config?: AxiosRequestConfig): Promise<T> {
    const response = await axiosInstance.get<{ data: T }>(`${this.baseUrl}${url}`, config)
    return response.data.data
  }

  async getAll<T>(): Promise<T> {
    return this.get<T>()
  }

  async getById<T>(id: number | string): Promise<T> {
    return this.get<T>(`/${id}`)
  }

  async create<T>(data: any): Promise<T> {
    const response = await axiosInstance.post<{ data: T }>(this.baseUrl, data)
    return response.data.data
  }

  async update<T>(id: number | string, data: any): Promise<T> {
    const response = await axiosInstance.post<{ data: T }>(`${this.baseUrl}/${id}`, data)
    return response.data.data
  }

  async delete<T>(id: number | string): Promise<T> {
    const response = await axiosInstance.delete<T>(`${this.baseUrl}/${id}`)
    return response.data
  }

  async getPaginated<T>(params: Record<string, any> = {}): Promise<T> {
    const response = await axiosInstance.get<T>(this.baseUrl, { params })
    return response.data
  }
}
