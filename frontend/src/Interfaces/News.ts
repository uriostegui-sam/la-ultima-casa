export interface News {
  id: number;
  title: string;
  content: string;
  image_path?: string;
  image_url?: string;
  published_at?: string;
  translations?: {
    title?: Record<string, string>;
    content?: Record<string, string>;
  }
}