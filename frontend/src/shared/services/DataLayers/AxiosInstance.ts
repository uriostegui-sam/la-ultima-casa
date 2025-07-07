import { useAuthStore } from '@/shared/stores/AuthStore';
import axios from 'axios'
import type {
  AxiosInstance,
  AxiosResponse,
  InternalAxiosRequestConfig
} from 'axios'

const axiosInstance: AxiosInstance = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
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
    
    // Only set Content-Type if it's NOT FormData
    const isFormData = (data: any): data is FormData => {
      return typeof FormData !== 'undefined' && data && typeof data.append === 'function' && data instanceof FormData;
    };

    if (!isFormData(config.data)) {
      config.headers.set('Content-Type', 'application/json');
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

<<<<<<< HEAD
export default axiosInstance
=======
export default axiosInstance
>>>>>>> develop
