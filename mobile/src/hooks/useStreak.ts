import { useEffect, useRef } from 'react';
import { AppState, AppStateStatus } from 'react-native';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { useAuthStore, selectIsAuthenticated } from '@/stores/authStore';

const STORAGE_KEY = 'mapelead_last_streak_date';

function getTodayKey(): string {
  return new Date().toISOString().slice(0, 10); // YYYY-MM-DD
}

function getLastDate(): string | null {
  try {
    return globalThis.localStorage?.getItem(STORAGE_KEY) ?? null;
  } catch {
    return null;
  }
}
function setLastDate(date: string): void {
  try { globalThis.localStorage?.setItem(STORAGE_KEY, date); } catch {}
}

async function trackStreak() {
  const today = getTodayKey();
  if (getLastDate() === today) return;
  try {
    await apiClient.post(API.STREAK);
    setLastDate(today);
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
