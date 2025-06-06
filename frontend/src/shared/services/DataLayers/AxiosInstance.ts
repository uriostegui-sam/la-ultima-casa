import { useAuthStore } from '@/shared/stores/AuthStore';
import axios from 'axios'
import type {
  AxiosInstance,
  AxiosResponse,
  InternalAxiosRequestConfig
} from 'axios'

const axiosInstance: AxiosInstance = axios.create({
  baseURL: '/api',
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
})

axiosInstance.defaults.withXSRFToken = true;

// Request interceptor
axiosInstance.interceptors.request.use(
  (config: InternalAxiosRequestConfig) => {
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.set('Authorization', `Bearer ${token}`)
    }
    return config
  }
)


// Response interceptor
axiosInstance.interceptors.response.use(
  (response: AxiosResponse) => response,
  (error) => {
    if (error.response?.status === 401) {
      const authStore = useAuthStore()
      authStore.logout()
      window.location.href = '/admin/auth/login'
    }
    return Promise.reject(error.response?.data || error)
  }
)

export default axiosInstance