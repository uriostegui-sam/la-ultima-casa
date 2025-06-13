export interface AboutUs {
  id: number
  number: number
  cover_image: string
  mail: string
  address: {
    text: string
    map: string
  }
}

export interface AboutUsCreatePayload {
  number: number
  mail: string
  address: Record<string, string>
  cover_image?: File
}

export interface AboutUsUpdatePayload extends Partial<AboutUsCreatePayload> {
  id: number
}
