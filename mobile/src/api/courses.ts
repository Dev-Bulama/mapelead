import { apiClient } from './client';
import { API } from './endpoints';
import type { ApiResponse, Course, CourseCategory, PaginationMeta } from '@/types';

export interface CourseFilters {
  category?: string;
  type?: string;
  level?: string;
  search?: string;
  min_price?: number;
  max_price?: number;
  free_only?: boolean;
  has_certificate?: boolean;
  sort?: 'popular' | 'newest' | 'price_low' | 'price_high' | 'rating';
  page?: number;
}

export const coursesApi = {
  list(filters?: CourseFilters) {
    return apiClient.get<ApiResponse<{ courses: Course[]; meta: PaginationMeta }>>(
      API.COURSES, { params: filters }
    );
  },

  featured() {
    return apiClient.get<ApiResponse<Course[]>>(API.COURSES_FEATURED);
  },

  search(q: string) {
    return apiClient.get<ApiResponse<{ courses: Course[]; total: number; query: string }>>(
      API.COURSES_SEARCH, { params: { q } }
    );
  },

  detail(slug: string) {
    return apiClient.get<ApiResponse<Course>>(API.COURSE_DETAIL(slug));
  },

  categories() {
    return apiClient.get<ApiResponse<CourseCategory[]>>(API.CATEGORIES);
  },
};
