import type { Skill } from '@/Interfaces/Skill'
import SkillService from '@/Services/DataLayers/Skill'
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

    async createSkill(payload: Skill) {
      this.loading = true
      this.error = null
      try {
        const newSkill = await SkillService.create<Skill>(payload)
        this.skills.push(newSkill)
        return newSkill
      } catch (err: any) {
        this.error = err.message || 'Failed to create skill'
        throw err
      } finally {
        this.loading = false
      }
    },

    async updateSkill(id: number | string, payload: Skill) {
      this.loading = true
      this.error = null
      try {
        const updatedSkill = await SkillService.update<Skill>(id, payload)
        const index = this.skills.findIndex((a) => a.id === updatedSkill.id)
        if (index !== -1) {
          this.skills[index] = updatedSkill
        }
        return updatedSkill
      } catch (err: any) {
        this.error = err.message || 'Failed to update skill'
        throw err
      } finally {
        this.loading = false
      }
    },

    async deleteSkill(id: number) {
      this.loading = true
      this.error = null
      try {
        await SkillService.delete(id)
        this.skills = this.skills.filter((a) => a.id !== id)
        if (this.selectedSkill?.id === id) {
          this.selectedSkill = null
        }
      } catch (err: any) {
        this.error = err.message || 'Failed to delete skill'
      } finally {
        this.loading = false
      }
    },

    setSelectedSkill(skill: Skill | null) {
      this.selectedSkill = skill
    },

    clearError() {
      this.error = null
    },
  },
})
