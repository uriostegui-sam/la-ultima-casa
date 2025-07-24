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

export interface UserPasswordUpdate {
  id: number;
  password: string;
  newPassword: string;
  newPassword_confirmation: string;
}

export interface UserPasswordUpdatePayload {
  id: number;
  password: string;
  newPassword: string;
  newPassword_confirmation: string;
}

export interface PasswordReset {
  id: number;
  id_admin: number;
  token: string;
}

export interface PasswordResetPayload {
  token: string;
  new_password: string;
  new_password_confirmation: string;
}