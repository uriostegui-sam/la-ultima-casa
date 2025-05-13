import { describe, it, expect, vi, beforeEach } from 'vitest'
import NewsService from '../../src/Services/DataLayers/News'
import axiosInstance from '../../src/Services/DataLayers/AxiosInstance'

describe('NewsService', () => {
  const mockNews = {
    id: 1,
    title: 'Test News',
    content: 'Test content',
    image_url: '/storage/news/test.jpg',
  }

  beforeEach(() => {
    vi.clearAllMocks()
    vi.mocked(axiosInstance.post).mockResolvedValue({ data: mockNews })
    vi.spyOn(axiosInstance, 'get').mockResolvedValue({
      data: { data: [mockNews], meta: {} },
    })
  })

  it('creates news with image', async () => {
    const mockFile = new File([''], 'test.jpg')
    const result = await NewsService.createNews({
      title: { en: 'English', es: 'Spanish' },
      content: { en: 'Content', es: 'Contenido' },
      image: mockFile,
    })

    const formData = (vi.mocked(axiosInstance.post).mock.calls[0][1]) as FormData
    expect(formData.get('title')).toBe(JSON.stringify({ en: 'English', es: 'Spanish' }))
    expect(formData.get('image')).toBe(mockFile)
    expect(result).toEqual(mockNews)
  })

  it('handles date filters', async () => {
    await NewsService.getNews({
      published_from: '2024-01-01',
      published_to: '2024-12-31',
    })

    expect(axiosInstance.get).toHaveBeenCalledWith('/news', {
      params: {
        published_from: '2024-01-01',
        published_to: '2024-12-31',
      },
    })
  })
})
