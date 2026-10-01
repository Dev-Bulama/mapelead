import React, { useState, useRef, useEffect } from 'react';
import {
  View, Text, ScrollView, TouchableOpacity, StyleSheet,
  ActivityIndicator, Alert, TextInput, KeyboardAvoidingView, Platform,
} from 'react-native';
import { WebView } from 'react-native-webview';
import { downloadAttachment } from '@/screens/main/DownloadsScreen';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { extractApiError } from '@/api/client';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { ApiResponse, Lesson, RootStackParamList } from '@/types';

type Props = NativeStackScreenProps<RootStackParamList, 'LessonView'>;

// ── Video URL helpers ──────────────────────────────────────────────────────────

function getYouTubeId(url: string): string | null {
  const patterns = [
    /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/,
  ];
  for (const p of patterns) {
    const m = url.match(p);
    if (m) return m[1];
  }
  return null;
}

function getVimeoId(url: string): string | null {
  const m = url.match(/vimeo\.com\/(?:video\/)?(\d+)/);
  return m ? m[1] : null;
}

function buildEmbedUrl(videoUrl: string, provider: string | null): string | null {
  if (provider === 'youtube' || getYouTubeId(videoUrl)) {
    const id = getYouTubeId(videoUrl);
    if (id) return `https://www.youtube.com/embed/${id}?autoplay=0&rel=0&modestbranding=1`;
  }
  if (provider === 'vimeo' || getVimeoId(videoUrl)) {
    const id = getVimeoId(videoUrl);
    if (id) return `https://player.vimeo.com/video/${id}?autoplay=0`;
  }
  // Direct video file — wrap in HTML5 video
  return null;
}

function directVideoHtml(url: string): string {
  return `<!DOCTYPE html><html><head>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>*{margin:0;padding:0;box-sizing:border-box;background:#000}
  video{width:100%;height:100vh;display:block}</style>
  </head><body>
  <video controls playsinline src="${url}"></video>
  </body></html>`;
}

// ── Attachment Download Button ─────────────────────────────────────────────────

function AttachmentButton({ url, lessonTitle }: { url: string; lessonTitle: string }) {
  const [downloading, setDownloading] = useState(false);
  const [done,        setDone]        = useState(false);

  const handleDownload = async () => {
    if (done) return;
    setDownloading(true);
    try {
      const filename = url.split('/').pop() ?? `${lessonTitle}_attachment`.replace(/\s+/g, '_');
      await downloadAttachment(url, filename);
      setDone(true);
      Alert.alert('Downloaded', 'File saved to Downloads. Find it in the Downloads tab.');
    } catch {
      Alert.alert('Error', 'Download failed. Please try again.');
    } finally {
      setDownloading(false);
    }
  };

  return (
    <TouchableOpacity
      style={[S.attachmentBtn, done && { backgroundColor: '#dcfce7' }]}
      onPress={handleDownload}
      disabled={downloading}
    >
      <Text style={S.attachmentIcon}>{done ? '✅' : downloading ? '⏳' : '📎'}</Text>
      <Text style={[S.attachmentText, done && { color: Colors.success }]}>
        {done ? 'Downloaded' : downloading ? 'Downloading…' : 'Download Attachment'}
      </Text>
    </TouchableOpacity>
  );
}

// ── Video Player ───────────────────────────────────────────────────────────────

function VideoPlayer({ videoUrl, provider }: { videoUrl: string; provider: string | null }) {
  const embedUrl = buildEmbedUrl(videoUrl, provider);

  if (embedUrl) {
    return (
      <WebView
        source={{ uri: embedUrl }}
        style={V.webview}
        allowsFullscreenVideo
        javaScriptEnabled
        mediaPlaybackRequiresUserAction={false}
      />
    );
  }

  // Direct video
  return (
    <WebView
      source={{ html: directVideoHtml(videoUrl) }}
      style={V.webview}
      allowsFullscreenVideo
      javaScriptEnabled
      originWhitelist={['*']}
      mediaPlaybackRequiresUserAction
    />
  );
}

const V = StyleSheet.create({
  webview: { width: '100%', height: 220, backgroundColor: '#000' },
});

// ── Notes Panel ────────────────────────────────────────────────────────────────

