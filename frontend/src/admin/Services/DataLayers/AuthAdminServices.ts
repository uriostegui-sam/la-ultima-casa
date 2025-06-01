import type { ApiResponse } from '@/shared/Interfaces/ApiResponse'
import type { User } from '@/shared/Interfaces/User'
import axiosInstance from '@/shared/services/DataLayers/AxiosInstance'
import type { LoginCredentials, RegisterData } from '@/shared/Interfaces/LoginCredentials'

export default {
  async login(credentials: LoginCredentials): Promise<ApiResponse<{ token: string }>> {
    return axiosInstance.post('/auth/login', credentials)
  },

  async register(data: RegisterData): Promise<ApiResponse<{ token: string }>> {
    return axiosInstance.post('/auth/register', data)
  },

  async googleAuth(): Promise<{ url: string }> {
    return axiosInstance.get('/auth/google')
  },

  async logout(): Promise<void> {
    return axiosInstance.post('/auth/logout')
  },

  async getCurrentUser(): Promise<ApiResponse<User>> {
    return axiosInstance.get('/auth/user')
  }
}