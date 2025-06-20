import type { WorkshopCreatePayload, WorkshopUpdatePayload } from '@/shared/Interfaces/Workshop'
import { formatDateToYMD } from '.'

export function buildWorkshopFormData(
  payload: WorkshopCreatePayload | WorkshopUpdatePayload,
  isCreate: boolean = true,
): FormData {
  const formData = new FormData()

  if ('artist_id' in payload && payload.artist_id) {
    formData.append('artist_id', payload.artist_id.toString())
  }
  if (payload.title?.en) {
    formData.append('title[en]', payload.title.en)
  }
  if (payload.title?.es) {
    formData.append('title[es]', payload.title.es)
  }
  if (payload.description?.en) {
    formData.append('description[en]', payload.description.en)
  }
  if (payload.description?.es) {
    formData.append('description[es]', payload.description.es)
  }
  if (payload.type) {
    formData.append('type', payload.type)
  }

  if (payload.start_date) {
    const formatted = formatDateToYMD(payload.start_date)
    formData.append('start_date', formatted)
  }

  if (payload.end_date) {
    const formatted = formatDateToYMD(payload.end_date)
    formData.append('end_date', formatted)
  } else {
    formData.append('end_date', '')
  }

  
  if (payload.price) {
    formData.append('price', payload.price.toString())
  }
  if (payload.featured_position) {
    formData.append('featured_position', payload.featured_position.toString())
  }
  if (payload.max_students) {
    formData.append('max_students', payload.max_students.toString())
  }
  if (payload.skills) {
    payload.skills.forEach((id) => formData.append('skills[]', id.toString()))
  }
  // File uploads
  if ('cover_image' in payload && payload.cover_image) {
    formData.append('cover_image', payload.cover_image)
  }

  // Skills
  if ('skills' in payload && payload.skills) {
    payload.skills.forEach((id) => formData.append('skills[]', id.toString()))
  }

  if (!isCreate) {
    formData.append('_method', 'PUT')
  }

  return formData
}