function NotesPanel({ lessonId, initialNote }: { lessonId: number; initialNote: string }) {
  const qc = useQueryClient();
  const [text, setText] = useState(initialNote);
  const [dirty, setDirty] = useState(false);

  const { mutate: saveNote, isPending: saving } = useMutation({
    mutationFn: (content: string) => apiClient.post(API.LESSON_NOTE(lessonId), { content }),
    onSuccess: () => {
      setDirty(false);
      qc.invalidateQueries({ queryKey: ['lesson', lessonId] });
    },
    onError: (err) => Alert.alert('Error', extractApiError(err)),
  });

  const { mutate: deleteNote, isPending: deleting } = useMutation({
    mutationFn: () => apiClient.delete(API.LESSON_NOTE(lessonId)),
    onSuccess: () => {
      setText('');
      setDirty(false);
      qc.invalidateQueries({ queryKey: ['lesson', lessonId] });
    },
  });

  return (
    <View style={N.panel}>
      <View style={N.header}>
        <Text style={N.title}>My Notes</Text>
        {text.length > 0 && (
          <TouchableOpacity onPress={() => deleteNote()} disabled={deleting}>
            <Text style={N.deleteBtn}>{deleting ? '…' : 'Delete'}</Text>
          </TouchableOpacity>
        )}
      </View>
      <TextInput
        style={N.input}
        value={text}
        onChangeText={(v) => { setText(v); setDirty(true); }}
        placeholder="Add notes for this lesson…"
        placeholderTextColor={Colors.gray400}
        multiline
        textAlignVertical="top"
      />
      {dirty && (
        <TouchableOpacity
          style={[N.saveBtn, saving && N.saveBtnDisabled]}
          onPress={() => saveNote(text)}
          disabled={saving}
        >
          <Text style={N.saveBtnText}>{saving ? 'Saving…' : 'Save Note'}</Text>
        </TouchableOpacity>
      )}
    </View>
  );
}

const N = StyleSheet.create({
  panel:       { backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], marginBottom: Spacing[4], ...Shadows.sm },
  header:      { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: Spacing[3] },
  title:       { fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  deleteBtn:   { fontSize: Typography.sizes.sm, color: Colors.error },
  input:       { minHeight: 100, borderWidth: 1, borderColor: Colors.gray200, borderRadius: Radii.lg, padding: Spacing[3], fontSize: Typography.sizes.base, color: Colors.textPrimary, backgroundColor: Colors.gray50 },
  saveBtn:     { backgroundColor: Colors.primary, borderRadius: Radii.lg, paddingVertical: 10, alignItems: 'center', marginTop: Spacing[3] },
  saveBtnDisabled: { opacity: 0.6 },
  saveBtnText: { color: Colors.white, fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold },
});

// ── Main Screen ────────────────────────────────────────────────────────────────

interface LessonNav {
  prev: { id: number; title: string } | null;
  next: { id: number; title: string } | null;
}

