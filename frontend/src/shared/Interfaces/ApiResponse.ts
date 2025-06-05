import type { PaginationMeta } from "./PaginationMeta";

export interface ApiResponse<T> {
    data: T;
    message?: string;
    meta?: PaginationMeta;
  }