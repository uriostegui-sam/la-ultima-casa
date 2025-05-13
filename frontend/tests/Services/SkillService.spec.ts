import { beforeEach, describe, expect, it, vi } from 'vitest'
import axiosInstance from '../../src/Services/DataLayers/AxiosInstance'
import SkillService from '../../src/Services/DataLayers/Skill'

describe('SkillService', () => {
  const mockSkill = {
    id: 1,
    name: 'Test Skill',
  }

  beforeEach(() => {
    vi.clearAllMocks()
    vi.mocked(axiosInstance.post).mockResolvedValue({ data: mockSkill })
    vi.spyOn(axiosInstance, 'get').mockResolvedValue({
      data: { data: [mockSkill], meta: {} },
    })
  })

  it('creates skill', async () => {
    const result = await SkillService.create({
      name: 'Test Skill',
    })

    expect(axiosInstance.post).toHaveBeenCalledWith('/skills', {
      name: 'Test Skill',
    })

    expect(result).toEqual(mockSkill)
  })

  it('updates skill', async () => {
    vi.mocked(axiosInstance.put).mockResolvedValue({ data: mockSkill })

    const result = await SkillService.update(1, {
      name: 'Updated Skill',
    })

    expect(axiosInstance.put).toHaveBeenCalledWith('/skills/1', {
      name: 'Updated Skill',
    })

    expect(result).toEqual(mockSkill)
  })

  it('fetches skills', async () => {
    const mockResponse = {
      data: [mockSkill],
      meta: { total: 1 },
    }

    vi.mocked(axiosInstance.get).mockResolvedValue({
      data: mockResponse,
    })

    const result = await SkillService.getPaginated()

    expect(axiosInstance.get).toHaveBeenCalledWith('/skills', { params: {} })
    expect(result).toEqual(mockResponse)
  })
})