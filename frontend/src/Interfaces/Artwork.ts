import type { Artist } from './Artist'
import type { ArtworkImage } from './ArtworkImage'

export interface Artwork {
  id: number;
  title: string;
  description: string;
  dimensions: {
    width?: number;
    height?: number;
    depth?: number;
  }
  creation_date: string;
  artist: Artist;
  images: ArtworkImage[];
  translations?: {
    title?: Record<string, string>;
    description?: Record<string, string>;
  }
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