import { defineStore } from "pinia"
import type { Artwork } from "@/Interfaces/Artwork"
import ArtworkService from '@/Services/DataLayers/Artwork'

export const useArtworkStore = defineStore('artwork', {
  state: () => ({
    artworks: [] as Artwork[],
    selectedArtwork: null as Artwork | null,
    loading: false,
    error: null as string | null
  }),

  actions: {
    async getArtworks(params = {}) {
      this.loading = true
      this.error = null
      try {
        const response = await ArtworkService.getArtworks(params)
        this.artworks = response.data
      } catch (err: any) {
        this.error = err.message || 'Failed to load artworks'
      } finally {
        this.loading = false
      }
    },

    async getArtwork(id: number | string) {
      this.loading = true
      this.error = null
      try {
        const artwork = await ArtworkService.getById<Artwork>(id)
        this.selectedArtwork = artwork
      } catch (err: any) {
        this.error = err.message || 'Failed to load artwork'
      } finally {
        this.loading = false
      }
    },

    async createArtwork(formData: FormData) {
      this.loading = true
      this.error = null
      try {
        const newArtwork = await ArtworkService.uploadArtwork(formData)
        this.artworks.push(newArtwork)
      } catch (err: any) {
        this.error = err.message || 'Failed to upload artwork'
      } finally {
        this.loading = false
      }
    },

    async updateArtwork(id: number | string, formData: FormData) {
      this.loading = true
      this.error = null
      try {
        const updatedArtwork = await ArtworkService.update<Artwork>(id, formData)
        const index = this.artworks.findIndex(a => a.id === updatedArtwork.id)
        if (index !== -1) {
          this.artworks[index] = updatedArtwork
        }
        if (this.selectedArtwork?.id === updatedArtwork.id) {
          this.selectedArtwork = updatedArtwork
        }
      } catch (err: any) {
        this.error = err.message || 'Failed to update artwork'
      } finally {
        this.loading = false
      }
    },

    async deleteArtwork(id: number | string) {
      this.loading = true
      this.error = null
      try {
        await ArtworkService.delete(id)
        this.artworks = this.artworks.filter(a => a.id !== id)
        if (this.selectedArtwork?.id === id) {
          this.selectedArtwork = null
        }
      } catch (err: any) {
        this.error = err.message || 'Failed to delete artwork'
      } finally {
        this.loading = false
      }
    },

    setSelectedArtwork(artwork: Artwork | null) {
      this.selectedArtwork = artwork
    },
  
    clearError() {
      this.error = null
    }
  }
})