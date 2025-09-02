export interface AboutUs {
  id: number
  number: string
  cover_image: string
  logo_header: string
  logo_footer: string
  mail: string
  address: {
    text: string
    map: string
  }
  description: {
    en: string
    es: string
  }
}

export interface AboutUsCreatePayload {
  number: string
  mail: string
  address: Record<string, string>
  description: Record<string, string>
  cover_image?: File
  logo_header?: File
  logo_footer?: File
}

export interface AboutUsUpdatePayload extends Partial<AboutUsCreatePayload> {
  id: number
}
