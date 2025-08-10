import { describe, it, expect, beforeEach, vi } from 'vitest'
import { getFromStorage, setToStorage } from '../../../../src/shared/services/Helpers/LocalStorage'

describe('localStorage helpers', () => {
  beforeEach(() => {
    localStorage.clear()
  })

  it('sets a value in localStorage', () => {
    setToStorage('key', 'value')
    expect(localStorage.getItem('key')).toBe('value')
  })

  it('gets a value from localStorage', () => {
    localStorage.setItem('key', 'value')
    const result = getFromStorage('key')
    expect(result).toBe('value')
  })

  it('returns null for missing keys', () => {
    const result = getFromStorage('nonexistent')
    expect(result).toBeNull()
  })
})
