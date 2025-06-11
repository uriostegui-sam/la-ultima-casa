import type { Skill, SkillCreatePayload, SkillUpdatePayload } from '@/shared/Interfaces/Skill'
import { defineStore } from 'pinia'
import SkillAdminServices from '../Services/DataLayers/SkillAdminServices'

export const useAdminSkillStore = defineStore('adminSkill', {
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
      const response = await SkillAdminServices.getAll<Skill[]>()
      this.skills = response
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
        const skill = await SkillAdminServices.getById<Skill>(id)
        this.selectedSkill = skill
        return skill
      } catch (err: any) {
        this.error = err.message || 'Failed to load skill'
        return null
      } finally {
        this.loading = false
      }
    },

    async createSkill(payload: SkillCreatePayload) {
      this.loading = true
      this.error = null
      try {
        const newSkill = await SkillAdminServices.createSkill(payload)
        this.skills.push(newSkill)
        return newSkill
      } catch (err: any) {
        this.error = err.message || 'Failed to create skill'
        throw err
      } finally {
        this.loading = false
      }
    },

    async updateSkill(id: number, payload: SkillUpdatePayload) {
      this.loading = true
      this.error = null
      try {
        const updatedSkill = await SkillAdminServices.updateSkill(id, payload)
        const index = this.skills.findIndex((a) => a.id === id)
        if (index !== -1) {
          this.skills[index] = updatedSkill
        }
        if (this.selectedSkill?.id === id) {
          this.selectedSkill = updatedSkill
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
        await SkillAdminServices.delete(id)
        this.skills = this.skills.filter((a) => a.id !== id)
        if (this.selectedSkill?.id === id) {
          this.selectedSkill = null
        }
      } catch (err: any) {
        this.error = err.message || 'Failed to delete skill'
        throw err
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
