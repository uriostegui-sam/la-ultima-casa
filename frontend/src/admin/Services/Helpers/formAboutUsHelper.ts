import type { AboutUsCreatePayload, AboutUsUpdatePayload } from '@/shared/Interfaces/AboutUs'

export function buildAboutUsFormData(
  payload: AboutUsCreatePayload | AboutUsUpdatePayload,
  isCreate: boolean = true,
): FormData {
  const formData = new FormData()

  if (payload.number) {
    formData.append('number', payload.number.toString())
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

  // File uploads
  if ('cover_image' in payload && payload.cover_image) {
    formData.append('cover_image', payload.cover_image)
  }

  if (!isCreate) {
    formData.append('_method', 'PUT')
  }

  return formData
}
