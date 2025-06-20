import { defineStore } from 'pinia'
import type { Workshop, WorkshopCreatePayload, WorkshopUpdatePayload } from '@/shared/Interfaces/Workshop'
import WorkshopService from '@/visitors/Services/DataLayers/Workshop'

export const useWorkshopStore = defineStore('workshop', {
  state: () => ({
    workshops: [] as Workshop[],
    selectedWorkshop: null as Workshop | null,
    loading: false,
    error: null as string | null,
  }),

  actions: {
    async getWorkshops(params = {}) {
        this.loading = true;
        this.error = null;
    try {
        const response = await WorkshopService.getWorkshops(params)
        this.workshops = response.data
      } catch (err: any) {
        this.error = err.message || 'Failed to load workshops'
      } finally {
        this.loading = false
      }
    },

    async getWorkshop(id: number | string) {
      this.loading = true
      this.error = null
      try {
        const workshop = await WorkshopService.getById<Workshop>(id)
        this.selectedWorkshop = workshop
      } catch (err: any) {
        this.error = err.message || 'Failed to load workshop'
      } finally {
        this.loading = false
      }
    },

    async getFeaturedWorkshops() {
      this.loading = true
      this.error = null
      try {
        const response = await WorkshopService.getFeaturedWorkshops()
        this.workshops = response
      } catch (err: any) {
        this.error = err.message || 'Failed to load workshop'
      } finally {
        this.loading = false
      }
    },

    clearError() {
      this.error = null
    },
  },
})
