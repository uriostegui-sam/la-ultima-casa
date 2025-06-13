import type { AboutUs, AboutUsCreatePayload, AboutUsUpdatePayload } from '@/shared/Interfaces/AboutUs'
import { defineStore } from 'pinia'
import AboutUsAdminServices from '../Services/DataLayers/AboutUsAdminServices'

export const useAdminAboutUsStore = defineStore('adminAboutUs', {
  state: () => ({
    aboutUs: [] as AboutUs[],
    selectedAboutUs: null as AboutUs | null,
    loading: false,
    error: null as string | null,
  }),

  actions: {
    async getAboutUs() {
      this.loading = true
      this.error = null
      try {
      const response = await AboutUsAdminServices.getAll<AboutUs[]>()
      this.aboutUs = response
      } catch (err: any) {
        this.error = err.message || 'Failed to get aboutUs'
      } finally {
        this.loading = false
      }
    },

    async getAboutUsById(id: number | string): Promise<AboutUs | null> {
      this.loading = true
      this.error = null
      try {
        const aboutUs = await AboutUsAdminServices.getById<AboutUs>(id)
        this.selectedAboutUs = aboutUs
        return aboutUs
      } catch (err: any) {
        this.error = err.message || 'Failed to load aboutUs'
        return null
      } finally {
        this.loading = false
      }
    },

    async createAboutUs(payload: AboutUsCreatePayload) {
      this.loading = true
      this.error = null
      try {
        const newAboutUs = await AboutUsAdminServices.createAboutUs(payload)
        this.aboutUs.push(newAboutUs)
        return newAboutUs
      } catch (err: any) {
        this.error = err.message || 'Failed to create aboutUs'
        throw err
      } finally {
        this.loading = false
      }
    },

    async updateAboutUs(id: number, payload: AboutUsUpdatePayload) {
      this.loading = true
      this.error = null
      try {
        const updatedAboutUs = await AboutUsAdminServices.updateAboutUs(id, payload)
        const index = this.aboutUs.findIndex((a) => a.id === id)
        if (index !== -1) {
          this.aboutUs[index] = updatedAboutUs
        }
        if (this.selectedAboutUs?.id === id) {
          this.selectedAboutUs = updatedAboutUs
        }
        return updatedAboutUs
      } catch (err: any) {
        this.error = err.message || 'Failed to update about us'
        throw err
      } finally {
        this.loading = false
      }
    },

    async deleteAboutUs(id: number) {
      this.loading = true
      this.error = null
      try {
        await AboutUsAdminServices.delete(id)
        this.aboutUs = this.aboutUs.filter((a) => a.id !== id)
        if (this.selectedAboutUs?.id === id) {
          this.selectedAboutUs = null
        }
      } catch (err: any) {
        this.error = err.message || 'Failed to delete about us'
        throw err
      } finally {
        this.loading = false
      }
    },

    setSelectedAboutUs(aboutUs: AboutUs | null) {
      this.selectedAboutUs = aboutUs
    },

    clearError() {
      this.error = null
    },
  },
})
