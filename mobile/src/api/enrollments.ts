import { apiClient } from './client';
import { API } from './endpoints';
import type { ApiResponse, Enrollment } from '@/types';

export interface EnrollPayload {
  training_type?: 'online' | 'physical_monthly' | 'physical_quarterly';
  payment_type?: 'full' | 'installment';
  installments?: number;
}

export const enrollmentsApi = {
  myCourses() {
    return apiClient.get<ApiResponse<Enrollment[]>>(API.MY_COURSES);
  },

  getEnrollment(id: number) {
    return apiClient.get<ApiResponse<Enrollment>>(API.ENROLLMENT(id));
  },

  enroll(courseId: number, payload?: EnrollPayload) {
    return apiClient.post<ApiResponse<{
      enrollment: Enrollment;
      payment_url: string | null;
      payment_reference: string | null;
      is_free: boolean;
    }>>(API.ENROLL(courseId), payload ?? {});
  },

  verifyPayment(reference: string) {
    return apiClient.post<ApiResponse<{ enrollment: Enrollment }>>(
      API.PAYMENT_VERIFY, { reference }
    );
  },
};
