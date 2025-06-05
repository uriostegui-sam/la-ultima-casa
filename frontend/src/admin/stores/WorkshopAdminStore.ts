import { defineStore } from 'pinia'
import type { Workshop, WorkshopCreatePayload, WorkshopUpdatePayload } from '@/shared/Interfaces/Workshop'
import WorkshopService from '@/visitors/Services/DataLayers/Workshop'

export const useWorkshopStore = defineStore('adminWorkshop', {
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

    async createWorkshop(formData: FormData) {
      this.loading = true
      this.error = null
      try {
        const payload: WorkshopCreatePayload = {
          artist_id: Number(formData.get('artist_id')),
          title: JSON.parse(formData.get('title') as string),
          description: JSON.parse(formData.get('description') as string),
          type: formData.get('type') as 'permanent' | 'temporary',
          start_date: formData.get('start_date') as string,
          end_date: formData.get('end_date') as string,
          price: Number(formData.get('price')),
          max_students: Number(formData.get('max_students')),
          cover_image: formData.get('cover_image') as File,
          skills: formData.getAll('skills[]').map((s) => Number(s)),
        }

        const newWorkshop = await WorkshopService.createWorkshop(payload)
        this.workshops.push(newWorkshop)
      } catch (err: any) {
        this.error = err.message || 'Failed to create workshop'
      } finally {
        this.loading = false
      }
    },

    async updateWorkshop(id: number | string, formData: FormData) {
      this.loading = true
      this.error = null
      try {
        const payload: WorkshopUpdatePayload = {
          id: Number(id),
        }

        if (formData.has('artist_id')) {
          payload.artist_id = Number(formData.get('artist_id'))
        }
        if (formData.has('title')) {
          payload.title = JSON.parse(formData.get('title') as string)
        }
        if (formData.has('description')) {
          payload.description = JSON.parse(formData.get('description') as string)
        }
        if (formData.has('type')) {
          payload.type = formData.get('type') as 'permanent' | 'temporary'
        }
        if (formData.has('start_date')) {
          payload.start_date = formData.get('start_date') as string
        }
        if (formData.has('end_date')) {
          payload.end_date = formData.get('end_date') as string
        }
        if (formData.has('price')) {
          payload.price = Number(formData.get('price'))
        }
        if (formData.has('max_students')) {
          payload.max_students = Number(formData.get('max_students'))
        }
        if (formData.has('cover_image')) {
          payload.cover_image = formData.get('cover_image') as File
        }
        if (formData.has('skills[]')) {
          payload.skills = formData.getAll('skills[]').map((id) => Number(id))
        }

        const updatedWorkshop = await WorkshopService.updateWorkshop(payload)

        const index = this.workshops.findIndex((w) => w.id === updatedWorkshop.id)
        if (index !== -1) {
          this.workshops[index] = updatedWorkshop
        }
        if (this.selectedWorkshop?.id === updatedWorkshop.id) {
          this.selectedWorkshop = updatedWorkshop
        }
      } catch (err: any) {
        this.error = err.message || 'Failed to update workshop'
      } finally {
        this.loading = false
      }
    },

    async deleteWorkshop(id: number) {
      this.loading = true
      this.error = null
      try {
        await WorkshopService.deleteWorkshop(id)
        this.workshops = this.workshops.filter((w) => w.id !== id)
        if (this.selectedWorkshop?.id === id) {
          this.selectedWorkshop = null
        }
      } catch (err: any) {
        this.error = err.message || 'Failed to delete workshop'
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
