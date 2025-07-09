import axios from 'axios';
import type { UserPasswordUpdate } from '@/shared/Interfaces/User';

export default (axiosInstance = axios) => ({
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
})