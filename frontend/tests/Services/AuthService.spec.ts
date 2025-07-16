import { beforeEach, describe, expect, it, vi } from 'vitest'
import { getMockAxiosInstance } from '../setup'
import createAuthService from '../../src/shared/services/DataLayers/AuthService'

describe('AuthService', () => {
  let axiosInstance: any
  let AuthService: ReturnType<typeof createAuthService>

  beforeEach(async () => {
    axiosInstance = await getMockAxiosInstance()
    vi.clearAllMocks()

    AuthService = createAuthService(axiosInstance)
  })

  it('sends password update request', async () => {
    const mockPayload = {
      id: 1,
      password: 'oldpass',
      newPassword: 'newpass123',
      newPasswordAgain: 'newpass123',
    }

    const mockResponse = {
      data: mockPayload,
    }

    axiosInstance.post.mockResolvedValueOnce(mockResponse)

    const result = await AuthService.updatePassword(mockPayload)

    expect(axiosInstance.post).toHaveBeenCalledWith(
      '/auth/update-password',
      mockPayload
    )

    expect(result).toEqual(mockPayload)
  })
})
