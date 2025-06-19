export interface News {
  id: number
  title: {
    en?: string
    es?: string
  }
  content: {
    en?: string
    es?: string
  }
  image_url?: string
  published?: boolean
}

export interface NewsCreatePayload {
  title: Record<string, string>
  content: Record<string, string>
  published?: boolean
  cover_image?: File
}

export interface NewsUpdatePayload extends Partial<NewsCreatePayload> {
  id: number
}
