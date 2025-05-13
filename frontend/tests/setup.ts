import { vi } from 'vitest'
import type { AxiosInstance } from 'axios'

const setupTestEnvironment = () => {
  // Auth store mock
  vi.mock('@/stores/AuthStore', () => ({
    useAuthStore: () => ({
      token: 'test-token',
      logout: vi.fn(),
    }),
  }))

  // Axios instance mock - return a fresh mock for each test
  vi.mock('@/Services/DataLayers/AxiosInstance', () => {
    const mockAxios: Partial<AxiosInstance> = {
      post: vi.fn(),
      get: vi.fn(),
      put: vi.fn(),
      patch: vi.fn(),
      delete: vi.fn(), // Add this line
      interceptors: {
        request: { use: vi.fn() },
        response: { use: vi.fn() },
      },
    }
    return { default: mockAxios }
  })
}

setupTestEnvironment()

export const getMockAxiosInstance = async () => {
  const { default: axiosInstance } = await import('../src/Services/DataLayers/AxiosInstance')
  return axiosInstance
}
