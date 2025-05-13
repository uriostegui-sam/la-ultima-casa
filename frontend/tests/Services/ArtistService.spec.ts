import { beforeEach, describe, expect, it, vi } from 'vitest'
import { getMockAxiosInstance } from '../setup'
import Artist from '../../src/Services/DataLayers/Artist'

describe('ArtistService', () => {
  const mockArtist = {
    id: 1,
    user_id: 1,
    name: 'Test Artist',
    profile_image_url: '/storage/artists/test.jpg',
    skills: [{ id: 1, name: 'Painting' }],
  }

  let axiosInstance: any

  beforeEach(async () => {
    // fresh mock instance for each test
    axiosInstance = await getMockAxiosInstance()
    vi.clearAllMocks()

    // default mock implementation
    axiosInstance.post.mockResolvedValue({
      data: mockArtist,
      status: 201,
      statusText: 'Created',
      headers: {},
      config: {},
    })

    axiosInstance.get.mockResolvedValue({
      data: { data: [mockArtist], meta: {} },
    })
  })

  it('creates artist with FormData', async () => {
    const mockFile = new File([''], 'test.jpg')
    const payload = {
      user_id: 1,
      minibio: { en: 'English', es: 'Spanish' },
      bio: { en: 'Bio', es: 'Biografía' },
      social_links: { instagram: '@test' },
      skills: [1, 2],
      profile_image: mockFile,
    }

    const result = await Artist.createArtist(payload)

    expect(axiosInstance.post).toHaveBeenCalledWith('/artists', expect.any(FormData), {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })

    const formData = axiosInstance.post.mock.calls[0][1] as FormData
    expect(formData.get('minibio')).toBe(JSON.stringify({ en: 'English', es: 'Spanish' }))
    expect(formData.getAll('skills[]')).toEqual(['1', '2'])
    expect(result).toEqual(mockArtist)
  })

  it('updates artist with FormData', async () => {
    const payload = {
      id: 1,
      name: 'Updated Artist',
      skills: [1, 2, 3],
      profile_image: new File([''], 'test.jpg'),
    }

    const result = await Artist.updateArtist(payload.id, payload)

    expect(axiosInstance.post).toHaveBeenCalledWith('/artists/1', expect.any(FormData), {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
  })

    const formData = axiosInstance.post.mock.calls[0][1] as FormData
    expect(formData.get('_method')).toBe('PUT')
    expect(formData.get('name')).toBe('Updated Artist')
    expect(formData.getAll('skills[]')).toEqual(['1', '2', '3'])
    expect(result).toEqual(mockArtist)
  })

  it('fetches artists with params', async () => {
    const params = { page: 1, per_page: 10 }
    const mockResponse = {
      data: [mockArtist],
      meta: { current_page: 1, last_page: 1 },
    }

    axiosInstance.get.mockResolvedValueOnce({ data: mockResponse })

    const result = await Artist.getArtists(params)

    expect(axiosInstance.get).toHaveBeenCalledWith('/artists', { params })
    expect(result).toEqual(mockResponse)
  })
})
