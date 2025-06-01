import { BaseService } from "@/shared/services/DataLayers/BaseService"

class SkillService extends BaseService {
  constructor() {
    super('/skills')
  }
}

export default new SkillService()
