import { defineStore } from 'pinia'
import type { Artist, ArtistCreatePayload, ArtistUpdatePayload } from '@/shared/Interfaces/Artist'
import ArtistAdminServices from '../Services/DataLayers/ArtistAdminServices'

export const useAdminArtistStore = defineStore('adminArtist', {
  state: () => ({
    artists: [] as Artist[],
    selectedArtist: null as Artist | null,
    loading: false,
    error: null as string | null,
  }),

  actions: {
    async getArtists(params = {}) {
      this.loading = true
      this.error = null
      try {
        const response = await ArtistAdminServices.getArtists(params)
        this.artists = response.data
      } catch (err: any) {
        this.error = err.message || 'Failed to fetch artists'
      } finally {
        this.loading = false
      }
    },

    async getArtist(id: number): Promise<Artist | null> {
      this.loading = true
      this.error = null
      try {
        const artist = await ArtistAdminServices.getById<Artist>(id)
        this.selectedArtist = artist
        return artist
      } catch (err: any) {
        this.error = err.message || 'Failed to fetch artist'
        return null
      } finally {
        this.loading = false
      }
    },
    async createArtist(payload: ArtistCreatePayload) {
      this.loading = true
      this.error = null
      try {
        const newArtist = await ArtistAdminServices.createArtist(payload)
        this.artists.push(newArtist)
        return newArtist
      } catch (err: any) {
        this.error = err.message || 'Failed to create artist'
        throw err
      } finally {
        this.loading = false
      }
    },

    async updateArtist(id: number, payload: ArtistUpdatePayload) {
      this.loading = true
      this.error = null
      try {
        const updatedArtist = await ArtistAdminServices.updateArtist(id, payload)
        const index = this.artists.findIndex((a) => a.id === id)
        if (index !== -1) {
          this.artists[index] = updatedArtist
        }
        if (this.selectedArtist?.id === id) {
          this.selectedArtist = updatedArtist
        }
        return updatedArtist
      } catch (err: any) {
        this.error = err.message || 'Failed to update artist'
        throw err
      } finally {
        this.loading = false
      }
    },

    async deleteArtist(id: number) {
      this.loading = true
      this.error = null
      try {
        await ArtistAdminServices.deleteArtist(id)
        this.artists = this.artists.filter((artist) => artist.id !== id)
        if (this.selectedArtist?.id === id) {
          this.selectedArtist = null
        }
      } catch (err: any) {
        this.error = err.message || 'Failed to delete artist'
      } finally {
        this.loading = false
      }
    },
  },
})
