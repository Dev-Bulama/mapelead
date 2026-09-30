import React from 'react';
import {
  View, Text, ScrollView, TouchableOpacity, StyleSheet, ActivityIndicator, Alert,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { extractApiError } from '@/api/client';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { ApiResponse, Lesson, RootStackParamList } from '@/types';

type Props = NativeStackScreenProps<RootStackParamList, 'LessonView'>;

export default function LessonViewScreen({ route, navigation }: Props) {
  const { lessonId, courseSlug } = route.params;
  const qc = useQueryClient();

  const { data: lesson, isLoading } = useQuery({
    queryKey: ['lesson', lessonId],
    queryFn: () =>
      apiClient.get<ApiResponse<Lesson>>(API.LESSON(lessonId)).then((r) => r.data.data),
  });

  const { mutate: markComplete, isPending: completing } = useMutation({
    mutationFn: () => apiClient.post(API.LESSON_COMPLETE(lessonId)),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['lesson', lessonId] });
      qc.invalidateQueries({ queryKey: ['enrollments'] });
      Alert.alert('Done!', 'Lesson marked as complete.');
    },
    onError: (err) => Alert.alert('Error', extractApiError(err)),
  });

  const { mutate: toggleBookmark } = useMutation({
    mutationFn: () => apiClient.post(API.LESSON_BOOKMARK(lessonId)),
    onSuccess: () => qc.invalidateQueries({ queryKey: ['lesson', lessonId] }),
  });

  if (isLoading) {
    return <View style={styles.center}><ActivityIndicator color={Colors.primary} size="large" /></View>;
  }

  if (!lesson) {
    return <View style={styles.center}><Text>Lesson not found</Text></View>;
  }

  return (
    <View style={styles.flex}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={styles.backBtn}>‹ Back</Text>
        </TouchableOpacity>
        <TouchableOpacity onPress={() => toggleBookmark()}>
          <Text style={styles.bookmarkBtn}>{lesson.is_bookmarked ? '🔖' : '📌'}</Text>
        </TouchableOpacity>
      </View>

      <ScrollView contentContainerStyle={styles.scroll}>
        {/* Module breadcrumb */}
        {lesson.module && (
          <Text style={styles.breadcrumb}>{lesson.module.title}</Text>
        )}

        {/* Title */}
        <Text style={styles.title}>{lesson.title}</Text>

        {/* Video placeholder */}
        {lesson.video_url && (
          <View style={styles.videoPlaceholder}>
            <Text style={styles.videoIcon}>▶</Text>
            <Text style={styles.videoProvider}>{lesson.video_provider ?? 'video'}</Text>
          </View>
        )}

        {/* Content */}
        {lesson.content && (
          <View style={styles.contentBox}>
            <Text style={styles.content}>{lesson.content}</Text>
          </View>
        )}

        {/* Attachment */}
        {lesson.attachment_url && (
          <TouchableOpacity style={styles.attachmentBtn}>
            <Text style={styles.attachmentText}>📎 Download Attachment</Text>
          </TouchableOpacity>
        )}

        {/* Duration */}
        {lesson.duration_minutes && (
          <Text style={styles.meta}>{lesson.duration_minutes} min read/watch</Text>
        )}
      </ScrollView>

      {/* Footer: mark complete */}
      {!lesson.progress?.is_completed && (
        <View style={styles.footer}>
          <TouchableOpacity
            style={[styles.completeBtn, completing && styles.completeBtnDisabled]}
            onPress={() => markComplete()}
            disabled={completing}
          >
            <Text style={styles.completeBtnText}>
              {completing ? 'Saving…' : '✓ Mark as Complete'}
            </Text>
          </TouchableOpacity>
        </View>
      )}

      {lesson.progress?.is_completed && (
        <View style={styles.footer}>
          <View style={styles.completedBadge}>
            <Text style={styles.completedText}>✅ Completed</Text>
          </View>
        </View>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  flex:              { flex: 1, backgroundColor: Colors.white },
  center:            { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header:            { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingHorizontal: Spacing[4], paddingTop: Spacing[12], paddingBottom: Spacing[3], backgroundColor: Colors.white, borderBottomWidth: 1, borderBottomColor: Colors.border },
  backBtn:           { fontSize: Typography.sizes.base, color: Colors.primary, fontWeight: Typography.weights.medium },
  bookmarkBtn:       { fontSize: 22 },
  scroll:            { padding: Spacing[4], paddingBottom: 100 },
  breadcrumb:        { fontSize: Typography.sizes.xs, color: Colors.primary, fontWeight: Typography.weights.semibold, textTransform: 'uppercase', marginBottom: Spacing[2] },
  title:             { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.textPrimary, lineHeight: 30, marginBottom: Spacing[4] },
  videoPlaceholder:  { backgroundColor: Colors.gray900, borderRadius: Radii.xl, height: 200, justifyContent: 'center', alignItems: 'center', marginBottom: Spacing[4] },
  videoIcon:         { fontSize: 40, color: Colors.white },
  videoProvider:     { color: Colors.gray400, fontSize: Typography.sizes.sm, marginTop: Spacing[2], textTransform: 'capitalize' },
  contentBox:        { marginBottom: Spacing[4] },
  content:           { fontSize: Typography.sizes.base, color: Colors.textSecondary, lineHeight: 26 },
  attachmentBtn:     { backgroundColor: Colors.gray100, borderRadius: Radii.lg, padding: Spacing[4], marginBottom: Spacing[4] },
  attachmentText:    { fontSize: Typography.sizes.base, color: Colors.primary, fontWeight: Typography.weights.medium },
  meta:              { fontSize: Typography.sizes.sm, color: Colors.textMuted },
  footer:            { position: 'absolute', bottom: 0, left: 0, right: 0, padding: Spacing[4], backgroundColor: Colors.white, borderTopWidth: 1, borderTopColor: Colors.border, ...Shadows.md },
  completeBtn:       { backgroundColor: Colors.primary, borderRadius: Radii.lg, paddingVertical: Spacing[4], alignItems: 'center' },
  completeBtnDisabled:{ opacity: 0.6 },
  completeBtnText:   { color: Colors.white, fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold },
  completedBadge:    { backgroundColor: Colors.success, borderRadius: Radii.lg, paddingVertical: Spacing[4], alignItems: 'center' },
  completedText:     { color: Colors.white, fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold },
});
