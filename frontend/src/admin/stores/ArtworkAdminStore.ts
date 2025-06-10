import { defineStore } from "pinia"
import type { Artwork, ArtworkCreatePayload, ArtworkUpdatePayload } from "@/shared/Interfaces/Artwork"
import ArtworkAdminServices from "../Services/DataLayers/ArtworkAdminServices"
import axiosInstance from "@/shared/services/DataLayers/AxiosInstance"
// import ArtworkAdminServices from "../Services/DataLayers/ArtworkAdminServices"

export const useAdminArtworkStore = defineStore('adminArtwork', {
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
        const response = await ArtworkAdminServices.getArtworks(params)
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
        const artwork = await ArtworkAdminServices.getById<Artwork>(id)
        this.selectedArtwork = artwork
      } catch (err: any) {
        this.error = err.message || 'Failed to load artwork'
      } finally {
        this.loading = false
      }
    },

    async createArtwork(payload: ArtworkCreatePayload) {
      this.loading = true
      this.error = null
      try {    
        const newArtwork = await ArtworkAdminServices.createArtwork(payload);
        this.artworks.push(newArtwork)
        return newArtwork
      } catch (err: any) {
        this.error = err.message || 'Failed to upload artwork'
        throw err
      } finally {
        this.loading = false
      }
    },

    async updateArtwork(id: number, payload: ArtworkUpdatePayload) {
      this.loading = true
      this.error = null
      try {
        const updatedArtwork = await ArtworkAdminServices.updateArtwork(id, payload)
        const index = this.artworks.findIndex((a) => a.id === id);
        if (index !== -1) {
          this.artworks[index] = updatedArtwork
        }
        if (this.selectedArtwork?.id === id) {
          this.selectedArtwork = updatedArtwork
        }
        return updatedArtwork
      } catch (err: any) {
        this.error = err.message || 'Failed to update artwork'
        throw err
      } finally {
        this.loading = false
      }
    },

    async deleteArtwork(id: number | string) {
      this.loading = true
      this.error = null
      try {
        await ArtworkAdminServices.delete(id)
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