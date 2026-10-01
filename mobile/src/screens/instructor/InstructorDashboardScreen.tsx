import React from 'react';
import {
  View, Text, ScrollView, StyleSheet, TouchableOpacity,
  Image, ActivityIndicator, RefreshControl,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useQuery } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { ApiResponse, RootStackParamList } from '@/types';

type IoniconsName = React.ComponentProps<typeof Ionicons>['name'];

type Nav = NativeStackNavigationProp<RootStackParamList>;

interface InstructorStats {
  total_courses:   number;
  published:       number;
  total_students:  number;
  pending_reviews: number;
}

interface InstructorCourse {
  id:                number;
  title:             string;
  slug:              string;
  status:            string;
  enrollments_count: number;
  thumbnail_url:     string | null;
}

interface RecentEnrollment {
  id:     number;
  user:   { full_name: string; email: string; avatar_url: string | null };
  course: { title: string; slug: string };
}

interface PendingSubmission {
  id:           number;
  assignment:   { title: string };
  user:         { full_name: string };
  submitted_at: string;
}

interface DashboardData {
  stats:                InstructorStats;
  courses:              InstructorCourse[];
  recent_enrollments:   RecentEnrollment[];
  pending_submissions:  PendingSubmission[];
}

export default function InstructorDashboardScreen() {
  const navigation = useNavigation<Nav>();

  const { data, isLoading, refetch, isRefetching } = useQuery({
    queryKey: ['instructor', 'dashboard'],
    queryFn:  () => apiClient.get<ApiResponse<DashboardData>>(API.INSTRUCTOR_DASHBOARD).then(r => r.data.data),
    staleTime: 60_000,
  });

  if (isLoading) {
    return (
      <View style={S.loading}>
        <ActivityIndicator color={Colors.primary} size="large" />
      </View>
    );
  }

  const stats = data?.stats;

  return (
    <ScrollView
      style={S.screen}
      showsVerticalScrollIndicator={false}
      refreshControl={<RefreshControl refreshing={isRefetching} onRefresh={refetch} tintColor={Colors.primary} />}
    >
      {/* Hero */}
      <View style={S.hero}>
        <TouchableOpacity style={S.backBtn} onPress={() => navigation.goBack()}>
          <Text style={S.backText}>‹</Text>
        </TouchableOpacity>
        <Text style={S.heroTitle}>Instructor Panel</Text>
        <Text style={S.heroSub}>Manage your courses and students</Text>
      </View>

      {/* Stats */}
      <View style={S.statsRow}>
        <StatCard icon="book-outline"              label="Courses"   value={stats?.total_courses   ?? 0} />
        <StatCard icon="checkmark-circle-outline"  label="Published" value={stats?.published       ?? 0} />
        <StatCard icon="people-outline"            label="Students"  value={stats?.total_students  ?? 0} />
        <StatCard icon="document-text-outline"     label="Reviews"   value={stats?.pending_reviews ?? 0} />
      </View>

      {/* My Courses */}
      {(data?.courses ?? []).length > 0 && (
        <View style={S.section}>
          <Text style={S.sectionTitle}>My Courses</Text>
          {(data!.courses).map(course => (
            <TouchableOpacity
              key={course.id}
              style={S.courseRow}
              onPress={() => navigation.navigate('CourseDetail', { slug: course.slug })}
              activeOpacity={0.75}
            >
              {course.thumbnail_url ? (
                <Image source={{ uri: course.thumbnail_url }} style={S.thumb} />
              ) : (
                <View style={[S.thumb, S.thumbFallback]}>
                  <Ionicons name="book-outline" size={22} color={Colors.gray400} />
                </View>
              )}
              <View style={S.courseInfo}>
                <Text style={S.courseTitle} numberOfLines={2}>{course.title}</Text>
                <View style={S.metaRow}>
                  <View style={[S.badge, course.status === 'published' ? S.badgeGreen : S.badgeGray]}>
                    <Text style={S.badgeText}>{course.status}</Text>
                  </View>
                  <Text style={S.courseMeta}>{course.enrollments_count} students</Text>
                </View>
              </View>
            </TouchableOpacity>
          ))}
        </View>
      )}

      {/* Recent Enrollments */}
      {(data?.recent_enrollments ?? []).length > 0 && (
        <View style={S.section}>
          <Text style={S.sectionTitle}>Recent Enrollments</Text>
          {(data!.recent_enrollments).map(enr => (
            <View key={enr.id} style={S.enrollRow}>
              <View style={S.enrollAvatar}>
                <Ionicons name="person-outline" size={18} color={Colors.gray400} />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={S.enrollName}>{enr.user.full_name}</Text>
                <Text style={S.enrollCourse} numberOfLines={1}>{enr.course.title}</Text>
              </View>
            </View>
          ))}
        </View>
      )}

      {/* Pending Submissions */}
      {(data?.pending_submissions ?? []).length > 0 && (
        <View style={S.section}>
          <Text style={S.sectionTitle}>Pending Submissions</Text>
          {(data!.pending_submissions).map(sub => (
            <TouchableOpacity
              key={sub.id}
              style={S.submissionRow}
              onPress={() => navigation.navigate('InstructorGrading', { submissionId: sub.id })}
              activeOpacity={0.75}
            >
              <View style={{ flex: 1 }}>
                <Text style={S.submissionTitle}>{sub.assignment.title}</Text>
                <Text style={S.submissionMeta}>by {sub.user.full_name}</Text>
              </View>
              <Ionicons name="chevron-forward" size={16} color={Colors.gray300} />
            </TouchableOpacity>
          ))}
        </View>
      )}

      {(data?.courses ?? []).length === 0 && (
        <View style={S.empty}>
          <Ionicons name="file-tray-outline" size={48} color={Colors.gray300} />
          <Text style={S.emptyText}>No courses yet</Text>
          <Text style={S.emptySub}>Your courses will appear here once created by the admin</Text>
        </View>
      )}

      <View style={{ height: Spacing[10] }} />
    </ScrollView>
  );
}

