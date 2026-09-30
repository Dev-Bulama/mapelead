import { apiClient } from './client';
import { API } from './endpoints';
import type { ApiResponse, LoginPayload, RegisterPayload, User } from '@/types';

export const authApi = {
  login(payload: LoginPayload) {
    return apiClient.post<ApiResponse<{ token: string; user: User }>>(API.AUTH_LOGIN, payload);
  },

  register(payload: RegisterPayload) {
    return apiClient.post<ApiResponse<{ token: string; user: User }>>(API.AUTH_REGISTER, payload);
  },

  logout() {
    return apiClient.post<ApiResponse<[]>>(API.AUTH_LOGOUT);
  },

  me() {
    return apiClient.get<ApiResponse<User>>(API.AUTH_ME);
  },

  resendVerification() {
    return apiClient.post<ApiResponse<[]>>(API.AUTH_RESEND_VERIFY);
  },

  changePassword(payload: { current_password: string; password: string; password_confirmation: string }) {
    return apiClient.post<ApiResponse<[]>>(API.AUTH_CHANGE_PASSWORD, payload);
  },
};
