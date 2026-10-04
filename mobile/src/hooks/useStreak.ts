import { useEffect, useRef } from 'react';
import { AppState, AppStateStatus } from 'react-native';
import * as SecureStore from 'expo-secure-store';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { useAuthStore, selectIsAuthenticated } from '@/stores/authStore';

const STORAGE_KEY = 'mapelead_last_streak_date';

function getTodayKey(): string {
  return new Date().toISOString().slice(0, 10);
}

async function getLastDate(): Promise<string | null> {
  try {
    return await SecureStore.getItemAsync(STORAGE_KEY);
  } catch {
    return null;
  }
}

async function setLastDate(date: string): Promise<void> {
  try {
    await SecureStore.setItemAsync(STORAGE_KEY, date);
  } catch {}
}

async function trackStreak() {
  const today = getTodayKey();
  if ((await getLastDate()) === today) return;
  try {
    await apiClient.post(API.STREAK);
    await setLastDate(today);
  } catch {}
}

export function useStreak() {
  const isAuthenticated = useAuthStore(selectIsAuthenticated);
  const appState        = useRef<AppStateStatus>(AppState.currentState);

  useEffect(() => {
    if (!isAuthenticated) return;

    // Track on mount (first open)
    trackStreak();

    const sub = AppState.addEventListener('change', (nextState) => {
      if (appState.current !== 'active' && nextState === 'active') {
        trackStreak();
      }
      appState.current = nextState;
    });

    return () => sub.remove();
  }, [isAuthenticated]);
}
