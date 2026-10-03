import React, { useEffect } from 'react';
import { View, ActivityIndicator, StyleSheet, Linking } from 'react-native';
import { NavigationContainer, LinkingOptions, createNavigationContainerRef } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { useAuthStore, selectIsAuthenticated } from '@/stores/authStore';
import { Colors } from '@/theme';
import type { RootStackParamList } from '@/types';

export const navigationRef = createNavigationContainerRef<RootStackParamList>();

import AuthNavigator    from './AuthNavigator';
import MainNavigator    from './MainNavigator';
import OnboardingScreen from '@/screens/onboarding/OnboardingScreen';

const Root = createNativeStackNavigator<RootStackParamList>();

const linking: LinkingOptions<RootStackParamList> = {
  prefixes: ['mapelead://', 'https://mapelead.org', 'http://mapelead.org'],
  config: {
    screens: {
      Main: {
        screens: {
          Main: {
            screens: {
              Home: '',
            },
          },
        },
      },
      CourseDetail:        { path: 'courses/:slug' },
      LessonView:          { path: 'lessons/:lessonId' },
      QuizView:            { path: 'quizzes/:quizId' },
      Certificates:        { path: 'certificates' },
      Notifications:       { path: 'notifications' },
      PaymentHistory:      { path: 'payments' },
      SupportTickets:      { path: 'tickets' },
      SupportTicketDetail: { path: 'tickets/:ticketId' },
    } as any,
  },
};

export default function RootNavigator() {
  const { initialize, isInitialized } = useAuthStore();
  const isAuthenticated   = useAuthStore(selectIsAuthenticated);
  const onboardingDone    = useAuthStore((s) => s.onboardingDone);

  useEffect(() => {
    initialize();
  }, []);

  if (!isInitialized) {
    return (
      <View style={styles.splash}>
        <ActivityIndicator size="large" color={Colors.primary} />
      </View>
    );
  }

  return (
    <NavigationContainer ref={navigationRef} linking={linking}>
      <Root.Navigator screenOptions={{ headerShown: false, animation: 'fade' }}>
        {isAuthenticated ? (
          onboardingDone ? (
            <Root.Screen name="Main" component={MainNavigator} />
          ) : (
            <Root.Screen name="Onboarding" component={OnboardingScreen} />
          )
        ) : (
          <Root.Screen name="Auth" component={AuthNavigator} />
        )}
      </Root.Navigator>
    </NavigationContainer>
  );
}

const styles = StyleSheet.create({
  splash: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: Colors.background,
  },
});
