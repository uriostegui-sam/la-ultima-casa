import type { Artist } from "./Artist";

export interface User {
  id?: number | string;
  firstName: string;
  lastName: string;
  email: string;
  role: 'artist' | 'admin';
  artist_id?: number; 
  artist?: Artist;
}

export interface AuthState {
  user: User | null;
  token: string | null;
}