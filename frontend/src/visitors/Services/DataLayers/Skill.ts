import type { ApiResponse } from "@/shared/Interfaces/ApiResponse"
import type { Skill } from "@/shared/Interfaces/Skill"
import axiosInstance from "@/shared/services/DataLayers/AxiosInstance"
import { BaseService } from "@/shared/services/DataLayers/BaseService"

class SkillService extends BaseService {
  constructor() {
    super('/skills')
  }

  async getPublishedSkills(): Promise<ApiResponse<Skill[]>> {
    const response = await axiosInstance.get<ApiResponse<Skill[]>>(
      `${this.baseUrl}/published`
    )
    return response.data
  }
}

export default new SkillService()
