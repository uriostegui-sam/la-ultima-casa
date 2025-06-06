import axios from 'axios';
import axiosInstance from './AxiosInstance'

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
}