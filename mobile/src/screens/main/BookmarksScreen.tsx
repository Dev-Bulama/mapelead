import React from 'react';
import {
  View, Text, FlatList, TouchableOpacity,
  StyleSheet, ActivityIndicator, RefreshControl, Alert,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { ApiResponse, RootStackParamList } from '@/types';

type Nav = NativeStackNavigationProp<RootStackParamList>;

interface Bookmark {
  id:         number;
  lesson_id:  number;
  lesson: {
    id:     number;
    title:  string;
    type:   string;
    course: { id: number; title: string; slug: string } | null;
  } | null;
  created_at: string;
}

type IoniconsName = React.ComponentProps<typeof Ionicons>['name'];
const TYPE_ICON: Record<string, IoniconsName> = {
  video:      'play-circle-outline',
  quiz:       'help-circle-outline',
  assignment: 'clipboard-outline',
  text:       'document-text-outline',
};

export default function BookmarksScreen() {
  const navigation = useNavigation<Nav>();
  const qc = useQueryClient();

  const { data, isLoading, refetch, isRefetching } = useQuery({
    queryKey: ['bookmarks'],
    queryFn:  () => apiClient.get<ApiResponse<Bookmark[]>>(API.BOOKMARKS).then(r => r.data.data),
    staleTime: 30_000,
  });

  const { mutate: toggleBookmark } = useMutation({
    mutationFn: (lessonId: number) => apiClient.post(API.LESSON_BOOKMARK(lessonId)),
    onSuccess: () => qc.invalidateQueries({ queryKey: ['bookmarks'] }),
    onError: () => Alert.alert('Error', 'Could not remove bookmark. Please try again.'),
  });

  const handleRemove = (bookmark: Bookmark) => {
    Alert.alert(
      'Remove Bookmark',
      `Remove "${bookmark.lesson?.title}" from bookmarks?`,
      [
        { text: 'Cancel', style: 'cancel' },
        { text: 'Remove', style: 'destructive', onPress: () => toggleBookmark(bookmark.lesson_id) },
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
        <Text style={S.headerTitle}>Bookmarks</Text>
        <Text style={S.count}>{(data ?? []).length}</Text>
      </View>

      <FlatList
        data={data ?? []}
        keyExtractor={item => String(item.id)}
        contentContainerStyle={S.list}
        refreshControl={<RefreshControl refreshing={isRefetching} onRefresh={refetch} tintColor={Colors.primary} />}
        renderItem={({ item }) => (
          <TouchableOpacity
            style={S.row}
            onPress={() => {
              if (item.lesson_id && item.lesson?.course?.slug) {
                navigation.navigate('LessonView', {
                  lessonId:   item.lesson_id,
                  courseSlug: item.lesson.course.slug,
                });
              }
            }}
            activeOpacity={0.75}
          >
            <View style={S.iconWrap}>
              <Ionicons name={TYPE_ICON[item.lesson?.type ?? ''] ?? 'document-text-outline'} size={18} color={Colors.primary} />
            </View>
            <View style={S.info}>
              <Text style={S.lessonTitle} numberOfLines={2}>{item.lesson?.title ?? 'Unknown lesson'}</Text>
              {item.lesson?.course && (
                <Text style={S.courseTitle} numberOfLines={1}>{item.lesson.course.title}</Text>
              )}
            </View>
            <TouchableOpacity onPress={() => handleRemove(item)} style={S.removeBtn} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
              <Ionicons name="trash-outline" size={18} color={Colors.error} />
            </TouchableOpacity>
          </TouchableOpacity>
        )}
        ListEmptyComponent={
          <View style={S.empty}>
            <Ionicons name="bookmarks-outline" size={52} color={Colors.gray300} />
            <Text style={S.emptyTitle}>No bookmarks yet</Text>
            <Text style={S.emptySub}>Bookmark lessons while studying to find them here quickly</Text>
          </View>
        }
      />
    </View>
  );
}

const S = StyleSheet.create({
  screen:      { flex: 1, backgroundColor: Colors.surface },
  center:      { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header:      { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingHorizontal: Spacing[4], paddingTop: Spacing[12], paddingBottom: Spacing[3], backgroundColor: Colors.white, borderBottomWidth: 1, borderBottomColor: Colors.gray100 },
  backBtn:     { width: 40, alignItems: 'flex-start' },
  headerTitle: { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  count:       { fontSize: Typography.sizes.sm, color: Colors.textMuted, width: 60, textAlign: 'right' },
  list:        { padding: Spacing[4] },
  row:         { flexDirection: 'row', alignItems: 'center', backgroundColor: Colors.white, borderRadius: Radii.xl, marginBottom: Spacing[3], padding: Spacing[4], gap: Spacing[3], ...Shadows.sm },
  iconWrap:    { width: 40, height: 40, borderRadius: 20, backgroundColor: 'rgba(30,58,219,0.08)', alignItems: 'center', justifyContent: 'center' },
  info:        { flex: 1 },
  lessonTitle: { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  courseTitle: { fontSize: Typography.sizes.xs, color: Colors.textMuted, marginTop: 3 },
  removeBtn:   { padding: Spacing[1] },
  empty:       { alignItems: 'center', paddingTop: Spacing[12], gap: Spacing[3] },
  emptyTitle:  { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  emptySub:    { fontSize: Typography.sizes.sm, color: Colors.textMuted, textAlign: 'center', paddingHorizontal: Spacing[8] },
});
