import type { Artist } from './Artist'
import type { ArtworkImage } from './ArtworkImage'

export interface Artwork {
  id: number;
  artist_id: number;
  title: string;
  description: {
    en?: string
    es?: string
  }
  dimensions: {
    width?: number;
    height?: number;
    depth?: number;
  }
  creation_date: Date;
  artist: Artist | null;
  primary_image?: ArtworkImage | null;
  order?: number;
  images: ArtworkImage[];
}

export interface ArtworkCreatePayload {
  artist_id: number;
  title: string;
  description?: Record<string, string>;
  dimensions?: {
    width?: number;
    height?: number;
    depth?: number;
  }
  order?: number;
  creation_date?: Date | string;
  new_images: File[];
}

export interface ArtworkUpdatePayload {
  id: number
  artist_id: number;
  title?: string;
  description?: Record<string, string>;
  dimensions?: {
    width?: number;
    height?: number;
    depth?: number;
  }
  order?: number;
  creation_date?: Date | string;
  images_to_delete: number[];
  new_images?: File[];
}