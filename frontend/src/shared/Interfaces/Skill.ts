export interface Skill {
  id: number;
  name: {
    en?: string
    es?: string
  }
  created_at?: string;
  updated_at?: string;
}

export interface TranslatedSkill {
  id?: number
  en?: string
  es?: string
}

export interface SkillCreatePayload {
  name: Record<string, string>;
}