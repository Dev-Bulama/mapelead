import * as SecureStore from 'expo-secure-store';

const TOKEN_KEY        = 'mapelead_auth_token';
const USER_KEY         = 'mapelead_user';
const ONBOARDING_KEY   = 'mapelead_onboarding_done';
const PUSH_ENABLED_KEY = 'mapelead_push_enabled';

export const secureStorage = {
  async setToken(token: string): Promise<void> {
    await SecureStore.setItemAsync(TOKEN_KEY, token);
  },

  async getToken(): Promise<string | null> {
    return SecureStore.getItemAsync(TOKEN_KEY);
  },

  async removeToken(): Promise<void> {
    await SecureStore.deleteItemAsync(TOKEN_KEY);
  },

  async setUser(user: object): Promise<void> {
    await SecureStore.setItemAsync(USER_KEY, JSON.stringify(user));
  },

  async getUser<T>(): Promise<T | null> {
    const raw = await SecureStore.getItemAsync(USER_KEY);
    if (!raw) return null;
    try {
      return JSON.parse(raw) as T;
    } catch {
      return null;
    }
  },

  async removeUser(): Promise<void> {
    await SecureStore.deleteItemAsync(USER_KEY);
  },

  async clearAll(): Promise<void> {
    await Promise.all([
      SecureStore.deleteItemAsync(TOKEN_KEY),
      SecureStore.deleteItemAsync(USER_KEY),
    ]);
  },

  async getOnboardingDone(): Promise<boolean> {
    const raw = await SecureStore.getItemAsync(ONBOARDING_KEY);
    return raw === 'true';
  },

  async setOnboardingDone(): Promise<void> {
    await SecureStore.setItemAsync(ONBOARDING_KEY, 'true');
  },

  async getPushEnabled(): Promise<boolean> {
    const raw = await SecureStore.getItemAsync(PUSH_ENABLED_KEY);
    return raw === null ? true : raw === 'true';
  },

  async setPushEnabled(enabled: boolean): Promise<void> {
    await SecureStore.setItemAsync(PUSH_ENABLED_KEY, enabled ? 'true' : 'false');
  },
};
