import type { NewsCreatePayload, NewsUpdatePayload } from '@/shared/Interfaces/News'

export function buildNewsFormData(
  payload: NewsCreatePayload | NewsUpdatePayload,
  isCreate: boolean = true,
): FormData {
  const formData = new FormData()

  if (payload.title?.es) {
    formData.append('title[es]', payload.title.es)
  }
  if (payload.title?.en) {
    formData.append('title[en]', payload.title.en)
  }

  if (payload.content?.es) {
    formData.append('content[es]', payload.content.es)
  }
  if (payload.content?.en) {
    formData.append('content[en]', payload.content.en)
  }
  if (typeof payload.published === 'boolean') {
    formData.append('published', payload.published ? '1' : '0');
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
