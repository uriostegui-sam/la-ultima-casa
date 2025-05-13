import type { Artwork } from './Artwork'
import type { Skill } from './Skill'

export interface SocialLinks {
  website?: string;
  instagram?: string;
  twitter?: string;
  flickr?: string;
}

export interface Artist {
  id: number;
  user_id: number;
  user: {
    id: number;
    name: string;
    email: string;
  };
  name: string;
  profile_image?: string;
  profile_image_url?: string;
  minibio: string;
  bio: string;
  skills: Skill[];
  social_links: SocialLinks;
  artworks?: Artwork[];
  translations?: {
    minibio: Record<string, string>;
    bio: Record<string, string>;
  }
}

export interface ArtistCreatePayload {
  user_id: number;
  profile_image?: File;
  minibio: Record<string, string>;
  bio: Record<string, string>;
  skills?: number[];
  social_links: SocialLinks;
}

export interface ArtistUpdatePayload {
  id: number
  name?: string
  user_id?: number
  minibio?: Record<string, string>
  bio?: Record<string, string>
  skills?: number[]
  social_links?: SocialLinks
  profile_image?: File
}