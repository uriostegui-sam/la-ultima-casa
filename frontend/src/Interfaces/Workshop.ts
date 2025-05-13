import type { Artist } from './Artist'
import type { Skill } from './Skill'

type WorkshopType = 'permanent' | 'temporary'

export interface Workshop {
    id: number;
    artist_id: number;
    artist?: Artist;
    title: string;
    description: string;
    type: WorkshopType;
    start_date: string;
    end_date: string;
    price: number;
    max_students: number;
    cover_image_path?: string;
    cover_image_url?: string;
    skills?: Skill[];
    translations?: {
      title: Record<string, string>;
      description: Record<string, string>;
    }
}

export interface WorkshopCreatePayload {
  artist_id: number;
  title: Record<string, string>;
  description: Record<string, string>;
  type: WorkshopType;
  start_date: string;
  end_date: string;
  price: number;
  max_students: number;
  cover_image?: File;
  skills?: number[];
}

export interface WorkshopUpdatePayload extends Partial<WorkshopCreatePayload> {
  id: number;
}
