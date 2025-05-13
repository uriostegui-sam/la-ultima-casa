import type { News } from "@/Interfaces/News"
import NewsService from "@/Services/DataLayers/News"
import { defineStore } from "pinia"

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
      }
      catch (err: any) {
        this.error = err.message || 'Failed to get news'
      }
      finally {
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
      }
      catch (err: any) {
        this.error = err.message || 'Failed to load news'
        return null
      }
      finally {
        this.loading = false
      }
    },
    async createNews(formData: FormData) {
      this.loading = true
      this.error = null
      try {
        const newNews = await NewsService.createNews(formData)
        this.news.push(newNews)
        return newNews
      }
      catch (err: any) {
        this.error = err.message || 'Failed to create news'
        throw err
      }
      finally {
        this.loading = false
      }
    },

    async updateNews(id: number | string, formData: FormData) {
      this.loading = true
      this.error = null
      try {
        const updatedNews = await NewsService.update<News>(id, formData)
        const index = this.news.findIndex(n => n.id === updatedNews.id)
        if (index !== -1) {
          this.news[index] = updatedNews
        }
        if (this.selectedNews?.id === updatedNews.id) {
          this.selectedNews = updatedNews
        }
        return updatedNews
      }
      catch (err: any) {
        this.error = err.message || 'Failed to update news'
        throw err
      }
      finally {
        this.loading = false
      }
    },
    
    async deleteNews(id: number | string) {
      this.loading = true
      this.error = null
      try {
        await NewsService.delete(id)
        this.news = this.news.filter(n => n.id !== id)
        if (this.selectedNews?.id === id) {
          this.selectedNews = null
        }
      }
      catch (err: any) {
        this.error = err.message || 'Failed to delete news'
      }
      finally {
        this.loading = false
      }
    },

    setSelectedNews(news: News | null) {
      this.selectedNews = news
    },

    clearError() {
      this.error = null
    }
  }
})