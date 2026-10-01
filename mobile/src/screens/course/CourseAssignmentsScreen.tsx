import React from 'react';
import {
  View, Text, FlatList, TouchableOpacity, StyleSheet, ActivityIndicator,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { useQuery } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { ApiResponse, Assignment, RootStackParamList } from '@/types';

type Props = NativeStackScreenProps<RootStackParamList, 'CourseAssignments'>;

type IoniconsName = React.ComponentProps<typeof Ionicons>['name'];

const STATUS_CONFIG: Record<string, { label: string; color: string; bg: string; icon: IoniconsName }> = {
  not_submitted: { label: 'Not submitted', color: Colors.gray500,  bg: Colors.gray100,  icon: 'time-outline' },
  submitted:     { label: 'Submitted',     color: '#d97706',        bg: '#fef3c7',       icon: 'paper-plane-outline' },
  graded:        { label: 'Graded',        color: Colors.success,   bg: '#dcfce7',       icon: 'checkmark-circle-outline' },
};

export default function CourseAssignmentsScreen({ route, navigation }: Props) {
  const { courseId, courseSlug, courseTitle } = route.params;

  const { data, isLoading } = useQuery({
    queryKey: ['assignments', courseId],
    queryFn: () =>
      apiClient.get<ApiResponse<Assignment[]>>(API.COURSE_ASSIGNMENTS(courseId)).then(r => r.data.data),
  });

  if (isLoading) {
    return <View style={S.center}><ActivityIndicator color={Colors.primary} size="large" /></View>;
  }

  return (
    <View style={S.screen}>
      <View style={S.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={S.back}>‹ Back</Text>
        </TouchableOpacity>
        <Text style={S.title}>Assignments</Text>
        <View style={{ width: 60 }} />
      </View>

      <Text style={S.courseName} numberOfLines={2}>{courseTitle}</Text>

      {!data?.length ? (
        <View style={S.empty}>
          <Ionicons name="clipboard-outline" size={52} color={Colors.gray300} />
          <Text style={S.emptyTitle}>No assignments</Text>
          <Text style={S.emptySub}>This course has no assignments yet</Text>
        </View>
      ) : (
        <FlatList
          data={data}
          keyExtractor={item => String(item.id)}
          contentContainerStyle={S.list}
          renderItem={({ item }) => (
            <AssignmentCard
              assignment={item}
              onPress={() => navigation.navigate('AssignmentView', { assignmentId: item.id, courseSlug })}
            />
          )}
        />
      )}
    </View>
  );
}

function AssignmentCard({ assignment, onPress }: { assignment: Assignment; onPress: () => void }) {
  const statusKey = assignment.submission?.status ?? 'not_submitted';
  const cfg = STATUS_CONFIG[statusKey] ?? STATUS_CONFIG.not_submitted;
  const sub = assignment.submission;

  return (
    <TouchableOpacity style={S.card} onPress={onPress} activeOpacity={0.75}>
      <View style={S.cardLeft}>
        <View style={[S.statusIcon, { backgroundColor: cfg.bg }]}>
          <Ionicons name={cfg.icon} size={20} color={cfg.color} />
        </View>
      </View>
      <View style={S.cardBody}>
        <View style={S.cardRow}>
          <Text style={S.cardTitle} numberOfLines={2}>{assignment.title}</Text>
          {assignment.is_required && (
            <View style={S.requiredBadge}>
              <Text style={S.requiredText}>Required</Text>
            </View>
          )}
        </View>
        <View style={S.metaRow}>
          <Text style={S.scoreMeta}>Max {assignment.max_score} pts · Pass {assignment.pass_score} pts</Text>
        </View>
        <View style={S.statusRow}>
          <View style={[S.statusBadge, { backgroundColor: cfg.bg }]}>
            <Text style={[S.statusText, { color: cfg.color }]}>{cfg.label}</Text>
          </View>
          {sub?.score !== null && sub?.score !== undefined && (
            <Text style={[S.scoreResult, { color: sub.passed ? Colors.success : Colors.error }]}>
              {sub.score} / {assignment.max_score}
            </Text>
          )}
        </View>
      </View>
      <Ionicons name="chevron-forward" size={18} color={Colors.gray300} />
    </TouchableOpacity>
  );
}

const S = StyleSheet.create({
  screen:       { flex: 1, backgroundColor: Colors.surface },
  center:       { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header:       { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', backgroundColor: Colors.white, paddingHorizontal: Spacing[4], paddingTop: 56, paddingBottom: Spacing[3], borderBottomWidth: 1, borderBottomColor: Colors.gray100 },
  back:         { fontSize: Typography.sizes.base, color: Colors.primary, fontWeight: Typography.weights.medium, width: 60 },
  title:        { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  courseName:   { fontSize: Typography.sizes.sm, color: Colors.textMuted, paddingHorizontal: Spacing[4], paddingVertical: Spacing[3], backgroundColor: Colors.white, borderBottomWidth: 1, borderBottomColor: Colors.gray100 },
  empty:        { flex: 1, justifyContent: 'center', alignItems: 'center', paddingHorizontal: Spacing[8], gap: Spacing[4] },
  emptyTitle:   { fontSize: Typography.sizes.xl, fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  emptySub:     { fontSize: Typography.sizes.sm, color: Colors.textSecondary, textAlign: 'center' },
  list:         { padding: Spacing[4], gap: Spacing[3] },
  card:         { flexDirection: 'row', alignItems: 'center', backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], gap: Spacing[3], ...Shadows.sm },
  cardLeft:     { justifyContent: 'flex-start', paddingTop: 2 },
  statusIcon:   { width: 44, height: 44, borderRadius: Radii.full, alignItems: 'center', justifyContent: 'center' },
  cardBody:     { flex: 1 },
  cardRow:      { flexDirection: 'row', alignItems: 'flex-start', gap: Spacing[2], marginBottom: 4 },
  cardTitle:    { flex: 1, fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, lineHeight: 20 },
  requiredBadge:{ backgroundColor: '#fee2e2', borderRadius: Radii.full, paddingHorizontal: 8, paddingVertical: 2 },
  requiredText: { fontSize: 10, color: Colors.error, fontWeight: Typography.weights.semibold },
  metaRow:      { marginBottom: Spacing[2] },
  scoreMeta:    { fontSize: Typography.sizes.xs, color: Colors.textMuted },
  statusRow:    { flexDirection: 'row', alignItems: 'center', gap: Spacing[2] },
  statusBadge:  { borderRadius: Radii.full, paddingHorizontal: 8, paddingVertical: 3 },
  statusText:   { fontSize: 10, fontWeight: Typography.weights.semibold },
  scoreResult:  { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.bold },
});
