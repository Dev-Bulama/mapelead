import React, { useState } from 'react';
import {
  View, Text, ScrollView, TouchableOpacity, TextInput,
  StyleSheet, ActivityIndicator, Alert, Platform,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import * as DocumentPicker from 'expo-document-picker';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { extractApiError } from '@/api/client';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { ApiResponse, RootStackParamList } from '@/types';

type Props = NativeStackScreenProps<RootStackParamList, 'AssignmentView'>;

interface Assignment {
  id: number; title: string; description: string | null; instructions: string | null;
  max_score: number; pass_score: number; is_required: boolean;
  allowed_types: string[]; max_file_mb: number;
  submission: {
    id: number; status: string; score: number | null; feedback: string | null;
    passed: boolean; submitted_at: string; graded_at: string | null;
  } | null;
}

export default function AssignmentScreen({ route, navigation }: Props) {
  const { assignmentId } = route.params;
  const qc = useQueryClient();

  const [notes, setNotes] = useState('');
  const [file,  setFile]  = useState<{ name: string; uri: string; type: string } | null>(null);

  const { data: assignment, isLoading } = useQuery({
    queryKey: ['assignment', assignmentId],
    queryFn: () =>
      apiClient.get<ApiResponse<Assignment>>(API.ASSIGNMENT(assignmentId)).then((r) => r.data.data),
  });

  const { mutate: submit, isPending: submitting } = useMutation({
    mutationFn: async () => {
      const form = new FormData();
      if (notes.trim()) form.append('notes', notes.trim());
      if (file) {
        form.append('file', {
          uri:  file.uri,
          name: file.name,
          type: file.type,
        } as any);
      }
      return apiClient.post<ApiResponse<unknown>>(API.ASSIGNMENT_SUBMIT(assignmentId), form, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
    },
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['assignment', assignmentId] });
      Alert.alert('Submitted!', 'Your assignment has been submitted for grading.');
    },
    onError: (err) => Alert.alert('Error', extractApiError(err)),
  });

  const pickFile = async () => {
    try {
      const result = await DocumentPicker.getDocumentAsync({
        type: assignment?.allowed_types?.length
          ? assignment.allowed_types.map((t) => `application/${t}`)
          : '*/*',
        copyToCacheDirectory: true,
      });
      if (!result.canceled && result.assets?.[0]) {
        const asset = result.assets[0];
        setFile({ name: asset.name, uri: asset.uri, type: asset.mimeType ?? 'application/octet-stream' });
      }
    } catch {
      Alert.alert('Error', 'Could not open file picker');
    }
  };

  const handleSubmit = () => {
    if (!notes.trim() && !file) {
      Alert.alert('Nothing to submit', 'Please add a note or attach a file.');
      return;
    }
    Alert.alert('Submit assignment?', 'You can only submit once.', [
      { text: 'Cancel', style: 'cancel' },
      { text: 'Submit', onPress: () => submit() },
    ]);
  };

  if (isLoading) {
    return <View style={s.center}><ActivityIndicator color={Colors.primary} size="large" /></View>;
  }
  if (!assignment) {
    return <View style={s.center}><Text>Assignment not found</Text></View>;
  }

  const already = assignment.submission && ['submitted', 'graded'].includes(assignment.submission.status);

  return (
    <View style={s.flex}>
      <View style={s.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={s.backLink}>‹ Back</Text>
        </TouchableOpacity>
        <Text style={s.headerTitle} numberOfLines={1}>{assignment.title}</Text>
        <View style={{ width: 60 }} />
      </View>

      <ScrollView contentContainerStyle={s.scroll}>
        {/* Info */}
        <View style={s.card}>
          <View style={s.badgeRow}>
            {assignment.is_required && <View style={s.requiredBadge}><Text style={s.requiredText}>Required</Text></View>}
            <View style={s.scoreBadge}><Text style={s.scoreText}>Max: {assignment.max_score} pts · Pass: {assignment.pass_score} pts</Text></View>
          </View>
          {assignment.description && <Text style={s.desc}>{assignment.description}</Text>}
          {assignment.instructions && (
            <View style={s.instructionsBox}>
              <Text style={s.instructionsTitle}>Instructions</Text>
              <Text style={s.instructionsText}>{assignment.instructions}</Text>
            </View>
          )}
          {assignment.allowed_types.length > 0 && (
            <Text style={s.meta}>Accepted formats: {assignment.allowed_types.join(', ')}</Text>
          )}
          <Text style={s.meta}>Max file size: {assignment.max_file_mb} MB</Text>
        </View>

        {/* Existing submission */}
        {assignment.submission && (
          <View style={[s.card, s.submissionCard]}>
            <Text style={s.submissionTitle}>Your Submission</Text>
            <StatusRow label="Status" value={assignment.submission.status} />
            {assignment.submission.score !== null && (
              <StatusRow label="Score" value={`${assignment.submission.score} / ${assignment.max_score}`} />
            )}
            {assignment.submission.passed && (
              <Text style={s.passedTag}>✅ Passed</Text>
            )}
            {assignment.submission.feedback && (
              <View style={s.feedbackBox}>
                <Text style={s.feedbackTitle}>Instructor Feedback</Text>
                <Text style={s.feedbackText}>{assignment.submission.feedback}</Text>
              </View>
            )}
            <Text style={s.submittedAt}>
              Submitted: {new Date(assignment.submission.submitted_at).toLocaleDateString()}
            </Text>
            {assignment.submission.graded_at && (
              <Text style={s.submittedAt}>
                Graded: {new Date(assignment.submission.graded_at).toLocaleDateString()}
              </Text>
            )}
          </View>
        )}

        {/* Submission form */}
        {!already && (
          <View style={s.card}>
            <Text style={s.formTitle}>Submit Your Work</Text>

            <Text style={s.fieldLabel}>Notes / Answer</Text>
            <TextInput
              style={s.notesInput}
              value={notes}
              onChangeText={setNotes}
              placeholder="Write your answer or notes here…"
              placeholderTextColor={Colors.gray400}
              multiline
              numberOfLines={6}
              textAlignVertical="top"
            />

            <Text style={s.fieldLabel}>Attach File (optional)</Text>
            <TouchableOpacity style={s.filePickerBtn} onPress={pickFile}>
              <Text style={s.filePickerText}>
                {file ? `📎 ${file.name}` : '+ Choose file'}
              </Text>
            </TouchableOpacity>
            {file && (
              <TouchableOpacity onPress={() => setFile(null)}>
                <Text style={s.removeFile}>Remove file</Text>
              </TouchableOpacity>
            )}

            <TouchableOpacity
              style={[s.submitBtn, submitting && s.submitBtnDisabled]}
              onPress={handleSubmit}
              disabled={submitting}
            >
              <Text style={s.submitBtnText}>{submitting ? 'Submitting…' : 'Submit Assignment'}</Text>
            </TouchableOpacity>
          </View>
        )}
      </ScrollView>
    </View>
  );
}

