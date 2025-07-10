import axios from 'axios';
import axiosInstance from './AxiosInstance'
import type { PasswordReset, UserPasswordUpdate } from '@/shared/Interfaces/User';

export default {
  async getCsrfCookie(): Promise<void> {
    await axios.get('/sanctum/csrf-cookie', {
      withCredentials: true
    })
  },

  async login(credentials: { email: string; password: string }): Promise<any> {
    await this.getCsrfCookie()
    return axiosInstance.post('/auth/login', credentials, {
      withCredentials: true
    })
  },

  async updatePassword(payload: UserPasswordUpdate): Promise<UserPasswordUpdate> {
      const reponse = await axiosInstance.post<UserPasswordUpdate>(
        '/auth/update-password',
        payload
      )
      return reponse.data
  },

    async resetPassword(payload: PasswordReset): Promise<PasswordReset> {
    console.log("reset")
    const reponse = await axiosInstance.post<PasswordReset>(
      '/auth/reset-password',
      payload
    )
    return reponse.data
  }
}