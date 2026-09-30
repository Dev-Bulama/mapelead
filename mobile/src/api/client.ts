import axios, { AxiosError, AxiosInstance, InternalAxiosRequestConfig } from 'axios';
import Constants from 'expo-constants';
import { secureStorage } from '@/utils/secureStorage';

const BASE_URL: string =
  (Constants.expoConfig?.extra?.apiBaseUrl as string | undefined) ??
  'https://mapelead.org/api/v1';

export const apiClient: AxiosInstance = axios.create({
  baseURL: BASE_URL,
  timeout: 15000,
  headers: {
    'Accept':       'application/json',
    'Content-Type': 'application/json',
    'X-App-Source': 'mobile',
  },
});

// ── Request interceptor: attach stored token ──────────────────────────────────
apiClient.interceptors.request.use(
  async (config: InternalAxiosRequestConfig) => {
    const token = await secureStorage.getToken();
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error),
);

// ── Response interceptor: normalise errors, handle 401 ───────────────────────
apiClient.interceptors.response.use(
  (response) => response,
  async (error: AxiosError) => {
    if (error.response?.status === 401) {
      // Token expired / invalid — clear stored auth and let the store handle redirect
      await secureStorage.clearAll();
      // Signal to auth store via a custom event (store listens in its persist middleware)
      authEventEmitter.emit('unauthenticated');
    }
    return Promise.reject(error);
  },
);

// Simple in-process event emitter (no native deps needed)
type Listener = () => void;
class AuthEventEmitter {
  private listeners: Listener[] = [];
  emit(_event: 'unauthenticated') {
    this.listeners.forEach((l) => l());
  }
  on(_event: 'unauthenticated', listener: Listener) {
    this.listeners.push(listener);
    return () => { this.listeners = this.listeners.filter((l) => l !== listener); };
  }
}
export const authEventEmitter = new AuthEventEmitter();

// ── Typed API error helper ────────────────────────────────────────────────────
export interface ApiValidationError {
  message: string;
  errors?: Record<string, string[]>;
}

export function extractApiError(error: unknown): string {
  if (axios.isAxiosError(error)) {
    const data = error.response?.data as ApiValidationError | undefined;
    if (data?.errors) {
      const first = Object.values(data.errors)[0];
      return Array.isArray(first) ? first[0] : String(first);
    }
    if (data?.message) return data.message;
    if (error.code === 'ECONNABORTED') return 'Request timed out. Check your connection.';
    if (!error.response) return 'No internet connection. Please try again.';
  }
  return 'Something went wrong. Please try again.';
}
