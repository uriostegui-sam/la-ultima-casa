export interface Skill {
  id: number;
  name: string;
  translations: Record<string, string>;
  created_at?: string;
  updated_at?: string;
}

export interface SkillCreatePayload {
  name: Record<string, string>;
}