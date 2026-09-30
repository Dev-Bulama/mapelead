import React, { useState } from 'react';
import {
  View, Text, ScrollView, TouchableOpacity, Image, StyleSheet,
  ActivityIndicator, Alert,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { coursesApi } from '@/api/courses';
import { enrollmentsApi } from '@/api/enrollments';
import { extractApiError } from '@/api/client';
import { useAuthStore, selectUser } from '@/stores/authStore';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { RootStackParamList, Enrollment, ModuleSummary } from '@/types';

type Props = NativeStackScreenProps<RootStackParamList, 'CourseDetail'>;

export default function CourseDetailScreen({ route, navigation }: Props) {
  const { slug } = route.params;
  const user = useAuthStore(selectUser);
  const qc   = useQueryClient();
  const [expandedModule, setExpandedModule] = useState<number | null>(null);

  const { data: course, isLoading } = useQuery({
    queryKey: ['course', slug],
    queryFn:  () => coursesApi.detail(slug).then((r) => r.data.data),
  });

  const { data: enrollments } = useQuery({
    queryKey: ['enrollments'],
    queryFn:  () => enrollmentsApi.myCourses().then((r) => r.data.data),
    enabled:  !!user,
  });

  const enrollment = enrollments?.find(
    (e: Enrollment) => e.course?.slug === slug && e.payment_status === 'paid'
  );

  const { mutate: enroll, isPending: enrolling } = useMutation({
    mutationFn: () => enrollmentsApi.enroll(course!.id, { training_type: 'online' }),
    onSuccess: (res) => {
      const { is_free, payment_url } = res.data.data;
      if (is_free) {
        qc.invalidateQueries({ queryKey: ['enrollments'] });
        Alert.alert('Enrolled!', 'You now have access to this course.');
      } else if (payment_url) {
        navigation.navigate('PaymentWebView', {
          url: payment_url,
          reference: res.data.data.payment_reference!,
        });
      }
    },
    onError: (err) => Alert.alert('Error', extractApiError(err)),
  });

  if (isLoading) {
    return <View style={styles.center}><ActivityIndicator color={Colors.primary} size="large" /></View>;
  }

  if (!course) {
    return <View style={styles.center}><Text>Course not found</Text></View>;
  }

  return (
    <View style={styles.flex}>
      <ScrollView contentContainerStyle={styles.scroll}>
        {/* Hero */}
        {course.thumbnail_url ? (
          <Image source={{ uri: course.thumbnail_url }} style={styles.hero} />
        ) : (
          <View style={[styles.hero, styles.heroFallback]} />
        )}

        {/* Back button */}
        <TouchableOpacity style={styles.backBtn} onPress={() => navigation.goBack()}>
          <Text style={styles.backBtnText}>‹</Text>
        </TouchableOpacity>

        <View style={styles.body}>
          {/* Category & Title */}
          <Text style={styles.category}>{course.category?.name ?? 'Course'}</Text>
          <Text style={styles.title}>{course.title}</Text>

          {/* Stats row */}
          <View style={styles.statsRow}>
            <StatChip label={`${course.total_lessons} lessons`} />
            {course.level && <StatChip label={course.level} />}
            {course.has_certificate && <StatChip label="Certificate" highlight />}
            {course.duration_hours ? <StatChip label={`${course.duration_hours}h`} /> : null}
          </View>

          {/* Description */}
          {course.description && (
            <View style={styles.section}>
              <Text style={styles.sectionTitle}>About this course</Text>
              <Text style={styles.bodyText}>{course.description}</Text>
            </View>
          )}

          {/* Curriculum */}
          {(course.modules ?? []).length > 0 && (
            <View style={styles.section}>
              <Text style={styles.sectionTitle}>Curriculum</Text>
              {(course.modules ?? []).map((mod: ModuleSummary) => (
                <View key={mod.id} style={styles.module}>
                  <TouchableOpacity
                    style={styles.moduleHeader}
                    onPress={() => setExpandedModule(expandedModule === mod.id ? null : mod.id)}
                  >
                    <Text style={styles.moduleTitle}>{mod.title}</Text>
                    <Text style={styles.moduleChevron}>
                      {expandedModule === mod.id ? '▲' : '▼'}
                    </Text>
                  </TouchableOpacity>
                  {expandedModule === mod.id && mod.lessons.map((lesson) => (
                    <TouchableOpacity
                      key={lesson.id}
                      style={styles.lessonRow}
                      onPress={() => {
                        if (enrollment || lesson.is_free_preview) {
                          navigation.navigate('LessonView', { lessonId: lesson.id, courseSlug: slug });
                        } else {
                          Alert.alert('Enroll required', 'Please enroll to access this lesson.');
                        }
                      }}
                    >
                      <Text style={styles.lessonIcon}>
                        {lesson.type === 'video' ? '▶' : lesson.type === 'quiz' ? '❓' : '📄'}
                      </Text>
                      <Text style={styles.lessonTitle} numberOfLines={1}>{lesson.title}</Text>
                      {lesson.is_free_preview && (
                        <Text style={styles.freeTag}>Free</Text>
                      )}
                      {lesson.duration_minutes && (
                        <Text style={styles.duration}>{lesson.duration_minutes}m</Text>
                      )}
                    </TouchableOpacity>
                  ))}
                </View>
              ))}
            </View>
          )}
        </View>
      </ScrollView>

      {/* CTA */}
      <View style={styles.cta}>
        {enrollment ? (
          <TouchableOpacity
            style={styles.ctaBtn}
            onPress={() =>
              navigation.navigate('LessonView', {
                lessonId: course.modules?.[0]?.lessons?.[0]?.id ?? 0,
                courseSlug: slug,
              })
            }
          >
            <Text style={styles.ctaBtnText}>Continue Learning →</Text>
          </TouchableOpacity>
        ) : (
          <TouchableOpacity
            style={[styles.ctaBtn, enrolling && styles.ctaBtnDisabled]}
            onPress={() => enroll()}
            disabled={enrolling}
          >
            <Text style={styles.ctaBtnText}>
              {enrolling
                ? 'Processing…'
                : course.is_free
                ? 'Enroll Free'
                : `Enroll — ₦${course.price.online.toLocaleString()}`}
            </Text>
          </TouchableOpacity>
        )}
      </View>
    </View>
  );
}

function StatChip({ label, highlight }: { label: string; highlight?: boolean }) {
  return (
    <View style={[styles.chip, highlight && styles.chipHighlight]}>
      <Text style={[styles.chipText, highlight && styles.chipTextHighlight]}>{label}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  flex:              { flex: 1, backgroundColor: Colors.white },
  center:            { flex: 1, justifyContent: 'center', alignItems: 'center' },
  scroll:            { paddingBottom: 100 },
  hero:              { width: '100%', height: 240, backgroundColor: Colors.gray200 },
  heroFallback:      { backgroundColor: Colors.primaryLight },
  backBtn:           { position: 'absolute', top: 44, left: Spacing[4], backgroundColor: 'rgba(0,0,0,0.4)', width: 36, height: 36, borderRadius: Radii.full, justifyContent: 'center', alignItems: 'center' },
  backBtnText:       { color: Colors.white, fontSize: 22, fontWeight: Typography.weights.bold, marginTop: -2 },
  body:              { padding: Spacing[4] },
  category:          { fontSize: Typography.sizes.xs, color: Colors.primary, fontWeight: Typography.weights.semibold, textTransform: 'uppercase', marginBottom: Spacing[2] },
  title:             { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.textPrimary, lineHeight: 32, marginBottom: Spacing[3] },
  statsRow:          { flexDirection: 'row', flexWrap: 'wrap', gap: Spacing[2], marginBottom: Spacing[4] },
  chip:              { backgroundColor: Colors.gray100, borderRadius: Radii.full, paddingHorizontal: Spacing[3], paddingVertical: Spacing[1] },
  chipHighlight:     { backgroundColor: Colors.primary },
  chipText:          { fontSize: Typography.sizes.xs, color: Colors.textSecondary, fontWeight: Typography.weights.medium },
  chipTextHighlight: { color: Colors.white },
  section:           { marginBottom: Spacing[6] },
  sectionTitle:      { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: Spacing[3] },
  bodyText:          { fontSize: Typography.sizes.base, color: Colors.textSecondary, lineHeight: 24 },
  module:            { backgroundColor: Colors.surface, borderRadius: Radii.lg, marginBottom: Spacing[3], overflow: 'hidden' },
  moduleHeader:      { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', padding: Spacing[4] },
  moduleTitle:       { flex: 1, fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  moduleChevron:     { fontSize: Typography.sizes.xs, color: Colors.gray500 },
  lessonRow:         { flexDirection: 'row', alignItems: 'center', padding: Spacing[3], paddingLeft: Spacing[4], borderTopWidth: 1, borderTopColor: Colors.border, gap: Spacing[3] },
  lessonIcon:        { fontSize: 14, color: Colors.gray500 },
  lessonTitle:       { flex: 1, fontSize: Typography.sizes.sm, color: Colors.textPrimary },
  freeTag:           { fontSize: Typography.sizes.xs, color: Colors.success, fontWeight: Typography.weights.semibold },
  duration:          { fontSize: Typography.sizes.xs, color: Colors.textMuted },
  cta:               { position: 'absolute', bottom: 0, left: 0, right: 0, backgroundColor: Colors.white, padding: Spacing[4], borderTopWidth: 1, borderTopColor: Colors.border, ...Shadows.lg },
  ctaBtn:            { backgroundColor: Colors.primary, borderRadius: Radii.lg, paddingVertical: Spacing[4], alignItems: 'center' },
  ctaBtnDisabled:    { opacity: 0.6 },
  ctaBtnText:        { color: Colors.white, fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold },
});
