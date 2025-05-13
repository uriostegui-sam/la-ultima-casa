import { beforeEach, describe, expect, it, vi } from 'vitest'
import { getMockAxiosInstance } from '../setup'
import Artwork from '../../src/Services/DataLayers/Artwork'
import type { ApiResponse } from '../../src/Interfaces/ApiResponse'
import type { Artwork as ArtworkModel } from '../../src/Interfaces/Artwork'

describe('ArtworkService', () => {
  const mockArtwork = {
    id: 1,
    title: 'Test Artwork',
    description: 'Test description',
    dimensions: { width: 100, height: 100 },
    creation_date: '2024-01-01',
    artist: { id: 1, name: 'Test Artist' },
    images: [
      { id: 1, path: 'path1.jpg', url: 'url1.jpg', is_primary: true, order: 0 },
      { id: 2, path: 'path2.jpg', url: 'url2.jpg', is_primary: false, order: 1 },
    ],
  }

  const mockResponse = {
    data: mockArtwork,
    status: 200,
    statusText: 'OK',
    headers: {},
    config: {},
  }

  let axiosInstance: any

  beforeEach(async () => {
    // fresh mock instance for each test
    axiosInstance = await getMockAxiosInstance()
    vi.clearAllMocks()

    // default mock implementation
    axiosInstance.post.mockResolvedValue({
      data: mockArtwork,
      status: 201,
      statusText: 'Created',
      headers: {},
      config: {},
    })
  })

  it('creates artwork with images', async () => {
    const mockFile = new File([''], 'test.jpg')
    const payload = {
      artist_id: 1,
      title: 'Test Artwork',
      description: { en: 'English description' },
      dimensions: { width: 100, height: 100 },
      images: [mockFile],
    }

    const result = await Artwork.createArtwork(payload)

    expect(axiosInstance.post).toHaveBeenCalledWith('/artworks', expect.any(FormData), {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    const formData = axiosInstance.post.mock.calls[0][1] as FormData
    expect(formData.get('title')).toBe('Test Artwork')
    expect(formData.get('description')).toBe(JSON.stringify(payload.description))
    expect(formData.getAll('images[]')).toHaveLength(1)
    expect(result).toEqual(mockArtwork)
  })

  it('updates artwork with new images', async () => {
    const mockFile = new File([''], 'new.jpg')
    const payload = {
      title: 'Updated Title',
      new_images: [mockFile],
    }

    const result = await Artwork.updateArtwork(1, payload)

    expect(axiosInstance.post).toHaveBeenCalledWith(
      '/artworks/1?_method=PUT',
      expect.any(FormData),
      {
        headers: { 'Content-Type': 'multipart/form-data' },
      },
    )

    const formData = axiosInstance.post.mock.calls[0][1] as FormData
    expect(formData.get('title')).toBe('Updated Title')
    expect(formData.getAll('images[]')).toHaveLength(1)
    expect(result).toEqual(mockArtwork)
  })

  it('handles partial updates', async () => {
    const payload = { title: 'Partial Update' }
    await Artwork.updateArtwork(1, payload)

    const formData = axiosInstance.post.mock.calls[0][1] as FormData
    expect(formData.get('title')).toBe('Partial Update')
    expect(formData.has('images[]')).toBe(false)
  })

  describe('image management', () => {
    it('deletes an image', async () => {
      axiosInstance.delete.mockResolvedValue({ status: 204 })
      await Artwork.deleteImage(1, 1)
      expect(axiosInstance.delete).toHaveBeenCalledWith('/artworks/1/images/1')
    })

    it('sets primary image', async () => {
      axiosInstance.patch.mockResolvedValue(mockResponse)
      const result = await Artwork.setPrimaryImage(1, 2)

      expect(axiosInstance.patch).toHaveBeenCalledWith('/artworks/1/images/2/set-primary')
      expect(result).toEqual(mockArtwork)
    })

    it('reorders images', async () => {
      axiosInstance.patch.mockResolvedValue(mockResponse)
      const result = await Artwork.reorderImages(1, [2, 1])

      expect(axiosInstance.patch).toHaveBeenCalledWith('/artworks/1/reorder-images', {
        image_ids: [2, 1],
      })
      expect(result).toEqual(mockArtwork)
    })
  })

  describe('getArtworks', () => {
    it('fetches paginated artworks', async () => {
      const mockApiResponse: ApiResponse<ArtworkModel[]> = {
        data: [mockArtwork],
        meta: { total: 1, per_page: 10 },
      }

      axiosInstance.get.mockResolvedValue({ data: mockApiResponse })

      const result = await Artwork.getArtworks()
      expect(result.data).toHaveLength(1)
      expect(axiosInstance.get).toHaveBeenCalledWith('/artworks', { params: {} })
    })

    it('handles filters', async () => {
      axiosInstance.get.mockResolvedValue({
        data: {
          data: [],
          meta: {},
        },
      })

      await Artwork.getArtworks({ artist_id: 1, year: 2024 })

      expect(axiosInstance.get).toHaveBeenCalledWith('/artworks', {
        params: { artist_id: 1, year: 2024 },
      })
    })
  })
})
