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
  artist: Artist;
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
  creation_date?: string;
  images: File[];
}

export interface ArtworkUpdatePayload {
  title?: string;
  description?: Record<string, string>;
  dimensions?: {
    width?: number;
    height?: number;
    depth?: number;
  }
  creation_date?: string;
  new_images?: File[];
}