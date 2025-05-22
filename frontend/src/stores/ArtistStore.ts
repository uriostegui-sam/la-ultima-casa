import { defineStore } from 'pinia'
import type { Artist, ArtistCreatePayload, ArtistUpdatePayload } from '@/Interfaces/Artist'
import ArtistService from '@/Services/DataLayers/Artist'

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
        this.selectedArtist = artist.data
        return artist
      } catch (err: any) {
        this.error = err.message || 'Failed to load artist'
        return null
      } finally {
        this.loading = false
      }
    },    

    async createArtist(payload: ArtistCreatePayload) {
      this.loading = true
      this.error = null
      try {
        const newArtist = await ArtistService.createArtist(payload)
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
        const updatedArtist = await ArtistService.updateArtist(id, payload)
        const index = this.artists.findIndex(a => a.id === id)
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
        await ArtistService.delete(id)
        this.artists = this.artists.filter(artist => artist.id !== id)
        if (this.selectedArtist?.id === id) {
          this.selectedArtist = null
        }
      } catch (err: any) {
        this.error = err.message || 'Failed to delete artist'
      } finally {
        this.loading = false
      }
    },

    setSelectedArtist(artist: Artist | null) {
      this.selectedArtist = artist
    },

    clearError() {
      this.error = null
    }
  }
})