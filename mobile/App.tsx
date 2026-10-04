import React from 'react';
import { View, Text, ActivityIndicator, StyleSheet } from 'react-native';
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

// ── Error boundary — catches JS errors instead of silently crashing ───────────
interface EBState { hasError: boolean; message: string }
class ErrorBoundary extends React.Component<React.PropsWithChildren, EBState> {
  constructor(props: React.PropsWithChildren) {
    super(props);
    this.state = { hasError: false, message: '' };
  }
  static getDerivedStateFromError(err: unknown): EBState {
    return { hasError: true, message: String(err) };
  }
  render() {
    if (this.state.hasError) {
      return (
        <View style={eb.screen}>
          <Text style={eb.title}>Something went wrong</Text>
          <Text style={eb.msg}>{this.state.message}</Text>
        </View>
      );
    }
    return this.props.children;
  }
}
const eb = StyleSheet.create({
  screen: { flex: 1, justifyContent: 'center', alignItems: 'center', padding: 24, backgroundColor: '#fff' },
  title:  { fontSize: 18, fontWeight: '700', color: '#111', marginBottom: 12 },
  msg:    { fontSize: 13, color: '#666', textAlign: 'center' },
});

// ── Inner app — hooks that need QueryClient / Auth context ────────────────────
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

// ── Root ──────────────────────────────────────────────────────────────────────
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
    <ErrorBoundary>
      <SafeAreaProvider>
        <QueryClientProvider client={queryClient}>
          <AppInner />
        </QueryClientProvider>
      </SafeAreaProvider>
    </ErrorBoundary>
  );
}
