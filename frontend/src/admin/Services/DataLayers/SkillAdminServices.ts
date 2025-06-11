import type { ApiResponse } from "@/shared/Interfaces/ApiResponse";
import type { Skill, SkillCreatePayload, SkillUpdatePayload } from "@/shared/Interfaces/Skill";
import axiosInstance from "@/shared/services/DataLayers/AxiosInstance";
import { BaseService } from "@/shared/services/DataLayers/BaseService"
import axios from "axios";
import { buildSkillFormData } from "../Helpers/formSkillHelper";

class SkillService extends BaseService {
  constructor() {
    super('/skills')
  }

  async createSkill(payload: SkillCreatePayload): Promise<Skill> {
    const formData = buildSkillFormData(payload, true);

    return await this.create<Skill>(formData)
  }

  async updateSkill(id: number, payload: SkillUpdatePayload): Promise<Skill> {
    const formData = buildSkillFormData(payload, false);
    
    return await this.update<Skill>(id, formData)
  }

  async deleteSkill(id: number): Promise<void> {
    try {
      await axiosInstance.delete(`${this.baseUrl}/${id}`)
    } catch (error) {
      if (axios.isAxiosError(error) && error.response?.status === 422) {
        throw new Error('cannotSkillWArtwork')
      }
      throw error
    }
  }

  async getSkills(params = {}): Promise<ApiResponse<Skill[]>> {
    return this.getPaginated<ApiResponse<Skill[]>>(params)
  }
}

export default new SkillService()
