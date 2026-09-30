import React from 'react';
import {
  View, Text, ScrollView, TouchableOpacity,
  StyleSheet, FlatList, Image, ActivityIndicator,
} from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { coursesApi } from '@/api/courses';
import { enrollmentsApi } from '@/api/enrollments';
import { useAuthStore, selectUser } from '@/stores/authStore';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { RootStackParamList, Course, Enrollment } from '@/types';

type Nav = NativeStackNavigationProp<RootStackParamList>;

export default function HomeScreen() {
  const navigation = useNavigation<Nav>();
  const user = useAuthStore(selectUser);

  const { data: featured, isLoading: featuredLoading } = useQuery({
    queryKey: ['courses', 'featured'],
    queryFn:  () => coursesApi.featured().then((r) => r.data.data),
  });

  const { data: enrollments } = useQuery({
    queryKey: ['enrollments'],
    queryFn:  () => enrollmentsApi.myCourses().then((r) => r.data.data),
  });

  const inProgress = enrollments?.filter((e: Enrollment) =>
    e.status === 'active' && e.progress_percent < 100
  ).slice(0, 3) ?? [];

  return (
    <ScrollView style={styles.screen} contentContainerStyle={styles.scroll}>
      {/* Greeting */}
      <View style={styles.greeting}>
        <Text style={styles.greetingText}>
          Hello, {user?.first_name ?? 'Learner'} 👋
        </Text>
        <Text style={styles.greetingSubtitle}>What are you learning today?</Text>
      </View>

      {/* Continue learning */}
      {inProgress.length > 0 && (
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Continue Learning</Text>
          {inProgress.map((e: Enrollment) => (
            <TouchableOpacity
              key={e.id}
              style={styles.progressCard}
              onPress={() => navigation.navigate('CourseDetail', { slug: e.course!.slug })}
            >
              <View style={styles.progressInfo}>
                <Text style={styles.progressTitle} numberOfLines={1}>{e.course?.title}</Text>
                <Text style={styles.progressLabel}>{Math.round(e.progress_percent)}% complete</Text>
                <View style={styles.progressBar}>
                  <View style={[styles.progressFill, { width: `${e.progress_percent}%` as any }]} />
                </View>
              </View>
            </TouchableOpacity>
          ))}
        </View>
      )}

      {/* Featured courses */}
      <View style={styles.section}>
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>Featured Courses</Text>
          <TouchableOpacity onPress={() => navigation.navigate('Main' as any)}>
            <Text style={styles.seeAll}>See all</Text>
          </TouchableOpacity>
        </View>

        {featuredLoading ? (
          <ActivityIndicator color={Colors.primary} style={{ marginTop: Spacing[4] }} />
        ) : (
          <FlatList
            data={featured ?? []}
            keyExtractor={(item) => String(item.id)}
            horizontal
            showsHorizontalScrollIndicator={false}
            contentContainerStyle={{ paddingBottom: Spacing[2] }}
            renderItem={({ item }) => (
              <CourseCard
                course={item}
                onPress={() => navigation.navigate('CourseDetail', { slug: item.slug })}
              />
            )}
          />
        )}
      </View>
    </ScrollView>
  );
}

function CourseCard({ course, onPress }: { course: Course; onPress: () => void }) {
  return (
    <TouchableOpacity style={styles.courseCard} onPress={onPress}>
      {course.thumbnail_url ? (
        <Image source={{ uri: course.thumbnail_url }} style={styles.courseThumbnail} />
      ) : (
        <View style={[styles.courseThumbnail, styles.courseThumbnailFallback]} />
      )}
      <View style={styles.courseInfo}>
        <Text style={styles.courseCategory} numberOfLines={1}>
          {course.category?.name ?? 'General'}
        </Text>
        <Text style={styles.courseTitle} numberOfLines={2}>{course.title}</Text>
        <Text style={styles.coursePrice}>
          {course.is_free ? 'Free' : `₦${course.price.online.toLocaleString()}`}
        </Text>
      </View>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  screen:             { flex: 1, backgroundColor: Colors.surface },
  scroll:             { padding: Spacing[4], paddingBottom: Spacing[10] },
  greeting:           { paddingVertical: Spacing[6] },
  greetingText:       { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  greetingSubtitle:   { fontSize: Typography.sizes.base, color: Colors.textSecondary, marginTop: Spacing[1] },
  section:            { marginBottom: Spacing[6] },
  sectionHeader:      { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: Spacing[3] },
  sectionTitle:       { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: Spacing[3] },
  seeAll:             { fontSize: Typography.sizes.sm, color: Colors.primary, fontWeight: Typography.weights.medium },
  progressCard:       { backgroundColor: Colors.white, borderRadius: Radii.lg, padding: Spacing[4], marginBottom: Spacing[3], ...Shadows.sm },
  progressInfo:       { flex: 1 },
  progressTitle:      { fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: Spacing[1] },
  progressLabel:      { fontSize: Typography.sizes.sm, color: Colors.textSecondary, marginBottom: Spacing[2] },
  progressBar:        { height: 6, backgroundColor: Colors.gray200, borderRadius: Radii.full, overflow: 'hidden' },
  progressFill:       { height: '100%', backgroundColor: Colors.primary, borderRadius: Radii.full },
  courseCard:         { width: 200, backgroundColor: Colors.white, borderRadius: Radii.xl, marginRight: Spacing[4], ...Shadows.sm, overflow: 'hidden' },
  courseThumbnail:    { width: '100%', height: 110 },
  courseThumbnailFallback: { backgroundColor: Colors.gray200 },
  courseInfo:         { padding: Spacing[3] },
  courseCategory:     { fontSize: Typography.sizes.xs, color: Colors.primary, fontWeight: Typography.weights.semibold, textTransform: 'uppercase', marginBottom: Spacing[1] },
  courseTitle:        { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: Spacing[2], lineHeight: 18 },
  coursePrice:        { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.bold, color: Colors.primary },
});
