import { describe, it, expect, vi, beforeEach } from 'vitest'
import Workshop from '../../src/Services/DataLayers/Workshop'
import type { WorkshopCreatePayload } from '../../src/Interfaces/Workshop'
import { getMockAxiosInstance } from '../setup'

describe('WorkshopService', () => {
  const mockWorkshop = {
    id: 1,
    artist_id: 1,
    title: 'Test Workshop',
    description: 'Test description',
    type: 'temporary',
    start_date: '2024-01-01',
    end_date: '2024-01-10',
    price: 100,
    max_students: 20,
    cover_image_url: '/storage/workshops/test.jpg',
    skills: [{ id: 1, name: 'Painting' }],
  }

  let axiosInstance: any

  beforeEach(async () => {
    axiosInstance = await getMockAxiosInstance()
    vi.clearAllMocks()
    axiosInstance.post.mockResolvedValue({ data: mockWorkshop })
    axiosInstance.get.mockResolvedValue({
      data: { data: [mockWorkshop], meta: {} },
    })

    axiosInstance.post.mockResolvedValue({
      data: mockWorkshop,
      status: 201,
      statusText: 'Created',
      headers: {},
      config: {},
    })
  })

  it('creates workshop with FormData', async () => {
    const mockFile = new File([''], 'test.jpg')
    const result = await Workshop.createWorkshop({
      artist_id: 1,
      title: { en: 'English', es: 'Spanish' },
      description: { en: 'Description', es: 'Descripción' },
      type: 'temporary',
      start_date: '2024-01-01',
      end_date: '2024-01-10',
      price: 100,
      max_students: 20,
      cover_image: mockFile,
      skills: [1, 2],
    })

    const formData = axiosInstance.post.mock.calls[0][1] as FormData
    expect(formData.get('title')).toBe(JSON.stringify({ en: 'English', es: 'Spanish' }))
    expect(formData.getAll('skills[]')).toEqual(['1', '2'])
    expect(result).toEqual(mockWorkshop)
  })

  it('updates workshop with partial data', async () => {
    const mockFile = new File([''], 'updated.jpg')
    const payload = {
      id: 1,
      title: { en: 'Updated Title', es: 'Título Actualizado' },
      max_students: 30,
      cover_image: mockFile
    }
  
    const updatedWorkshop = { 
      ...mockWorkshop, 
      title: payload.title,
      max_students: 30,
      cover_image_url: '/storage/workshops/updated.jpg'
    }
    axiosInstance.post.mockResolvedValue({ data: updatedWorkshop })
  
    const result = await Workshop.updateWorkshop(payload)
  
    expect(axiosInstance.post).toHaveBeenCalledWith(
      '/workshops/1',
      expect.any(FormData),
      {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }
    )
  
    const formData = axiosInstance.post.mock.calls[0][1] as FormData
    expect(formData.get('_method')).toBe('PUT')
    expect(formData.get('title')).toBe(JSON.stringify(payload.title))
    expect(formData.get('max_students')).toBe('30')
    expect(formData.get('cover_image')).toBe(mockFile)
    // Verify fields NOT included aren't accidentally sent
    expect(formData.has('description')).toBe(false)
    expect(formData.has('price')).toBe(false)
  
    expect(result).toEqual(updatedWorkshop)
  })

  it('fetches workshops', async () => {
    const params = { page: 1, per_page: 10 }
    const mockResponse = {
      data: [mockWorkshop],
      meta: { current_page: 1, last_page: 1 },
    }

    axiosInstance.get.mockResolvedValue({ data: mockResponse })

    const result = await Workshop.getWorkshops(params)

    expect(axiosInstance.get).toHaveBeenCalledWith('/workshops', { params })
    expect(result).toEqual(mockResponse)
  })
})