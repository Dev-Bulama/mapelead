import React, { useState } from 'react';
import {
  View, Text, FlatList, TouchableOpacity, StyleSheet,
  ActivityIndicator, RefreshControl, Alert, Modal, ScrollView, TextInput,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import { formatDate } from '@/utils/time';
import type { ApiResponse, RootStackParamList } from '@/types';

type Nav = NativeStackNavigationProp<RootStackParamList>;

interface Note {
  id:         number;
  content:    string;
  lesson_id:  number;
  course_id:  number | null;
  lesson:     { id: number; title: string; course?: { slug: string } | null } | null;
  updated_at: string;
}

export default function NotesScreen() {
  const navigation = useNavigation<Nav>();
  const qc = useQueryClient();
  const [selectedNote, setSelectedNote] = useState<Note | null>(null);
  const [editing,      setEditing]      = useState(false);
  const [editContent,  setEditContent]  = useState('');

  const { data, isLoading, refetch, isRefetching } = useQuery({
    queryKey: ['notes'],
    queryFn:  () => apiClient.get<ApiResponse<Note[]>>(API.NOTES).then(r => r.data.data),
    staleTime: 30_000,
  });

  const { mutate: deleteNote } = useMutation({
    mutationFn: (lessonId: number) => apiClient.delete(API.LESSON_NOTE(lessonId)),
    onSuccess:  () => {
      qc.invalidateQueries({ queryKey: ['notes'] });
      setSelectedNote(null);
      setEditing(false);
    },
    onError: () => Alert.alert('Error', 'Could not delete note. Please try again.'),
  });

  const { mutate: saveNote, isPending: saving } = useMutation({
    mutationFn: ({ lessonId, content }: { lessonId: number; content: string }) =>
      apiClient.post(API.LESSON_NOTE(lessonId), { content }),
    onSuccess: (_, vars) => {
      qc.invalidateQueries({ queryKey: ['notes'] });
      setEditing(false);
      setSelectedNote(prev => prev ? { ...prev, content: vars.content } : null);
    },
    onError: () => Alert.alert('Error', 'Could not save note. Please try again.'),
  });

  const openNote = (note: Note) => {
    setSelectedNote(note);
    setEditContent(note.content);
    setEditing(false);
  };

  const closeNote = () => {
    setSelectedNote(null);
    setEditing(false);
  };

  const startEditing = () => {
    setEditContent(selectedNote?.content ?? '');
    setEditing(true);
  };

  const handleSave = () => {
    if (!selectedNote) return;
    if (!editContent.trim()) { Alert.alert('Empty', 'Note cannot be empty.'); return; }
    saveNote({ lessonId: selectedNote.lesson_id, content: editContent.trim() });
  };

  const handleDelete = (note: Note) => {
    Alert.alert(
      'Delete Note',
      `Delete your note for "${note.lesson?.title ?? 'this lesson'}"?`,
      [
        { text: 'Cancel', style: 'cancel' },
        { text: 'Delete', style: 'destructive', onPress: () => deleteNote(note.lesson_id) },
      ]
    );
  };

  if (isLoading) {
    return <View style={S.center}><ActivityIndicator color={Colors.primary} size="large" /></View>;
  }

  return (
    <View style={S.screen}>
      <View style={S.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={S.backBtn}>
          <Ionicons name="chevron-back" size={22} color={Colors.primary} />
        </TouchableOpacity>
        <Text style={S.headerTitle}>My Notes</Text>
        <Text style={S.count}>{(data ?? []).length}</Text>
      </View>

      <FlatList
        data={data ?? []}
        keyExtractor={item => String(item.id)}
        contentContainerStyle={S.list}
        refreshControl={<RefreshControl refreshing={isRefetching} onRefresh={refetch} tintColor={Colors.primary} />}
        renderItem={({ item }) => (
          <TouchableOpacity style={S.card} onPress={() => openNote(item)} activeOpacity={0.8}>
            <View style={S.cardHeader}>
              <Text style={S.lessonTitle} numberOfLines={1}>{item.lesson?.title ?? 'Lesson'}</Text>
              <Text style={S.date}>{formatDate(item.updated_at)}</Text>
            </View>
            <Text style={S.preview} numberOfLines={3}>{item.content}</Text>
            <View style={S.cardFooter}>
              <TouchableOpacity
                onPress={() => {
                  const slug = item.lesson?.course?.slug ?? '';
                  navigation.navigate('LessonView', { lessonId: item.lesson_id, courseSlug: slug });
                }}
              >
                <Text style={S.goToLesson}>Go to lesson →</Text>
              </TouchableOpacity>
              <TouchableOpacity onPress={() => handleDelete(item)} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
                <Ionicons name="trash-outline" size={17} color={Colors.error} />
              </TouchableOpacity>
            </View>
          </TouchableOpacity>
        )}
        ListEmptyComponent={
          <View style={S.empty}>
            <Ionicons name="document-text-outline" size={52} color={Colors.gray300} />
            <Text style={S.emptyTitle}>No notes yet</Text>
            <Text style={S.emptySub}>Take notes while studying lessons and find them all here</Text>
          </View>
        }
      />

      {/* Full note modal */}
      <Modal
        visible={!!selectedNote}
        animationType="slide"
        transparent
        onRequestClose={closeNote}
      >
        <View style={S.modalOverlay}>
          <View style={S.modalSheet}>
            <View style={S.modalHandle} />
            <View style={S.modalTitleRow}>
              <Text style={S.modalLessonTitle} numberOfLines={2}>{selectedNote?.lesson?.title}</Text>
              <TouchableOpacity onPress={closeNote}>
                <Ionicons name="close" size={24} color={Colors.gray400} />
              </TouchableOpacity>
            </View>
            <Text style={S.modalDate}>{formatDate(selectedNote?.updated_at ?? '')}</Text>
            <ScrollView style={S.modalScroll}>
              {editing ? (
                <TextInput
                  style={S.modalEditInput}
                  value={editContent}
                  onChangeText={setEditContent}
                  multiline
                  autoFocus
                  placeholder="Write your note…"
                  placeholderTextColor={Colors.gray400}
                  textAlignVertical="top"
                />
              ) : (
                <Text style={S.modalContent}>{selectedNote?.content}</Text>
              )}
            </ScrollView>
            {editing ? (
              <View style={S.modalEditActions}>
                <TouchableOpacity style={S.modalCancelBtn} onPress={() => setEditing(false)}>
                  <Text style={S.modalCancelText}>Cancel</Text>
                </TouchableOpacity>
                <TouchableOpacity
                  style={[S.modalSaveBtn, saving && { opacity: 0.6 }]}
                  onPress={handleSave}
                  disabled={saving}
                >
                  {saving ? (
                    <ActivityIndicator size="small" color={Colors.white} />
                  ) : (
                    <Text style={S.modalSaveText}>Save</Text>
                  )}
                </TouchableOpacity>
              </View>
            ) : (
              <View style={S.modalViewActions}>
                <TouchableOpacity style={S.modalEditBtn} onPress={startEditing}>
                  <Ionicons name="pencil-outline" size={16} color={Colors.primary} style={{ marginRight: 6 }} />
                  <Text style={S.modalEditText}>Edit Note</Text>
                </TouchableOpacity>
                <TouchableOpacity style={S.modalDeleteBtn} onPress={() => selectedNote && handleDelete(selectedNote)}>
                  <Text style={S.modalDeleteText}>Delete</Text>
                </TouchableOpacity>
              </View>
            )}
          </View>
        </View>
      </Modal>
    </View>
  );
}

const S = StyleSheet.create({
  screen:           { flex: 1, backgroundColor: Colors.surface },
  center:           { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header:           { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingHorizontal: Spacing[4], paddingTop: Spacing[12], paddingBottom: Spacing[3], backgroundColor: Colors.white, borderBottomWidth: 1, borderBottomColor: Colors.gray100 },
  backBtn:          { width: 40, alignItems: 'flex-start' },
  headerTitle:      { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  count:            { fontSize: Typography.sizes.sm, color: Colors.textMuted, width: 60, textAlign: 'right' },
  list:             { padding: Spacing[4] },
  card:             { backgroundColor: Colors.white, borderRadius: Radii.xl, marginBottom: Spacing[3], padding: Spacing[4], ...Shadows.sm },
  cardHeader:       { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: Spacing[2] },
  lessonTitle:      { flex: 1, fontSize: Typography.sizes.sm, fontWeight: Typography.weights.bold, color: Colors.primary, marginRight: Spacing[2] },
  date:             { fontSize: Typography.sizes.xs, color: Colors.textMuted },
  preview:          { fontSize: Typography.sizes.sm, color: Colors.textSecondary, lineHeight: 20 },
  cardFooter:       { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginTop: Spacing[3] },
  goToLesson:       { fontSize: Typography.sizes.xs, color: Colors.primary, fontWeight: Typography.weights.medium },
  empty:            { alignItems: 'center', paddingTop: Spacing[12], gap: Spacing[3] },
  emptyTitle:       { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  emptySub:         { fontSize: Typography.sizes.sm, color: Colors.textMuted, textAlign: 'center', paddingHorizontal: Spacing[8] },
  modalOverlay:     { flex: 1, justifyContent: 'flex-end', backgroundColor: 'rgba(0,0,0,0.45)' },
  modalSheet:       { backgroundColor: Colors.white, borderTopLeftRadius: 24, borderTopRightRadius: 24, padding: Spacing[5], maxHeight: '80%' },
  modalHandle:      { width: 40, height: 4, borderRadius: 2, backgroundColor: Colors.gray200, alignSelf: 'center', marginBottom: Spacing[4] },
  modalTitleRow:    { flexDirection: 'row', alignItems: 'flex-start', justifyContent: 'space-between', marginBottom: Spacing[1] },
  modalLessonTitle: { flex: 1, fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold, color: Colors.primary, marginRight: Spacing[2] },
  modalDate:        { fontSize: Typography.sizes.xs, color: Colors.textMuted, marginBottom: Spacing[4] },
  modalScroll:      { maxHeight: 300 },
  modalContent:     { fontSize: Typography.sizes.base, color: Colors.textPrimary, lineHeight: 24 },
  modalEditInput:   { fontSize: Typography.sizes.base, color: Colors.textPrimary, lineHeight: 24, minHeight: 120, borderWidth: 1, borderColor: Colors.gray300, borderRadius: Radii.lg, padding: Spacing[3] },
  modalViewActions: { flexDirection: 'row', gap: Spacing[3], marginTop: Spacing[4] },
  modalEditBtn:     { flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', borderWidth: 1.5, borderColor: Colors.primary, borderRadius: Radii.xl, paddingVertical: 14 },
  modalEditText:    { color: Colors.primary, fontWeight: Typography.weights.semibold },
  modalDeleteBtn:   { flex: 1, borderWidth: 1.5, borderColor: Colors.error, borderRadius: Radii.xl, paddingVertical: 14, alignItems: 'center' },
  modalDeleteText:  { color: Colors.error, fontWeight: Typography.weights.semibold },
  modalEditActions: { flexDirection: 'row', gap: Spacing[3], marginTop: Spacing[4] },
  modalCancelBtn:   { flex: 1, borderWidth: 1.5, borderColor: Colors.gray300, borderRadius: Radii.xl, paddingVertical: 14, alignItems: 'center' },
  modalCancelText:  { color: Colors.textSecondary, fontWeight: Typography.weights.semibold },
  modalSaveBtn:     { flex: 1, backgroundColor: Colors.primary, borderRadius: Radii.xl, paddingVertical: 14, alignItems: 'center' },
  modalSaveText:    { color: Colors.white, fontWeight: Typography.weights.semibold },
});
