export interface News {
  id: number
  title: string
  content: string
  image_path?: string
  image_url?: string
  published_at?: string
  translations?: {
    title?: Record<string, string>
    content?: Record<string, string>
  }
}

export interface NewsCreatePayload {
  title: Record<string, string>
  content: Record<string, string>
  published_at?: string
  image?: File
}

export interface NewsUpdatePayload extends Partial<NewsCreatePayload> {
  id: number
}
