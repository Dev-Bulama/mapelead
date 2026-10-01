import 'react-native-gesture-handler';
import React from 'react';
import { View, ActivityIndicator } from 'react-native';
import { StatusBar } from 'expo-status-bar';
import { SafeAreaProvider } from 'react-native-safe-area-context';
import { QueryClientProvider } from '@tanstack/react-query';
import { useFonts } from 'expo-font';
import { Ionicons } from '@expo/vector-icons';
import { queryClient } from '@/stores/queryClient';
import RootNavigator from '@/navigation/RootNavigator';
import OfflineBanner from '@/components/common/OfflineBanner';
import { usePushNotifications } from '@/hooks/usePushNotifications';
import { useStreak } from '@/hooks/useStreak';
import { Colors } from '@/theme';

function AppInner() {
  usePushNotifications();
  useStreak();
  return (
    <>
      <StatusBar style="auto" />
      <OfflineBanner />
      <RootNavigator />
    </>
  );
}

export default function App() {
  const [fontsLoaded] = useFonts({ ...Ionicons.font });

  if (!fontsLoaded) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: Colors.white }}>
        <ActivityIndicator color={Colors.primary} />
      </View>
    );
  }

  return (
    <SafeAreaProvider>
      <QueryClientProvider client={queryClient}>
        <AppInner />
      </QueryClientProvider>
    </SafeAreaProvider>
  );
}
