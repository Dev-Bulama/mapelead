import { create } from 'zustand';
import { authApi } from '@/api/auth';
import { authEventEmitter, extractApiError } from '@/api/client';
import { secureStorage } from '@/utils/secureStorage';
import type { LoginPayload, RegisterPayload, User } from '@/types';

interface AuthState {
  user:           User | null;
  token:          string | null;
  isLoading:      boolean;
  isInitialized:  boolean;
  error:          string | null;

  // Actions
  initialize:     () => Promise<void>;
  login:          (payload: LoginPayload) => Promise<void>;
  register:       (payload: RegisterPayload) => Promise<void>;
  logout:         () => Promise<void>;
  refreshUser:    () => Promise<void>;
  clearError:     () => void;
}

export const useAuthStore = create<AuthState>((set, get) => {
  // Listen for 401s from the Axios interceptor
  authEventEmitter.on('unauthenticated', () => {
    set({ user: null, token: null });
  });

  return {
    user:          null,
    token:         null,
    isLoading:     false,
    isInitialized: false,
    error:         null,

    initialize: async () => {
      try {
        const [token, user] = await Promise.all([
          secureStorage.getToken(),
          secureStorage.getUser<User>(),
        ]);

        if (token && user) {
          set({ token, user, isInitialized: true });
          // Silently refresh user data in background
          get().refreshUser().catch(() => {/* ignore on init */});
        } else {
          set({ isInitialized: true });
        }
      } catch {
        set({ isInitialized: true });
      }
    },

    login: async (payload) => {
      set({ isLoading: true, error: null });
      try {
        const response = await authApi.login(payload);
        const { token, user } = response.data.data;
        await Promise.all([
          secureStorage.setToken(token),
          secureStorage.setUser(user),
        ]);
        set({ token, user, isLoading: false });
      } catch (err) {
        set({ isLoading: false, error: extractApiError(err) });
        throw err;
      }
    },

    register: async (payload) => {
      set({ isLoading: true, error: null });
      try {
        const response = await authApi.register(payload);
        const { token, user } = response.data.data;
        await Promise.all([
          secureStorage.setToken(token),
          secureStorage.setUser(user),
        ]);
        set({ token, user, isLoading: false });
      } catch (err) {
        set({ isLoading: false, error: extractApiError(err) });
        throw err;
      }
    },

    logout: async () => {
      set({ isLoading: true });
      try {
        await authApi.logout();
      } catch {
        // Proceed even if the server request fails
      } finally {
        await secureStorage.clearAll();
        set({ user: null, token: null, isLoading: false, error: null });
      }
    },

    refreshUser: async () => {
      try {
        const response = await authApi.me();
        const user = response.data.data;
        await secureStorage.setUser(user);
        set({ user });
      } catch {
        // Silently ignore refresh failures
      }
    },

    clearError: () => set({ error: null }),
  };
});

// Selectors
export const selectIsAuthenticated = (s: AuthState) => !!s.token && !!s.user;
export const selectUser            = (s: AuthState) => s.user;
export const selectIsStudent       = (s: AuthState) => s.user?.roles?.includes('student') ?? false;
