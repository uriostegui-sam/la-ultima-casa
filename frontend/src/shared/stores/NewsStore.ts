import type { News, NewsCreatePayload, NewsUpdatePayload } from '@/shared/Interfaces/News'
import NewsService from '@/visitors/Services/DataLayers/News'
import { defineStore } from 'pinia'

export const useNewsStore = defineStore('news', {
  state: () => ({
    news: [] as News[],
    selectedNews: null as News | null,
    loading: false,
    error: null as string | null,
  }),

  actions: {
    async getNews(params = {}) {
      this.loading = true
      this.error = null
      try {
        const response = await NewsService.getNews(params)
        this.news = response.data
      } catch (err: any) {
        this.error = err.message || 'Failed to get news'
      } finally {
        this.loading = false
      }
    },

    async getNewsById(id: number | string): Promise<News | null> {
      this.loading = true
      this.error = null
      try {
        const news = await NewsService.getById<News>(id)
        this.selectedNews = news
        return news
      } catch (err: any) {
        this.error = err.message || 'Failed to load news'
        return null
      } finally {
        this.loading = false
      }
    },
    
    clearError() {
      this.error = null
    },
  },
})