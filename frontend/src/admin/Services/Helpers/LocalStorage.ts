export function getFromStorage(key: string): string | null {
  return localStorage.getItem(key)
}

export function setToStorage(key: string, value: string): void {
  localStorage.setItem(key, value)
}