function StatusRow({ label, value }: { label: string; value: string }) {
  return (
    <View style={s.statusRow}>
      <Text style={s.statusLabel}>{label}:</Text>
      <Text style={s.statusValue}>{value}</Text>
    </View>
  );
}

const s = StyleSheet.create({
  flex:             { flex: 1, backgroundColor: Colors.surface },
  center:           { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header:           { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingTop: Spacing[12], paddingHorizontal: Spacing[4], paddingBottom: Spacing[3], backgroundColor: Colors.white, borderBottomWidth: 1, borderBottomColor: Colors.border },
  backLink:         { fontSize: Typography.sizes.base, color: Colors.primary, fontWeight: Typography.weights.medium, width: 60 },
  headerTitle:      { flex: 1, fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, textAlign: 'center' },
  scroll:           { padding: Spacing[4] },
  card:             { backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], marginBottom: Spacing[4], ...Shadows.sm },
  badgeRow:         { flexDirection: 'row', gap: Spacing[2], marginBottom: Spacing[3], flexWrap: 'wrap' },
  requiredBadge:    { backgroundColor: Colors.error, borderRadius: Radii.full, paddingHorizontal: Spacing[3], paddingVertical: Spacing[1] },
  requiredText:     { color: Colors.white, fontSize: Typography.sizes.xs, fontWeight: Typography.weights.semibold },
  scoreBadge:       { backgroundColor: Colors.gray100, borderRadius: Radii.full, paddingHorizontal: Spacing[3], paddingVertical: Spacing[1] },
  scoreText:        { color: Colors.textSecondary, fontSize: Typography.sizes.xs },
  desc:             { fontSize: Typography.sizes.base, color: Colors.textSecondary, lineHeight: 24, marginBottom: Spacing[3] },
  instructionsBox:  { backgroundColor: Colors.surface, borderRadius: Radii.lg, padding: Spacing[3], marginBottom: Spacing[3] },
  instructionsTitle:{ fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: Spacing[1] },
  instructionsText: { fontSize: Typography.sizes.sm, color: Colors.textSecondary, lineHeight: 22 },
  meta:             { fontSize: Typography.sizes.xs, color: Colors.textMuted, marginTop: Spacing[1] },
  submissionCard:   { borderLeftWidth: 4, borderLeftColor: Colors.primary },
  submissionTitle:  { fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold, color: Colors.textPrimary, marginBottom: Spacing[3] },
  statusRow:        { flexDirection: 'row', marginBottom: Spacing[2] },
  statusLabel:      { fontSize: Typography.sizes.sm, color: Colors.textMuted, width: 60 },
  statusValue:      { fontSize: Typography.sizes.sm, color: Colors.textPrimary, fontWeight: Typography.weights.medium, textTransform: 'capitalize' },
  passedTag:        { fontSize: Typography.sizes.sm, color: Colors.success, fontWeight: Typography.weights.semibold, marginVertical: Spacing[2] },
  feedbackBox:      { backgroundColor: Colors.surface, borderRadius: Radii.lg, padding: Spacing[3], marginTop: Spacing[2] },
  feedbackTitle:    { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: Spacing[1] },
  feedbackText:     { fontSize: Typography.sizes.sm, color: Colors.textSecondary },
  submittedAt:      { fontSize: Typography.sizes.xs, color: Colors.textMuted, marginTop: Spacing[1] },
  formTitle:        { fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold, color: Colors.textPrimary, marginBottom: Spacing[4] },
  fieldLabel:       { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.medium, color: Colors.gray700, marginBottom: Spacing[1] },
  notesInput:       { borderWidth: 1, borderColor: Colors.gray300, borderRadius: Radii.lg, paddingHorizontal: Spacing[4], paddingTop: Spacing[3], fontSize: Typography.sizes.base, color: Colors.textPrimary, minHeight: 120, marginBottom: Spacing[4] },
  filePickerBtn:    { borderWidth: 1.5, borderColor: Colors.primary, borderStyle: 'dashed', borderRadius: Radii.lg, paddingVertical: Spacing[4], alignItems: 'center', marginBottom: Spacing[2] },
  filePickerText:   { color: Colors.primary, fontSize: Typography.sizes.base, fontWeight: Typography.weights.medium },
  removeFile:       { color: Colors.error, fontSize: Typography.sizes.sm, textAlign: 'center', marginBottom: Spacing[3] },
  submitBtn:        { backgroundColor: Colors.primary, borderRadius: Radii.lg, paddingVertical: Spacing[4], alignItems: 'center', marginTop: Spacing[4] },
  submitBtnDisabled:{ opacity: 0.6 },
  submitBtnText:    { color: Colors.white, fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold },
});