function StatCard({ icon, label, value }: { icon: IoniconsName; label: string; value: number }) {
  return (
    <View style={S.statCard}>
      <Ionicons name={icon} size={20} color={Colors.primary} />
      <Text style={S.statValue}>{value}</Text>
      <Text style={S.statLabel}>{label}</Text>
    </View>
  );
}

const S = StyleSheet.create({
  screen:          { flex: 1, backgroundColor: Colors.surface },
  loading:         { flex: 1, justifyContent: 'center', alignItems: 'center' },
  hero:            { backgroundColor: Colors.navy, paddingHorizontal: Spacing[4], paddingTop: Spacing[12], paddingBottom: Spacing[5] },
  backBtn:         { marginBottom: Spacing[2] },
  backText:        { fontSize: 28, color: 'rgba(255,255,255,0.8)', lineHeight: 32 },
  heroTitle:       { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.white },
  heroSub:         { fontSize: Typography.sizes.sm, color: 'rgba(255,255,255,0.65)', marginTop: 4 },
  statsRow:        { flexDirection: 'row', margin: Spacing[4], gap: Spacing[2] },
  statCard:        { flex: 1, backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[3], alignItems: 'center', gap: 2, ...Shadows.sm },
  statValue:       { fontSize: Typography.sizes.xl, fontWeight: Typography.weights.extrabold, color: Colors.primary },
  statLabel:       { fontSize: 9, color: Colors.textMuted, textAlign: 'center' },
  section:         { paddingHorizontal: Spacing[4], marginBottom: Spacing[4] },
  sectionTitle:    { fontSize: Typography.sizes.xs, fontWeight: Typography.weights.bold, color: Colors.textMuted, marginBottom: Spacing[3], textTransform: 'uppercase', letterSpacing: 1 },
  courseRow:       { flexDirection: 'row', backgroundColor: Colors.white, borderRadius: Radii.xl, marginBottom: Spacing[3], overflow: 'hidden', ...Shadows.sm },
  thumb:           { width: 80, height: 70 },
  thumbFallback:   { backgroundColor: Colors.gray100, alignItems: 'center', justifyContent: 'center' },
  courseInfo:      { flex: 1, padding: Spacing[3] },
  courseTitle:     { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  metaRow:         { flexDirection: 'row', alignItems: 'center', gap: Spacing[2], marginTop: Spacing[1] },
  badge:           { borderRadius: Radii.full, paddingHorizontal: 8, paddingVertical: 2 },
  badgeGreen:      { backgroundColor: 'rgba(16,185,129,0.1)' },
  badgeGray:       { backgroundColor: Colors.gray100 },
  badgeText:       { fontSize: Typography.sizes.xs, fontWeight: Typography.weights.medium, color: Colors.textSecondary, textTransform: 'capitalize' },
  courseMeta:      { fontSize: Typography.sizes.xs, color: Colors.textMuted },
  enrollRow:       { flexDirection: 'row', alignItems: 'center', backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[3], marginBottom: Spacing[2], gap: Spacing[3], ...Shadows.sm },
  enrollAvatar:    { width: 38, height: 38, borderRadius: 19, backgroundColor: Colors.gray100, alignItems: 'center', justifyContent: 'center' },
  enrollName:      { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  enrollCourse:    { fontSize: Typography.sizes.xs, color: Colors.textMuted, marginTop: 2 },
  submissionRow:   { flexDirection: 'row', alignItems: 'center', backgroundColor: Colors.white, borderRadius: Radii.lg, padding: Spacing[3], marginBottom: Spacing[2], borderLeftWidth: 3, borderLeftColor: Colors.primary, ...Shadows.sm },
  submissionTitle: { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  submissionMeta:  { fontSize: Typography.sizes.xs, color: Colors.textMuted, marginTop: 2 },
  empty:           { alignItems: 'center', paddingVertical: Spacing[10], paddingHorizontal: Spacing[8] },
  emptyText:       { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.bold, color: Colors.textPrimary, marginTop: Spacing[3] },
  emptySub:        { fontSize: Typography.sizes.sm, color: Colors.textMuted, textAlign: 'center', marginTop: Spacing[2] },
});
