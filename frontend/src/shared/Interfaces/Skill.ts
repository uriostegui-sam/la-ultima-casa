export interface Skill {
  id: number;
  name: {
    en?: string
    es?: string
  }
  profile_image?: File
  published?: boolean
  created_at?: string;
  updated_at?: string;
}

export interface SkillCreatePayload {
  name: {
    en?: string
    es?: string
  }
  profile_image?: File
  published?: boolean
}

export interface SkillUpdatePayload {
  id: number
  name: {
    en?: string
    es?: string
  }
  profile_image?: File
  published?: boolean
}