export default function LessonViewScreen({ route, navigation }: Props) {
  const { lessonId } = route.params;
  const qc = useQueryClient();
  const [showNotes, setShowNotes] = useState(false);

  const { data: lesson, isLoading } = useQuery({
    queryKey: ['lesson', lessonId],
    queryFn: () =>
      apiClient.get<ApiResponse<Lesson>>(API.LESSON(lessonId)).then((r) => r.data.data),
  });

  const { data: lessonNav } = useQuery({
    queryKey: ['lesson-nav', lessonId],
    queryFn: () =>
      apiClient.get<ApiResponse<LessonNav>>(API.LESSON_NAVIGATION(lessonId)).then((r) => r.data.data),
    enabled: !!lesson,
  });

  // Track progress every 30s while screen is open
  const progressRef = useRef(0);
  useEffect(() => {
    if (!lesson?.video_url) return;
    const timer = setInterval(() => {
      progressRef.current += 30;
      apiClient.post(API.LESSON_PROGRESS(lessonId), {
        watch_time_seconds: progressRef.current,
      }).catch(() => {});
    }, 30_000);
    return () => clearInterval(timer);
  }, [lesson?.video_url, lessonId]);

  const { mutate: markComplete, isPending: completing } = useMutation({
    mutationFn: () => apiClient.post(API.LESSON_COMPLETE(lessonId)),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['lesson', lessonId] });
      qc.invalidateQueries({ queryKey: ['enrollments'] });
      Alert.alert('Done!', 'Lesson marked as complete. Keep going!');
    },
    onError: (err) => Alert.alert('Error', extractApiError(err)),
  });

  const { mutate: toggleBookmark } = useMutation({
    mutationFn: () => apiClient.post(API.LESSON_BOOKMARK(lessonId)),
    onSuccess: () => qc.invalidateQueries({ queryKey: ['lesson', lessonId] }),
  });

  if (isLoading) {
    return <View style={S.center}><ActivityIndicator color={Colors.primary} size="large" /></View>;
  }
  if (!lesson) {
    return <View style={S.center}><Text style={S.notFound}>Lesson not found</Text></View>;
  }

  return (
    <KeyboardAvoidingView
      style={S.flex}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      {/* ── Header ── */}
      <View style={S.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
          <Text style={S.backBtn}>‹ Back</Text>
        </TouchableOpacity>
        <View style={S.headerActions}>
          <TouchableOpacity onPress={() => setShowNotes(v => !v)} style={S.headerIconBtn}>
            <Ionicons name={showNotes ? 'document-text' : 'document-text-outline'} size={21} color={Colors.white} />
          </TouchableOpacity>
          <TouchableOpacity onPress={() => toggleBookmark()} style={S.headerIconBtn}>
            <Ionicons name={lesson.is_bookmarked ? 'bookmark' : 'bookmark-outline'} size={21} color={Colors.white} />
          </TouchableOpacity>
        </View>
      </View>

      {/* ── Video (outside scroll so WebView gets full width) ── */}
      {lesson.video_url && (
        <VideoPlayer videoUrl={lesson.video_url} provider={lesson.video_provider} />
      )}

      <ScrollView contentContainerStyle={S.scroll} keyboardShouldPersistTaps="handled">

        {/* Breadcrumb */}
        {lesson.module && (
          <Text style={S.breadcrumb}>{lesson.module.title}</Text>
        )}

        {/* Title */}
        <Text style={S.title}>{lesson.title}</Text>

        {/* Meta row */}
        <View style={S.metaRow}>
          {lesson.type && (
            <View style={S.badge}>
              <Text style={S.badgeText}>{lesson.type.charAt(0).toUpperCase() + lesson.type.slice(1)}</Text>
            </View>
          )}
          {lesson.duration_minutes && (
            <Text style={S.metaText}>{lesson.duration_minutes} min</Text>
          )}
          {lesson.progress?.is_completed && (
            <View style={[S.badge, { backgroundColor: Colors.success }]}>
              <Text style={[S.badgeText, { color: Colors.white }]}>✓ Completed</Text>
            </View>
          )}
        </View>

        {/* Content */}
        {lesson.content && (
          <View style={S.contentBox}>
            <Text style={S.content}>{lesson.content}</Text>
          </View>
        )}

        {/* Attachment */}
        {lesson.attachment_url && (
          <AttachmentButton url={lesson.attachment_url} lessonTitle={lesson.title} />
        )}

        {/* Notes panel */}
        {showNotes && (
          <NotesPanel
            lessonId={lessonId}
            initialNote={lesson.note?.content ?? ''}
          />
        )}

        <View style={{ height: 160 }} />
      </ScrollView>

      {/* ── Footer ── */}
      <View style={S.footer}>
        <View style={S.footerNav}>
          <TouchableOpacity
            style={[S.navBtn, !lessonNav?.prev && S.navBtnDisabled]}
            onPress={() => lessonNav?.prev && navigation.replace('LessonView', { lessonId: lessonNav.prev.id, courseSlug: route.params.courseSlug })}
            disabled={!lessonNav?.prev}
          >
            <Ionicons name="chevron-back" size={18} color={lessonNav?.prev ? Colors.primary : Colors.gray300} />
            <Text style={[S.navBtnText, !lessonNav?.prev && S.navBtnTextDisabled]} numberOfLines={1}>
              {lessonNav?.prev ? lessonNav.prev.title : 'First lesson'}
            </Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[S.navBtn, S.navBtnRight, !lessonNav?.next && S.navBtnDisabled]}
            onPress={() => lessonNav?.next && navigation.replace('LessonView', { lessonId: lessonNav.next.id, courseSlug: route.params.courseSlug })}
            disabled={!lessonNav?.next}
          >
            <Text style={[S.navBtnText, !lessonNav?.next && S.navBtnTextDisabled]} numberOfLines={1}>
              {lessonNav?.next ? lessonNav.next.title : 'Last lesson'}
            </Text>
            <Ionicons name="chevron-forward" size={18} color={lessonNav?.next ? Colors.primary : Colors.gray300} />
          </TouchableOpacity>
        </View>
        {!lesson.progress?.is_completed ? (
          <TouchableOpacity
            style={[S.completeBtn, completing && S.completeBtnDisabled]}
            onPress={() => markComplete()}
            disabled={completing}
          >
            {completing ? (
              <ActivityIndicator size="small" color={Colors.white} />
            ) : (
              <>
                <Ionicons name="checkmark-circle-outline" size={18} color={Colors.white} style={{ marginRight: 6 }} />
                <Text style={S.completeBtnText}>Mark as Complete</Text>
              </>
            )}
          </TouchableOpacity>
        ) : (
          <View style={S.completedBadge}>
            <Ionicons name="checkmark-circle" size={18} color={Colors.white} style={{ marginRight: 6 }} />
            <Text style={S.completedText}>Lesson Completed</Text>
          </View>
        )}
      </View>
    </KeyboardAvoidingView>
  );
}

