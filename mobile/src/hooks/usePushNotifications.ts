import { useEffect, useRef } from 'react';
import * as Notifications from 'expo-notifications';
import Constants from 'expo-constants';
import { Platform } from 'react-native';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { navigationRef } from '@/navigation/RootNavigator';
import { useAuthStore, selectIsAuthenticated } from '@/stores/authStore';

async function registerForPushNotifications(): Promise<string | null> {
  try {
    if (Platform.OS === 'android') {
      await Notifications.setNotificationChannelAsync('default', {
        name:             'default',
        importance:       Notifications.AndroidImportance.MAX,
        vibrationPattern: [0, 250, 250, 250],
        lightColor:       '#1e3adb',
      });
    }

    const { status: existing } = await Notifications.getPermissionsAsync();
    let finalStatus = existing;
    if (existing !== 'granted') {
      const { status } = await Notifications.requestPermissionsAsync();
      finalStatus = status;
    }
    if (finalStatus !== 'granted') return null;

    const projectId =
      Constants.expoConfig?.extra?.eas?.projectId ??
      Constants.expoConfig?.extra?.projectId;

    const token = (await Notifications.getExpoPushTokenAsync({ projectId })).data;
    return token;
  } catch {
    return null;
  }
}

export function usePushNotifications() {
  const isAuthenticated = useAuthStore(selectIsAuthenticated);
  const listenerRef     = useRef<Notifications.EventSubscription | null>(null);
  const responseRef     = useRef<Notifications.EventSubscription | null>(null);
  const tokenRef        = useRef<string | null>(null);
  const prevAuthRef     = useRef<boolean>(isAuthenticated);

  // Set up notification handler once on mount
  useEffect(() => {
    try {
      Notifications.setNotificationHandler({
        handleNotification: async () => ({
          shouldShowAlert: true,
          shouldPlaySound: true,
          shouldSetBadge:  true,
        }),
      });
    } catch {}
  }, []);

  // De-register token on logout
  useEffect(() => {
    if (prevAuthRef.current && !isAuthenticated && tokenRef.current) {
      apiClient.delete(API.DEVICE_TOKEN, { data: { token: tokenRef.current } }).catch(() => {});
      tokenRef.current = null;
    }
    prevAuthRef.current = isAuthenticated;
  }, [isAuthenticated]);

  useEffect(() => {
    if (!isAuthenticated) return;

    registerForPushNotifications().then((token) => {
      if (!token) return;
      tokenRef.current = token;
      apiClient.post(API.DEVICE_TOKEN, {
        token,
        platform:    Platform.OS as 'ios' | 'android',
        app_version: Constants.expoConfig?.version,
      }).catch(() => {});
    });

    listenerRef.current = Notifications.addNotificationReceivedListener(() => {});

    responseRef.current = Notifications.addNotificationResponseReceivedListener((response) => {
      if (!navigationRef.isReady()) return;
      const data = response.notification.request.content.data as Record<string, unknown>;
      if (data?.screen === 'Notifications') {
        navigationRef.navigate('Notifications');
      } else if (data?.screen === 'CourseDetail' && typeof data.slug === 'string') {
        navigationRef.navigate('CourseDetail', { slug: data.slug });
      } else if (data?.screen === 'SupportTicketDetail' && typeof data.ticketId === 'number') {
        navigationRef.navigate('SupportTicketDetail', { ticketId: data.ticketId });
      } else if (data?.screen === 'LessonView' && typeof data.lessonId === 'number') {
        navigationRef.navigate('LessonView', {
          lessonId:   data.lessonId,
          courseSlug: typeof data.courseSlug === 'string' ? data.courseSlug : '',
        });
      }
    });

    return () => {
      listenerRef.current?.remove();
      responseRef.current?.remove();
    };
  }, [isAuthenticated]);
}
