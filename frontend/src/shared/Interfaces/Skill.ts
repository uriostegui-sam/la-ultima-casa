export interface Skill {
  id: number;
  name: {
    en?: string
    es?: string
  }
  profile_image?: File
  created_at?: string;
  updated_at?: string;
}

export interface SkillCreatePayload {
  name: {
    en?: string
    es?: string
  }
  profile_image?: File
}

export interface SkillUpdatePayload {
  id: number
  name: {
    en?: string
    es?: string
  }
  profile_image?: File
}

export interface TranslatedSkill {
  en?: string
  es?: string
}