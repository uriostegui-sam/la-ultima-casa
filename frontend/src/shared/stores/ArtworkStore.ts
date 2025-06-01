import { defineStore } from "pinia"
import type { Artwork, ArtworkCreatePayload } from "@/shared/Interfaces/Artwork"
import ArtworkService from '@/visitors/Services/DataLayers/Artwork'

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
  
    clearError() {
      this.error = null
    }
  }
})