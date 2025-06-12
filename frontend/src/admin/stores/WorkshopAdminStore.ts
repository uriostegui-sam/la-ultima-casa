import { defineStore } from 'pinia'
import type { Workshop, WorkshopCreatePayload, WorkshopUpdatePayload } from '@/shared/Interfaces/Workshop'
import WorkshopAdminServices from '../Services/DataLayers/WorkshopAdminServices';

export const useAdminWorkshopStore = defineStore('adminWorkshop', {
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
        const response = await WorkshopAdminServices.getWorkshops(params)
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
        const workshop = await WorkshopAdminServices.getById<Workshop>(id)
        this.selectedWorkshop = workshop
      } catch (err: any) {
        this.error = err.message || 'Failed to load workshop'
      } finally {
        this.loading = false
      }
    },

    async createWorkshop(payload: WorkshopCreatePayload) {
      this.loading = true
      this.error = null
      try {
        const newWorkshop = await WorkshopAdminServices.createWorkshop(payload)
        this.workshops.push(newWorkshop)
        return newWorkshop
      } catch (err: any) {
        this.error = err.message || 'Failed to create workshop'
        throw err
      } finally {
        this.loading = false
      }
    },

    async updateWorkshop(id: number, payload: WorkshopUpdatePayload) {
      this.loading = true
      this.error = null
      try {
        const updatedWorkshop = await WorkshopAdminServices.updateWorkshop(id, payload)

        const index = this.workshops.findIndex((w) => w.id === id)
        if (index !== -1) {
          this.workshops[index] = updatedWorkshop
        }
        if (this.selectedWorkshop?.id === updatedWorkshop.id) {
          this.selectedWorkshop = updatedWorkshop
        }
        return updatedWorkshop
      } catch (err: any) {
        this.error = err.message || 'Failed to update workshop'
        throw err
      } finally {
        this.loading = false
      }
    },

    async deleteWorkshop(id: number) {
      this.loading = true
      this.error = null
      try {
        await WorkshopAdminServices.deleteWorkshop(id)
        this.workshops = this.workshops.filter((w) => w.id !== id)
        if (this.selectedWorkshop?.id === id) {
          this.selectedWorkshop = null
        }
      } catch (err: any) {
        this.error = err.message || 'Failed to delete workshop'
        throw err
      } finally {
        this.loading = false
      }
    },

    setSelectedWorkshop(workshop: Workshop | null) {
      this.selectedWorkshop = workshop
    },

    clearError() {
      this.error = null
    },
  },
})
