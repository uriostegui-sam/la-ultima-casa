import type { ApiResponse } from "@/shared/Interfaces/ApiResponse";
import type { AboutUs, AboutUsCreatePayload, AboutUsUpdatePayload } from "@/shared/Interfaces/AboutUs";
import axiosInstance from "@/shared/services/DataLayers/AxiosInstance";
import { BaseService } from "@/shared/services/DataLayers/BaseService"
import axios from "axios";
import { buildAboutUsFormData } from "../Helpers/formAboutUsHelper";

class AboutUsService extends BaseService {
  constructor() {
    super('/aboutUs')
  }

  async createAboutUs(payload: AboutUsCreatePayload): Promise<AboutUs> {
    const formData = buildAboutUsFormData(payload, true);

    return await this.create<AboutUs>(formData)
  }

  async updateAboutUs(id: number, payload: AboutUsUpdatePayload): Promise<AboutUs> {
    const formData = buildAboutUsFormData(payload, false);
    
    return await this.update<AboutUs>(id, formData)
  }

  async deleteAboutUs(id: number): Promise<void> {
    try {
      await axiosInstance.delete(`${this.baseUrl}/${id}`)
    } catch (error) {
      if (axios.isAxiosError(error) && error.response?.status === 422) {
        throw new Error('cannotAboutUsWArtwork')
      }
      throw error
    }
  }

  async getAboutUs(params = {}): Promise<ApiResponse<AboutUs[]>> {
    return this.getPaginated<ApiResponse<AboutUs[]>>(params)
  }
}

export default new AboutUsService()
