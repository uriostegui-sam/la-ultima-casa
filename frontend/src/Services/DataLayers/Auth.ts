import type { ApiResponse } from '@/Interfaces/ApiResponse'
import type { User } from '@/Interfaces/User'
import axiosInstance from './AxiosInstance'
import type { LoginCredentials, RegisterData } from '@/Interfaces/LoginCredentials'

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