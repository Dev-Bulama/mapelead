import React from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { StyleSheet } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
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
import PaymentWebViewScreen       from '@/screens/payment/PaymentWebViewScreen';
import InstructorDashboardScreen  from '@/screens/instructor/InstructorDashboardScreen';
import InstructorGradingScreen    from '@/screens/instructor/InstructorGradingScreen';
import CourseAssignmentsScreen    from '@/screens/course/CourseAssignmentsScreen';
import BookmarksScreen            from '@/screens/main/BookmarksScreen';
import NotesScreen                from '@/screens/main/NotesScreen';

const Tab   = createBottomTabNavigator<MainTabParamList>();
const Stack = createNativeStackNavigator<RootStackParamList>();

type IoniconsName = React.ComponentProps<typeof Ionicons>['name'];
const TAB_ICONS: Record<string, { on: IoniconsName; off: IoniconsName }> = {
  Home:       { on: 'home',               off: 'home-outline' },
  Explore:    { on: 'search',             off: 'search-outline' },
  MyLearning: { on: 'book',               off: 'book-outline' },
  Downloads:  { on: 'arrow-down-circle',  off: 'arrow-down-circle-outline' },
  Profile:    { on: 'person',             off: 'person-outline' },
};

function TabIcon({ name, focused }: { name: string; focused: boolean }) {
  const cfg = TAB_ICONS[name];
  if (!cfg) return null;
  return (
    <Ionicons
      name={focused ? cfg.on : cfg.off}
      size={22}
      color={focused ? Colors.primary : Colors.gray400}
    />
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
      <Stack.Screen name="PaymentWebView"      component={PaymentWebViewScreen}      options={{ animation: 'slide_from_bottom', gestureEnabled: false }} />
      <Stack.Screen name="InstructorDashboard" component={InstructorDashboardScreen} options={{ animation: 'slide_from_right' }} />
      <Stack.Screen name="InstructorGrading"  component={InstructorGradingScreen}  options={{ animation: 'slide_from_right' }} />
      <Stack.Screen name="CourseAssignments"  component={CourseAssignmentsScreen}  options={{ animation: 'slide_from_right' }} />
      <Stack.Screen name="Bookmarks"          component={BookmarksScreen}           options={{ animation: 'slide_from_right' }} />
      <Stack.Screen name="Notes"              component={NotesScreen}               options={{ animation: 'slide_from_right' }} />
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
