import type { SkillCreatePayload, SkillUpdatePayload } from "@/shared/Interfaces/Skill";

export function buildSkillFormData(
  payload: SkillCreatePayload | SkillUpdatePayload,
  isCreate: boolean = true
): FormData {
  const formData = new FormData();

  if (payload.name?.es) {
    formData.append('name[es]', payload.name.es);
  }
  if (payload.name?.en) {
    formData.append('name[en]', payload.name.en);
  }

  // File uploads
  if ('profile_image' in payload && payload.profile_image) {
    formData.append('profile_image', payload.profile_image);
  }

  if (!isCreate) {
    formData.append('_method', 'PUT')
  }

  return formData;
}