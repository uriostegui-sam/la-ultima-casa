import type { AboutUsCreatePayload, AboutUsUpdatePayload } from '@/shared/Interfaces/AboutUs'

export function buildAboutUsFormData(
  payload: AboutUsCreatePayload | AboutUsUpdatePayload,
  isCreate: boolean = true,
): FormData {
  const formData = new FormData()

  if (payload.number) {
    formData.append('number', payload.number)
  }
  if (payload.mail) {
    formData.append('mail', payload.mail)
  }
  if (payload.address?.text) {
    formData.append('address[text]', payload.address.text)
  }
  if (payload.address?.map) {
    formData.append('address[map]', payload.address.map)
  }
   if (payload.description?.en) {
    formData.append('description[en]', payload.description.en)
  }
  if (payload.description?.es) {
    formData.append('description[es]', payload.description.es)
  }

  // File uploads
  if ('cover_image' in payload && payload.cover_image) {
    formData.append('cover_image', payload.cover_image)
  }
  if ('logo' in payload && payload.logo) {
    formData.append('logo', payload.logo)
  }

  if (!isCreate) {
    formData.append('_method', 'PUT')
  }

  return formData
}