const S = StyleSheet.create({
  flex:           { flex: 1, backgroundColor: Colors.white },
  center:         { flex: 1, justifyContent: 'center', alignItems: 'center' },
  notFound:       { fontSize: Typography.sizes.base, color: Colors.textSecondary },

  // Header
  header:         { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingHorizontal: Spacing[4], paddingTop: Spacing[12], paddingBottom: Spacing[3], backgroundColor: Colors.white, borderBottomWidth: 1, borderBottomColor: Colors.border },
  backBtn:        { fontSize: Typography.sizes.base, color: Colors.primary, fontWeight: Typography.weights.medium },
  headerActions:  { flexDirection: 'row', gap: 4 },
  headerIconBtn:  { padding: 6 },

  // Scroll
  scroll:         { padding: Spacing[4] },
  breadcrumb:     { fontSize: Typography.sizes.xs, color: Colors.primary, fontWeight: Typography.weights.semibold, textTransform: 'uppercase', letterSpacing: 0.5, marginBottom: Spacing[2] },
  title:          { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.textPrimary, lineHeight: 30, marginBottom: Spacing[3] },

  // Meta
  metaRow:        { flexDirection: 'row', alignItems: 'center', gap: Spacing[2], marginBottom: Spacing[4], flexWrap: 'wrap' },
  badge:          { backgroundColor: Colors.gray100, borderRadius: Radii.full, paddingHorizontal: 10, paddingVertical: 4 },
  badgeText:      { fontSize: Typography.sizes.xs, color: Colors.textSecondary, fontWeight: Typography.weights.medium },
  metaText:       { fontSize: Typography.sizes.sm, color: Colors.textMuted },

  // Content
  contentBox:     { marginBottom: Spacing[4] },
  content:        { fontSize: Typography.sizes.base, color: Colors.textSecondary, lineHeight: 26 },

  // Attachment
  attachmentBtn:  { flexDirection: 'row', alignItems: 'center', gap: Spacing[2], backgroundColor: Colors.gray100, borderRadius: Radii.lg, padding: Spacing[4], marginBottom: Spacing[4] },
  attachmentIcon: { fontSize: 18 },
  attachmentText: { fontSize: Typography.sizes.base, color: Colors.primary, fontWeight: Typography.weights.medium },

  // Footer
  footer:              { position: 'absolute', bottom: 0, left: 0, right: 0, padding: Spacing[4], backgroundColor: Colors.white, borderTopWidth: 1, borderTopColor: Colors.border, ...Shadows.md },
  footerNav:           { flexDirection: 'row', gap: Spacing[2], marginBottom: Spacing[3] },
  navBtn:              { flex: 1, flexDirection: 'row', alignItems: 'center', gap: 4, backgroundColor: Colors.surface, borderRadius: Radii.lg, paddingHorizontal: Spacing[3], paddingVertical: Spacing[2] },
  navBtnRight:         { justifyContent: 'flex-end' },
  navBtnDisabled:      { opacity: 0.4 },
  navBtnText:          { flex: 1, fontSize: Typography.sizes.xs, color: Colors.primary, fontWeight: Typography.weights.medium },
  navBtnTextDisabled:  { color: Colors.gray400 },
  completeBtn:         { backgroundColor: Colors.primary, borderRadius: Radii.lg, paddingVertical: Spacing[4], alignItems: 'center', flexDirection: 'row', justifyContent: 'center' },
  completeBtnDisabled: { opacity: 0.6 },
  completeBtnText:     { color: Colors.white, fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold },
  completedBadge:      { backgroundColor: Colors.success, borderRadius: Radii.lg, paddingVertical: Spacing[4], alignItems: 'center', flexDirection: 'row', justifyContent: 'center' },
  completedText:       { color: Colors.white, fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold },
});
