import { defineStore } from "pinia"
import type { Artwork, ArtworkCreatePayload } from "@/shared/Interfaces/Artwork"
import ArtworkService from '@/visitors/Services/DataLayers/Artwork'

export const useArtworkStore = defineStore('adminArtwork', {
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

    async createArtwork(formData: FormData) {
      this.loading = true
      this.error = null
      try {
        const payload: ArtworkCreatePayload = {
          artist_id: Number(formData.get('artist_id')),
          title: formData.get('title') as string,
          images: formData.getAll('images[]') as File[],
        };

        const description = formData.get('description');
        if (description) {
          payload.description = JSON.parse(description as string);
        }
    
        const dimensions = formData.get('dimensions');
        if (dimensions) {
          payload.dimensions = JSON.parse(dimensions as string);
        }
    
        const creation_date = formData.get('creation_date');
        if (creation_date) {
          payload.creation_date = creation_date as string;
        }
    
        const newArtwork = await ArtworkAdminServices.createArtwork(payload);
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
        const updatedArtwork = await ArtworkAdminServices.update<Artwork>(id, formData)
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