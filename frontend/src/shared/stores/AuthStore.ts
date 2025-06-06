import { defineStore } from 'pinia'
import authService from '@/visitors/Services/DataLayers/Auth'
import type { AuthState } from '@/shared/Interfaces/User'
import AuthService from '../services/DataLayers/AuthService';
import axiosInstance from '../services/DataLayers/AxiosInstance';

const TOKEN_KEY = 'auth_token'

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    token: localStorage.getItem(TOKEN_KEY) || null
  }),

  actions: {
    async login(credentials: { email: string; password: string }) {
      try {
        const response = await AuthService.login(credentials)
        this.setToken(response.data.token)
        await this.fetchUser()
        return response
      } catch (error) {
        console.error('Login error:', error)
        throw error
      }
    },

    async register(userData: any) {
      const response = await authService.register(userData)
      this.setToken(response.data.token)
      await this.fetchUser()
    },

    async fetchUser() {
      const response = await authService.getCurrentUser()
      this.user = response.data
    },

    setToken(token: string) {
      this.token = token
      localStorage.setItem(TOKEN_KEY, token)
      axiosInstance.defaults.headers.common['Authorization'] = `Bearer ${token}`
    },

    logout() {
      this.token = null
      this.user = null
      localStorage.removeItem(TOKEN_KEY)
      authService.logout()
    }
  },

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
    isArtist: (state) => state.user?.role === 'artist'
  }
})