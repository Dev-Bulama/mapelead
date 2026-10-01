import React from 'react';
import {
  View, Text, FlatList, TouchableOpacity, Image,
  StyleSheet, ActivityIndicator,
} from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import { enrollmentsApi } from '@/api/enrollments';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { Enrollment, RootStackParamList } from '@/types';

type Nav = NativeStackNavigationProp<RootStackParamList>;

export default function MyCoursesScreen() {
  const navigation = useNavigation<Nav>();

  const { data, isLoading, refetch } = useQuery({
    queryKey: ['enrollments'],
    queryFn:  () => enrollmentsApi.myCourses().then((r) => r.data.data),
  });

  if (isLoading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color={Colors.primary} />
      </View>
    );
  }

  const paid = (data ?? []).filter((e: Enrollment) => e.payment_status === 'paid');

  return (
    <View style={styles.screen}>
      <View style={styles.header}>
        <Text style={styles.title}>My Courses</Text>
        <Text style={styles.subtitle}>{paid.length} enrolled</Text>
      </View>

      {paid.length === 0 ? (
        <View style={styles.empty}>
          <Ionicons name="book-outline" size={52} color={Colors.gray300} />
          <Text style={styles.emptyTitle}>No courses yet</Text>
          <Text style={styles.emptySubtitle}>
            Explore courses and enroll to get started
          </Text>
        </View>
      ) : (
        <FlatList
          data={paid}
          keyExtractor={(item) => String(item.id)}
          contentContainerStyle={styles.list}
          onRefresh={refetch}
          refreshing={isLoading}
          renderItem={({ item }) => (
            <EnrollmentCard
              enrollment={item}
              onPress={() =>
                navigation.navigate('CourseDetail', { slug: item.course!.slug })
              }
            />
          )}
        />
      )}
    </View>
  );
}

function EnrollmentCard({ enrollment, onPress }: { enrollment: Enrollment; onPress: () => void }) {
  const statusColors: Record<string, string> = {
    active:    Colors.success,
    completed: Colors.primary,
    pending:   Colors.warning,
    expired:   Colors.gray400,
  };

  return (
    <TouchableOpacity style={styles.card} onPress={onPress}>
      {enrollment.course?.thumbnail_url ? (
        <Image source={{ uri: enrollment.course.thumbnail_url }} style={styles.thumbnail} />
      ) : (
        <View style={[styles.thumbnail, styles.thumbnailFallback]} />
      )}
      <View style={styles.info}>
        <Text style={styles.courseTitle} numberOfLines={2}>{enrollment.course?.title}</Text>
        <Text style={styles.category}>{enrollment.course?.category ?? 'Course'}</Text>
        <View style={styles.row}>
          <View style={styles.progressBar}>
            <View style={[styles.progressFill, { width: `${enrollment.progress_percent}%` as any }]} />
          </View>
          <Text style={styles.progressText}>{Math.round(enrollment.progress_percent)}%</Text>
        </View>
        <View style={[styles.badge, { backgroundColor: statusColors[enrollment.status] ?? Colors.gray400 }]}>
          <Text style={styles.badgeText}>{enrollment.status}</Text>
        </View>
      </View>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  screen:           { flex: 1, backgroundColor: Colors.surface },
  header:           { backgroundColor: Colors.white, padding: Spacing[4], paddingTop: Spacing[10], borderBottomWidth: 1, borderBottomColor: Colors.border },
  title:            { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  subtitle:         { fontSize: Typography.sizes.sm, color: Colors.textSecondary, marginTop: Spacing[1] },
  center:           { flex: 1, justifyContent: 'center', alignItems: 'center' },
  list:             { padding: Spacing[4] },
  empty:            { flex: 1, justifyContent: 'center', alignItems: 'center', padding: Spacing[8], gap: Spacing[4] },
  emptyTitle:       { fontSize: Typography.sizes.xl, fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  emptySubtitle:    { fontSize: Typography.sizes.base, color: Colors.textSecondary, textAlign: 'center', marginTop: Spacing[2] },
  card:             { flexDirection: 'row', backgroundColor: Colors.white, borderRadius: Radii.xl, marginBottom: Spacing[4], overflow: 'hidden', ...Shadows.sm },
  thumbnail:        { width: 100, height: 90 },
  thumbnailFallback:{ backgroundColor: Colors.gray200 },
  info:             { flex: 1, padding: Spacing[3] },
  courseTitle:      { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: Spacing[1] },
  category:         { fontSize: Typography.sizes.xs, color: Colors.textMuted, marginBottom: Spacing[2] },
  row:              { flexDirection: 'row', alignItems: 'center', gap: Spacing[2], marginBottom: Spacing[2] },
  progressBar:      { flex: 1, height: 4, backgroundColor: Colors.gray200, borderRadius: Radii.full, overflow: 'hidden' },
  progressFill:     { height: '100%', backgroundColor: Colors.primary, borderRadius: Radii.full },
  progressText:     { fontSize: Typography.sizes.xs, color: Colors.textSecondary, width: 30, textAlign: 'right' },
  badge:            { alignSelf: 'flex-start', paddingHorizontal: Spacing[2], paddingVertical: 2, borderRadius: Radii.full },
  badgeText:        { fontSize: Typography.sizes.xs, color: Colors.white, fontWeight: Typography.weights.medium, textTransform: 'capitalize' },
});
