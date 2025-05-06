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
  name: string;
  profile_image?: string;
  profile_image_url?: string;
  minibio: string;
  bio: string;
  skills: Skill[];
  social_links: SocialLinks;
  artworks?: Artwork[];
}

export interface ArtistCreatePayload {
  name: string;
  user_id: number;
  profile_image?: File;
  minibio: string;
  bio: string;
  skills?: number[];
  social_links: SocialLinks;
}

export interface ArtistUpdatePayload extends Partial<ArtistCreatePayload> {}
