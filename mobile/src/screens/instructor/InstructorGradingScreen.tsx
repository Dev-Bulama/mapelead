import React, { useState } from 'react';
import {
  View, Text, ScrollView, TouchableOpacity, TextInput,
  StyleSheet, ActivityIndicator, Alert, Image,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { extractApiError } from '@/api/client';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { ApiResponse, RootStackParamList } from '@/types';

type Props = NativeStackScreenProps<RootStackParamList, 'InstructorGrading'>;

interface SubmissionDetail {
  id: number;
  status: string;
  notes: string | null;
  file_name: string | null;
  has_file: boolean;
  score: number | null;
  feedback: string | null;
  passed: boolean | null;
  submitted_at: string;
  graded_at: string | null;
  user: { id: number; full_name: string; email: string; avatar_url: string | null };
  assignment: {
    id: number; title: string; description: string | null;
    max_score: number; pass_score: number;
  } | null;
}

export default function InstructorGradingScreen({ route, navigation }: Props) {
  const { submissionId } = route.params;
  const qc = useQueryClient();

  const [score,    setScore]    = useState('');
  const [feedback, setFeedback] = useState('');
  const [edited,   setEdited]   = useState(false);

  const { data: sub, isLoading } = useQuery({
    queryKey: ['instructor-submission', submissionId],
    queryFn: () =>
      apiClient.get<ApiResponse<SubmissionDetail>>(API.INSTRUCTOR_SUBMISSION(submissionId))
        .then(r => r.data.data),
    onSuccess: (data) => {
      if (data.score !== null && !edited) {
        setScore(String(data.score));
        setFeedback(data.feedback ?? '');
      }
    },
  } as any);

  const { mutate: grade, isPending: grading } = useMutation({
    mutationFn: () => apiClient.put(API.INSTRUCTOR_GRADE(submissionId), {
      score:    parseFloat(score),
      feedback: feedback.trim() || undefined,
    }),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['instructor-submission', submissionId] });
      qc.invalidateQueries({ queryKey: ['instructor', 'dashboard'] });
      Alert.alert('Graded!', 'Submission has been graded successfully.', [
        { text: 'OK', onPress: () => navigation.goBack() },
      ]);
    },
    onError: (err) => Alert.alert('Error', extractApiError(err)),
  });

  const handleGrade = () => {
    const parsed = parseFloat(score);
    if (isNaN(parsed) || parsed < 0) {
      Alert.alert('Invalid score', 'Please enter a valid score.');
      return;
    }
    if (sub?.assignment && parsed > sub.assignment.max_score) {
      Alert.alert('Score too high', `Max score is ${sub.assignment.max_score}.`);
      return;
    }
    grade();
  };

  if (isLoading || !sub) {
    return <View style={S.center}><ActivityIndicator color={Colors.primary} size="large" /></View>;
  }

  const alreadyGraded = sub.status === 'graded';

  return (
    <View style={S.screen}>
      <View style={S.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={S.back}>‹ Back</Text>
        </TouchableOpacity>
        <Text style={S.headerTitle}>Grade Submission</Text>
        <View style={{ width: 60 }} />
      </View>

      <ScrollView contentContainerStyle={S.scroll} showsVerticalScrollIndicator={false}>

        {/* Assignment info */}
        <View style={S.card}>
          <Text style={S.cardLabel}>Assignment</Text>
          <Text style={S.assignmentTitle}>{sub.assignment?.title ?? '—'}</Text>
          {sub.assignment?.description ? (
            <Text style={S.assignmentDesc}>{sub.assignment.description}</Text>
          ) : null}
          <View style={S.scoreInfo}>
            <View style={S.scoreChip}>
              <Text style={S.scoreChipText}>Max {sub.assignment?.max_score ?? 0} pts</Text>
            </View>
            <View style={[S.scoreChip, { backgroundColor: '#fef3c7' }]}>
              <Text style={[S.scoreChipText, { color: '#d97706' }]}>Pass {sub.assignment?.pass_score ?? 0} pts</Text>
            </View>
          </View>
        </View>

        {/* Student */}
        <View style={S.card}>
          <Text style={S.cardLabel}>Student</Text>
          <View style={S.studentRow}>
            <View style={S.avatar}>
              {sub.user.avatar_url ? (
                <Image source={{ uri: sub.user.avatar_url }} style={S.avatarImg} />
              ) : (
                <Text style={S.avatarInitial}>{sub.user.full_name[0]?.toUpperCase() ?? '?'}</Text>
              )}
            </View>
            <View style={{ flex: 1 }}>
              <Text style={S.studentName}>{sub.user.full_name}</Text>
              <Text style={S.studentEmail}>{sub.user.email}</Text>
            </View>
          </View>
          <Text style={S.submittedAt}>
            Submitted {new Date(sub.submitted_at).toLocaleDateString('en-NG', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
          </Text>
        </View>

        {/* Submission content */}
        <View style={S.card}>
          <Text style={S.cardLabel}>Submission</Text>
          {sub.notes ? (
            <View style={S.notesBox}>
              <Text style={S.notesText}>{sub.notes}</Text>
            </View>
          ) : (
            <Text style={S.noNotes}>No written notes</Text>
          )}
          {sub.has_file && (
            <View style={S.fileRow}>
              <Ionicons name="attach-outline" size={16} color={Colors.primary} />
              <Text style={S.fileName}>{sub.file_name ?? 'Attachment'}</Text>
            </View>
          )}
          {!sub.notes && !sub.has_file && (
            <Text style={S.noNotes}>No content submitted</Text>
          )}
        </View>

        {/* Grading form */}
        <View style={S.card}>
          <Text style={S.cardLabel}>{alreadyGraded ? 'Grade (edit)' : 'Grade Submission'}</Text>

          {alreadyGraded && sub.passed !== null && (
            <View style={[S.gradedBanner, { backgroundColor: sub.passed ? '#dcfce7' : '#fee2e2' }]}>
              <Ionicons
                name={sub.passed ? 'checkmark-circle' : 'close-circle'}
                size={16}
                color={sub.passed ? Colors.success : Colors.error}
              />
              <Text style={[S.gradedBannerText, { color: sub.passed ? Colors.success : Colors.error }]}>
                {sub.passed ? 'Passed' : 'Failed'} — {sub.score} / {sub.assignment?.max_score}
              </Text>
            </View>
          )}

          <Text style={S.fieldLabel}>Score *</Text>
          <TextInput
            style={S.scoreInput}
            value={score}
            onChangeText={(v) => { setScore(v); setEdited(true); }}
            placeholder={`0 – ${sub.assignment?.max_score ?? 100}`}
            placeholderTextColor={Colors.gray400}
            keyboardType="decimal-pad"
          />

          <Text style={S.fieldLabel}>Feedback (optional)</Text>
          <TextInput
            style={S.feedbackInput}
            value={feedback}
            onChangeText={(v) => { setFeedback(v); setEdited(true); }}
            placeholder="Write feedback for the student…"
            placeholderTextColor={Colors.gray400}
            multiline
            numberOfLines={5}
            textAlignVertical="top"
          />

          <TouchableOpacity
            style={[S.gradeBtn, grading && S.gradeBtnDisabled]}
            onPress={handleGrade}
            disabled={grading}
          >
            {grading ? (
              <ActivityIndicator size="small" color={Colors.white} />
            ) : (
              <>
                <Ionicons name="checkmark-circle-outline" size={18} color={Colors.white} style={{ marginRight: 6 }} />
                <Text style={S.gradeBtnText}>{alreadyGraded ? 'Update Grade' : 'Submit Grade'}</Text>
              </>
            )}
          </TouchableOpacity>
        </View>

        <View style={{ height: Spacing[8] }} />
      </ScrollView>
    </View>
  );
}

const S = StyleSheet.create({
  screen:           { flex: 1, backgroundColor: Colors.surface },
  center:           { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header:           { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', backgroundColor: Colors.white, paddingHorizontal: Spacing[4], paddingTop: 56, paddingBottom: Spacing[3], borderBottomWidth: 1, borderBottomColor: Colors.gray100 },
  back:             { fontSize: Typography.sizes.base, color: Colors.primary, fontWeight: Typography.weights.medium, width: 60 },
  headerTitle:      { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  scroll:           { padding: Spacing[4] },
  card:             { backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], marginBottom: Spacing[4], ...Shadows.sm },
  cardLabel:        { fontSize: Typography.sizes.xs, fontWeight: Typography.weights.bold, color: Colors.textMuted, textTransform: 'uppercase', letterSpacing: 0.8, marginBottom: Spacing[3] },
  assignmentTitle:  { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.bold, color: Colors.textPrimary, marginBottom: Spacing[2] },
  assignmentDesc:   { fontSize: Typography.sizes.sm, color: Colors.textSecondary, lineHeight: 20, marginBottom: Spacing[3] },
  scoreInfo:        { flexDirection: 'row', gap: Spacing[2], marginTop: Spacing[2] },
  scoreChip:        { backgroundColor: Colors.gray100, borderRadius: Radii.full, paddingHorizontal: Spacing[3], paddingVertical: Spacing[1] },
  scoreChipText:    { fontSize: Typography.sizes.xs, color: Colors.textSecondary, fontWeight: Typography.weights.medium },
  studentRow:       { flexDirection: 'row', alignItems: 'center', gap: Spacing[3], marginBottom: Spacing[2] },
  avatar:           { width: 44, height: 44, borderRadius: 22, backgroundColor: Colors.primary, alignItems: 'center', justifyContent: 'center', overflow: 'hidden' },
  avatarImg:        { width: 44, height: 44 },
  avatarInitial:    { color: Colors.white, fontWeight: Typography.weights.bold, fontSize: Typography.sizes.lg },
  studentName:      { fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  studentEmail:     { fontSize: Typography.sizes.xs, color: Colors.textMuted },
  submittedAt:      { fontSize: Typography.sizes.xs, color: Colors.textMuted, marginTop: Spacing[1] },
  notesBox:         { backgroundColor: Colors.surface, borderRadius: Radii.lg, padding: Spacing[3], marginBottom: Spacing[2] },
  notesText:        { fontSize: Typography.sizes.base, color: Colors.textSecondary, lineHeight: 24 },
  noNotes:          { fontSize: Typography.sizes.sm, color: Colors.gray400, fontStyle: 'italic' },
  fileRow:          { flexDirection: 'row', alignItems: 'center', gap: Spacing[2], marginTop: Spacing[2], backgroundColor: Colors.surface, borderRadius: Radii.lg, padding: Spacing[3] },
  fileName:         { fontSize: Typography.sizes.sm, color: Colors.primary, fontWeight: Typography.weights.medium },
  gradedBanner:     { flexDirection: 'row', alignItems: 'center', gap: 6, borderRadius: Radii.lg, padding: Spacing[3], marginBottom: Spacing[4] },
  gradedBannerText: { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold },
  fieldLabel:       { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.medium, color: Colors.gray700, marginBottom: Spacing[1], marginTop: Spacing[3] },
  scoreInput:       { borderWidth: 1.5, borderColor: Colors.gray200, borderRadius: Radii.lg, paddingHorizontal: Spacing[4], paddingVertical: Spacing[3], fontSize: Typography.sizes.xl, fontWeight: Typography.weights.bold, color: Colors.textPrimary, textAlign: 'center' },
  feedbackInput:    { borderWidth: 1.5, borderColor: Colors.gray200, borderRadius: Radii.lg, paddingHorizontal: Spacing[4], paddingVertical: Spacing[3], fontSize: Typography.sizes.base, color: Colors.textPrimary, minHeight: 120, marginBottom: Spacing[4] },
  gradeBtn:         { backgroundColor: Colors.primary, borderRadius: Radii.xl, paddingVertical: Spacing[4], alignItems: 'center', flexDirection: 'row', justifyContent: 'center', marginTop: Spacing[2] },
  gradeBtnDisabled: { opacity: 0.6 },
  gradeBtnText:     { color: Colors.white, fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold },
});
