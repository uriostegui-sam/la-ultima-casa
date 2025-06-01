import type { Skill } from '@/shared/Interfaces/Skill'
import SkillService from '@/visitors/Services/DataLayers/Skill'
import { defineStore } from 'pinia'

export const useSkillStore = defineStore('skill', {
  state: () => ({
    skills: [] as Skill[],
    selectedSkill: null as Skill | null,
    loading: false,
    error: null as string | null,
  }),

  actions: {
    async getSkills() {
      this.loading = true
      this.error = null
      try {
        const response = await SkillService.getAll() as { data: Skill[] }
        this.skills = response.data
      } catch (err: any) {
        this.error = err.message || 'Failed to get skills'
      } finally {
        this.loading = false
      }
    },

    async getSkill(id: number | string): Promise<Skill | null> {
      this.loading = true
      this.error = null
      try {
        const skill = await SkillService.getById<Skill>(id)
        this.selectedSkill = skill
        return skill
      } catch (err: any) {
        this.error = err.message || 'Failed to load skill'
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
