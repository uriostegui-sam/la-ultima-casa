import type { Skill } from "@/Interfaces/Skill"
import { BaseService } from "./BaseService"

class SkillService extends BaseService {
  constructor() {
    super('/skills')
  }
}

export default new SkillService()
