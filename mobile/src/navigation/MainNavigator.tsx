import React from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { View, Text, StyleSheet } from 'react-native';
import { Colors, Typography, Spacing } from '@/theme';
import type { MainTabParamList, RootStackParamList } from '@/types';

// Tab screens
import HomeScreen         from '@/screens/main/HomeScreen';
import MyCoursesScreen    from '@/screens/main/MyCoursesScreen';
import ExploreScreen      from '@/screens/main/ExploreScreen';
import CertificatesScreen from '@/screens/main/CertificatesScreen';
import ProfileScreen      from '@/screens/main/ProfileScreen';

// Pushed screens
import CourseDetailScreen  from '@/screens/course/CourseDetailScreen';
import LessonViewScreen    from '@/screens/lesson/LessonViewScreen';
import NotificationsScreen from '@/screens/main/NotificationsScreen';

const Tab   = createBottomTabNavigator<MainTabParamList>();
const Stack = createNativeStackNavigator<Pick<RootStackParamList, 'Main' | 'CourseDetail' | 'LessonView' | 'QuizView' | 'ProfileEdit' | 'Notifications' | 'PaymentWebView'>>();

function TabIcon({ name, focused }: { name: string; focused: boolean }) {
  const icons: Record<string, string> = {
    Home:         '🏠',
    MyCourses:    '📚',
    Explore:      '🔍',
    Certificates: '🏆',
    Profile:      '👤',
  };
  return (
    <Text style={{ fontSize: 20, opacity: focused ? 1 : 0.5 }}>{icons[name] ?? '●'}</Text>
  );
}

function TabsRoot() {
  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        headerShown: false,
        tabBarIcon: ({ focused }) => <TabIcon name={route.name} focused={focused} />,
        tabBarActiveTintColor:   Colors.primary,
        tabBarInactiveTintColor: Colors.gray400,
        tabBarStyle: styles.tabBar,
        tabBarLabelStyle: styles.tabLabel,
      })}
    >
      <Tab.Screen name="Home"         component={HomeScreen}         options={{ title: 'Home' }} />
      <Tab.Screen name="MyCourses"    component={MyCoursesScreen}    options={{ title: 'My Courses' }} />
      <Tab.Screen name="Explore"      component={ExploreScreen}      options={{ title: 'Explore' }} />
      <Tab.Screen name="Certificates" component={CertificatesScreen} options={{ title: 'Certificates' }} />
      <Tab.Screen name="Profile"      component={ProfileScreen}      options={{ title: 'Profile' }} />
    </Tab.Navigator>
  );
}

export default function MainNavigator() {
  return (
    <Stack.Navigator screenOptions={{ headerShown: false }}>
      <Stack.Screen name="Main"          component={TabsRoot} />
      <Stack.Screen name="CourseDetail"  component={CourseDetailScreen}
        options={{ animation: 'slide_from_right' }} />
      <Stack.Screen name="LessonView"    component={LessonViewScreen}
        options={{ animation: 'slide_from_right' }} />
      <Stack.Screen name="Notifications" component={NotificationsScreen}
        options={{ animation: 'slide_from_right' }} />
    </Stack.Navigator>
  );
}

const styles = StyleSheet.create({
  tabBar: {
    backgroundColor:  Colors.white,
    borderTopColor:   Colors.border,
    borderTopWidth:   1,
    paddingTop:       Spacing[1],
    paddingBottom:    Spacing[2],
    height:           60,
  },
  tabLabel: {
    fontSize:   Typography.sizes.xs,
    fontWeight: Typography.weights.medium,
    marginTop:  2,
  },
});
