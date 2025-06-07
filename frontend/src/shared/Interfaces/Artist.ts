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
  user: Record<string, string>;
  name: string;
  profile_image?: string;
  profile_image_url?: string;
  minibio: {
    en?: string
    es?: string
  }
  bio: {
    en?: string
    es?: string
  }
  skills: Skill[];
  social_links: SocialLinks;
  artworks?: Artwork[];
}

export interface ArtistCreatePayload {
  user_id: number;
  user: Record<string, string>;
  profile_image?: File;
  minibio: Record<string, string>;
  bio: Record<string, string>;
  skills?: number[];
  social_links: SocialLinks;
}

export interface ArtistUpdatePayload {
  id: number
  user: Record<string, string>;
  user_id?: number
  minibio?: Record<string, string>
  bio?: Record<string, string>
  skills?: number[]
  social_links?: SocialLinks
  profile_image?: File
}