import React, { useCallback } from 'react';
import {
  View, Text, FlatList, TouchableOpacity, StyleSheet,
  ActivityIndicator, RefreshControl, Linking,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import { relativeTime } from '@/utils/time';
import type { ApiResponse, Notification, RootStackParamList } from '@/types';

type Nav = NativeStackNavigationProp<RootStackParamList>;

const TYPE_ICONS: Record<string, string> = {
  course:       '📚',
  assignment:   '📋',
  quiz:         '🧠',
  payment:      '💳',
  certificate:  '🏆',
  grade:        '⭐',
  announcement: '📢',
  default:      '🔔',
};

export default function NotificationsScreen() {
  const navigation = useNavigation<Nav>();
  const qc         = useQueryClient();

  const { data, isLoading, refetch, isRefetching } = useQuery({
    queryKey: ['notifications'],
    queryFn:  () => apiClient.get<ApiResponse<Notification[]>>(API.NOTIFICATIONS).then(r => r.data.data),
  });

  const { mutate: markAll } = useMutation({
    mutationFn: () => apiClient.post(API.NOTIFICATIONS_READ_ALL),
    onSuccess:  () => qc.invalidateQueries({ queryKey: ['notifications'] }),
  });

  const { mutate: markOne } = useMutation({
    mutationFn: (id: number) => apiClient.post(API.NOTIFICATION_READ(id)),
    onSuccess:  () => qc.invalidateQueries({ queryKey: ['notifications'] }),
  });

  const handleTap = useCallback((notif: Notification) => {
    if (!notif.is_read) markOne(notif.id);

    // Navigate based on URL / type
    if (notif.url) {
      const courseMatch = notif.url.match(/\/courses\/([\w-]+)/);
      const lessonMatch = notif.url.match(/\/lessons\/(\d+)/);
      if (courseMatch) {
        navigation.navigate('CourseDetail', { slug: courseMatch[1] });
        return;
      }
      if (lessonMatch) {
        navigation.navigate('LessonView', { lessonId: Number(lessonMatch[1]), courseSlug: '' });
        return;
      }
      if (notif.url.startsWith('http')) {
        Linking.openURL(notif.url).catch(() => {});
        return;
      }
    }
  }, [markOne, navigation]);

  const unreadCount = (data ?? []).filter(n => !n.is_read).length;

  if (isLoading) {
    return <View style={S.center}><ActivityIndicator color={Colors.primary} size="large" /></View>;
  }

  return (
    <View style={S.screen}>
      {/* Header */}
      <View style={S.header}>
        <View>
          <Text style={S.title}>Notifications</Text>
          {unreadCount > 0 && (
            <Text style={S.unreadLabel}>{unreadCount} unread</Text>
          )}
        </View>
        {unreadCount > 0 && (
          <TouchableOpacity style={S.markAllBtn} onPress={() => markAll()}>
            <Text style={S.markAllText}>Mark all read</Text>
          </TouchableOpacity>
        )}
      </View>

      {!data?.length ? (
        <View style={S.empty}>
          <Text style={S.emptyIcon}>🔔</Text>
          <Text style={S.emptyTitle}>All caught up!</Text>
          <Text style={S.emptyText}>No notifications yet. Keep learning!</Text>
        </View>
      ) : (
        <FlatList
          data={data}
          keyExtractor={(item) => String(item.id)}
          contentContainerStyle={S.list}
          refreshControl={
            <RefreshControl
              refreshing={isRefetching}
              onRefresh={refetch}
              colors={[Colors.primary]}
              tintColor={Colors.primary}
            />
          }
          renderItem={({ item }) => (
            <NotifRow notif={item} onTap={() => handleTap(item)} />
          )}
        />
      )}
    </View>
  );
}

function NotifRow({ notif, onTap }: { notif: Notification; onTap: () => void }) {
  const icon = TYPE_ICONS[notif.type] ?? TYPE_ICONS.default;

  return (
    <TouchableOpacity
      style={[S.card, !notif.is_read && S.cardUnread]}
      onPress={onTap}
      activeOpacity={0.7}
    >
      {!notif.is_read && <View style={S.unreadDot} />}
      <View style={S.iconWrap}>
        <Text style={{ fontSize: 22 }}>{icon}</Text>
      </View>
      <View style={S.content}>
        <Text style={[S.notifTitle, !notif.is_read && S.notifTitleUnread]} numberOfLines={1}>
          {notif.title}
        </Text>
        <Text style={S.message} numberOfLines={2}>{notif.message}</Text>
        <Text style={S.time}>{relativeTime(notif.created_at)}</Text>
      </View>
      {notif.url ? <Text style={S.chevron}>›</Text> : null}
    </TouchableOpacity>
  );
}

const S = StyleSheet.create({
  screen:         { flex: 1, backgroundColor: Colors.surface },
  center:         { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header:         { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-end', backgroundColor: Colors.white, paddingHorizontal: Spacing[4], paddingTop: 56, paddingBottom: Spacing[3], borderBottomWidth: 1, borderBottomColor: Colors.gray100 },
  title:          { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  unreadLabel:    { fontSize: Typography.sizes.xs, color: Colors.primary, fontWeight: Typography.weights.medium, marginTop: 2 },
  markAllBtn:     { backgroundColor: Colors.gray100, borderRadius: Radii.full, paddingHorizontal: Spacing[3], paddingVertical: 6 },
  markAllText:    { fontSize: Typography.sizes.xs, color: Colors.primary, fontWeight: Typography.weights.semibold },
  list:           { padding: Spacing[4], gap: Spacing[2] },
  empty:          { flex: 1, alignItems: 'center', justifyContent: 'center', paddingHorizontal: Spacing[8] },
  emptyIcon:      { fontSize: 52, marginBottom: Spacing[4] },
  emptyTitle:     { fontSize: Typography.sizes.xl, fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  emptyText:      { fontSize: Typography.sizes.sm, color: Colors.textSecondary, textAlign: 'center', marginTop: Spacing[2] },
  card:           { flexDirection: 'row', alignItems: 'flex-start', backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], ...Shadows.sm, position: 'relative' },
  cardUnread:     { backgroundColor: '#f0f4ff', borderLeftWidth: 3, borderLeftColor: Colors.primary },
  unreadDot:      { position: 'absolute', top: 14, left: -8, width: 8, height: 8, borderRadius: 4, backgroundColor: Colors.primary },
  iconWrap:       { width: 40, height: 40, borderRadius: 12, backgroundColor: Colors.gray100, alignItems: 'center', justifyContent: 'center', marginRight: Spacing[3] },
  content:        { flex: 1 },
  notifTitle:     { fontSize: Typography.sizes.base, color: Colors.textSecondary, fontWeight: Typography.weights.medium, marginBottom: 3 },
  notifTitleUnread:{ color: Colors.textPrimary, fontWeight: Typography.weights.semibold },
  message:        { fontSize: Typography.sizes.sm, color: Colors.textSecondary, lineHeight: 18, marginBottom: 4 },
  time:           { fontSize: Typography.sizes.xs, color: Colors.textMuted },
  chevron:        { fontSize: 20, color: Colors.gray300, marginLeft: Spacing[2], alignSelf: 'center' },
});
