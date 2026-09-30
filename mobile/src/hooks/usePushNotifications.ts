import { useEffect, useRef } from 'react';
import * as Notifications from 'expo-notifications';
import Constants from 'expo-constants';
import { Platform } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { apiClient } from '@/api/client';
import { useAuthStore, selectIsAuthenticated } from '@/stores/authStore';
import type { RootStackParamList } from '@/types';

type Nav = NativeStackNavigationProp<RootStackParamList>;

Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldShowAlert: true,
    shouldPlaySound: true,
    shouldSetBadge: true,
  }),
});

async function registerForPushNotifications(): Promise<string | null> {
  if (Platform.OS === 'android') {
    await Notifications.setNotificationChannelAsync('default', {
      name: 'default',
      importance: Notifications.AndroidImportance.MAX,
      vibrationPattern: [0, 250, 250, 250],
      lightColor: '#1e3adb',
    });
  }

  const { status: existing } = await Notifications.getPermissionsAsync();
  let finalStatus = existing;

  if (existing !== 'granted') {
    const { status } = await Notifications.requestPermissionsAsync();
    finalStatus = status;
  }

  if (finalStatus !== 'granted') return null;

  const projectId = Constants.expoConfig?.extra?.eas?.projectId
    ?? Constants.expoConfig?.extra?.projectId;

  try {
    const token = (await Notifications.getExpoPushTokenAsync({ projectId })).data;
    return token;
  } catch {
    return null;
  }
}

export function usePushNotifications() {
  const isAuthenticated = useAuthStore(selectIsAuthenticated);
  const navigation      = useNavigation<Nav>();
  const listenerRef     = useRef<Notifications.EventSubscription | null>(null);
  const responseRef     = useRef<Notifications.EventSubscription | null>(null);

  useEffect(() => {
    if (!isAuthenticated) return;

    registerForPushNotifications().then((token) => {
      if (!token) return;
      apiClient.post('/device-token', {
        token,
        platform: Platform.OS as 'ios' | 'android',
        app_version: Constants.expoConfig?.version,
      }).catch(() => {});
    });

    // Foreground notification listener
    listenerRef.current = Notifications.addNotificationReceivedListener(() => {
      // Badge / in-app toasts handled by the handler above
    });

    // Tapped notification → navigate
    responseRef.current = Notifications.addNotificationResponseReceivedListener((response) => {
      const data = response.notification.request.content.data as Record<string, unknown>;
      if (data?.screen === 'Notifications') {
        navigation.navigate('Notifications');
      } else if (data?.screen === 'CourseDetail' && typeof data.slug === 'string') {
        navigation.navigate('CourseDetail', { slug: data.slug });
      }
    });

    return () => {
      listenerRef.current?.remove();
      responseRef.current?.remove();
    };
  }, [isAuthenticated, navigation]);
}
