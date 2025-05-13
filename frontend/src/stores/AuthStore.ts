import { defineStore } from 'pinia'
import authService from '@/Services/DataLayers/Auth'
import type { AuthState } from '@/Interfaces/User'

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    token: localStorage.getItem('token') || null
  }),

  actions: {
    async login(credentials: { email: string; password: string }) {
      const response = await authService.login(credentials)
      this.setToken(response.data.token)
      await this.fetchUser()
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
      localStorage.setItem('token', token)
    },

    logout() {
      this.token = null
      this.user = null
      localStorage.removeItem('token')
      authService.logout()
    }
  },

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
    isArtist: (state) => state.user?.role === 'artist'
  }
})