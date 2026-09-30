import 'react-native-gesture-handler';
import React from 'react';
import { StatusBar } from 'expo-status-bar';
import { SafeAreaProvider } from 'react-native-safe-area-context';
import { QueryClientProvider } from '@tanstack/react-query';
import { queryClient } from '@/stores/queryClient';
import RootNavigator from '@/navigation/RootNavigator';
import OfflineBanner from '@/components/common/OfflineBanner';

export default function App() {
  return (
    <SafeAreaProvider>
      <QueryClientProvider client={queryClient}>
        <StatusBar style="auto" />
        <OfflineBanner />
        <RootNavigator />
      </QueryClientProvider>
    </SafeAreaProvider>
  );
}
