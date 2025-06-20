import type { Artist } from './Artist'
import type { Skill } from './Skill'

type WorkshopType = 'permanent' | 'temporary'

export interface Workshop {
  id: number
  artist_id: number
  artist?: Artist
  title: {
    en?: string
    es?: string
  }
  description: {
    en?: string
    es?: string
  }
  type: WorkshopType
  start_date: Date
  end_date: Date | null
  price: number
  max_students: number
  cover_image_path?: string
  cover_image_url?: string
  skills?: Skill[]
  featured_position?: number | false
}

export interface WorkshopCreatePayload {
  artist_id: number
  title: Record<string, string>
  description: Record<string, string>
  type: WorkshopType
  start_date: Date
  end_date: Date | null
  price: number
  max_students: number
  cover_image?: File
  skills?: number[],
  featured_position?: number | false
}

export interface WorkshopUpdatePayload extends Partial<WorkshopCreatePayload> {
  id: number
}
