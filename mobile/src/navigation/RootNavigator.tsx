import React, { useEffect } from 'react';
import { View, ActivityIndicator, StyleSheet, Linking } from 'react-native';
import { NavigationContainer, LinkingOptions } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { useAuthStore, selectIsAuthenticated } from '@/stores/authStore';
import { Colors } from '@/theme';
import type { RootStackParamList } from '@/types';

import AuthNavigator from './AuthNavigator';
import MainNavigator from './MainNavigator';

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
      CourseDetail:   { path: 'courses/:slug' },
      Certificates:   { path: 'certificates' },
      Notifications:  { path: 'notifications' },
      PaymentHistory: { path: 'payments' },
    } as any,
  },
};

export default function RootNavigator() {
  const { initialize, isInitialized } = useAuthStore();
  const isAuthenticated = useAuthStore(selectIsAuthenticated);

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
    <NavigationContainer linking={linking}>
      <Root.Navigator screenOptions={{ headerShown: false, animation: 'fade' }}>
        {isAuthenticated ? (
          <Root.Screen name="Main" component={MainNavigator} />
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
