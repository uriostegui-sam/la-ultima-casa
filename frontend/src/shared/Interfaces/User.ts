export interface User {
  id?: number | string;
  firstName: string;
  lastName: string;
  email: string;
  role: 'artist' | 'admin';
}

export interface AuthState {
  user: User | null;
  token: string | null;
}