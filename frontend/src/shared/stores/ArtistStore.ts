import { defineStore } from 'pinia'
import type { Artist } from '@/shared/Interfaces/Artist'
import ArtistService from '@/visitors/Services/DataLayers/Artist'

export const useArtistStore = defineStore('artist', {
  state: () => ({
    artists: [] as Artist[],
    selectedArtist: null as Artist | null,
    loading: false,
    error: null as string | null
  }),

  actions: {
    async getArtists(params = {}) {
      this.loading = true
      this.error = null
      try {
        const response = await ArtistService.getArtists(params)
        this.artists = response.data
      } catch (err: any) {
        this.error = err.message || 'Failed to get artists'
      } finally {
        this.loading = false
      }
    },

    async getArtist(id: number | string): Promise<Artist | null> {
      this.loading = true
      this.error = null
      try {
        const artist = await ArtistService.getById<Artist>(id)
        this.selectedArtist = artist
        return artist
      } catch (err: any) {
        this.error = err.message || 'Failed to load artist'
        return null
      } finally {
        this.loading = false
      }
    },

    clearError() {
      this.error = null
    }
  }
})