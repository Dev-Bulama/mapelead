import React from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { Text, StyleSheet } from 'react-native';
import { Colors, Typography, Spacing } from '@/theme';
import type { MainTabParamList, RootStackParamList } from '@/types';

// Tab screens
import HomeScreen         from '@/screens/main/HomeScreen';
import ExploreScreen      from '@/screens/main/ExploreScreen';
import MyCoursesScreen    from '@/screens/main/MyCoursesScreen';
import DownloadsScreen    from '@/screens/main/DownloadsScreen';
import ProfileScreen      from '@/screens/main/ProfileScreen';

// Pushed screens
import CourseDetailScreen      from '@/screens/course/CourseDetailScreen';
import LessonViewScreen        from '@/screens/lesson/LessonViewScreen';
import QuizScreen              from '@/screens/quiz/QuizScreen';
import AssignmentScreen        from '@/screens/assignment/AssignmentScreen';
import NotificationsScreen     from '@/screens/main/NotificationsScreen';
import CertificatesScreen      from '@/screens/main/CertificatesScreen';
import ProfileEditScreen       from '@/screens/main/ProfileEditScreen';
import SecurityScreen          from '@/screens/main/SecurityScreen';
import PaymentHistoryScreen    from '@/screens/main/PaymentHistoryScreen';
import LearningStatsScreen     from '@/screens/main/LearningStatsScreen';
import PaymentWebViewScreen    from '@/screens/payment/PaymentWebViewScreen';

const Tab   = createBottomTabNavigator<MainTabParamList>();
const Stack = createNativeStackNavigator<RootStackParamList>();

const TAB_ICONS: Record<string, string> = {
  Home:       '🏠',
  Explore:    '🔍',
  MyLearning: '📚',
  Downloads:  '📥',
  Profile:    '👤',
};

function TabIcon({ name, focused }: { name: string; focused: boolean }) {
  return (
    <Text style={{ fontSize: 20, opacity: focused ? 1 : 0.45 }}>{TAB_ICONS[name] ?? '●'}</Text>
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
      <Tab.Screen name="Home"       component={HomeScreen}      options={{ title: 'Home' }} />
      <Tab.Screen name="Explore"    component={ExploreScreen}   options={{ title: 'Explore' }} />
      <Tab.Screen name="MyLearning" component={MyCoursesScreen} options={{ title: 'My Learning' }} />
      <Tab.Screen name="Downloads"  component={DownloadsScreen} options={{ title: 'Downloads' }} />
      <Tab.Screen name="Profile"    component={ProfileScreen}   options={{ title: 'Profile' }} />
    </Tab.Navigator>
  );
}

export default function MainNavigator() {
  return (
    <Stack.Navigator screenOptions={{ headerShown: false }}>
      <Stack.Screen name="Main"           component={TabsRoot} />
      <Stack.Screen name="CourseDetail"   component={CourseDetailScreen}   options={{ animation: 'slide_from_right' }} />
      <Stack.Screen name="LessonView"     component={LessonViewScreen}     options={{ animation: 'slide_from_right' }} />
      <Stack.Screen name="QuizView"       component={QuizScreen}           options={{ animation: 'slide_from_bottom' }} />
      <Stack.Screen name="AssignmentView" component={AssignmentScreen}     options={{ animation: 'slide_from_right' }} />
      <Stack.Screen name="Notifications"  component={NotificationsScreen}  options={{ animation: 'slide_from_right' }} />
      <Stack.Screen name="ProfileEdit"    component={ProfileEditScreen}    options={{ animation: 'slide_from_bottom' }} />
      <Stack.Screen name="Security"       component={SecurityScreen}       options={{ animation: 'slide_from_right' }} />
      <Stack.Screen name="Certificates"   component={CertificatesScreen}   options={{ animation: 'slide_from_right' }} />
      <Stack.Screen name="PaymentHistory" component={PaymentHistoryScreen} options={{ animation: 'slide_from_right' }} />
      <Stack.Screen name="LearningStats"  component={LearningStatsScreen}  options={{ animation: 'slide_from_right' }} />
      <Stack.Screen name="PaymentWebView" component={PaymentWebViewScreen} options={{ animation: 'slide_from_bottom', gestureEnabled: false }} />
    </Stack.Navigator>
  );
}

const styles = StyleSheet.create({
  tabBar: {
    backgroundColor: Colors.white,
    borderTopColor:  Colors.gray100,
    borderTopWidth:  1,
    paddingTop:      Spacing[1],
    paddingBottom:   Spacing[2],
    height:          60,
  },
  tabLabel: {
    fontSize:   Typography.sizes.xs,
    fontWeight: Typography.weights.medium,
    marginTop:  2,
  },
});
