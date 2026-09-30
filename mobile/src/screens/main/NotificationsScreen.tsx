import React from 'react';
import {
  View, Text, FlatList, TouchableOpacity, StyleSheet, ActivityIndicator,
} from 'react-native';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { Colors, Typography, Spacing, Radii } from '@/theme';
import type { ApiResponse, Notification } from '@/types';

export default function NotificationsScreen() {
  const qc = useQueryClient();

  const { data, isLoading } = useQuery({
    queryKey: ['notifications'],
    queryFn: () =>
      apiClient.get<ApiResponse<{ notifications: Notification[]; meta: Record<string, unknown> }>>(
        API.NOTIFICATIONS
      ).then((r) => r.data.data.notifications),
  });

  const markAll = useMutation({
    mutationFn: () => apiClient.post(API.NOTIFICATIONS_READ_ALL),
    onSuccess:  () => qc.invalidateQueries({ queryKey: ['notifications'] }),
  });

  return (
    <View style={styles.screen}>
      <View style={styles.header}>
        <Text style={styles.title}>Notifications</Text>
        <TouchableOpacity onPress={() => markAll.mutate()}>
          <Text style={styles.markAll}>Mark all read</Text>
        </TouchableOpacity>
      </View>

      {isLoading ? (
        <ActivityIndicator color={Colors.primary} style={{ marginTop: Spacing[8] }} />
      ) : (
        <FlatList
          data={data ?? []}
          keyExtractor={(item) => String(item.id)}
          contentContainerStyle={styles.list}
          renderItem={({ item }) => <NotificationRow item={item} />}
          ListEmptyComponent={
            <Text style={styles.empty}>No notifications yet</Text>
          }
        />
      )}
    </View>
  );
}

function NotificationRow({ item }: { item: Notification }) {
  return (
    <View style={[styles.row, !item.is_read && styles.rowUnread]}>
      {!item.is_read && <View style={styles.dot} />}
      <View style={styles.rowContent}>
        <Text style={styles.rowTitle}>{item.title}</Text>
        <Text style={styles.rowMessage} numberOfLines={2}>{item.message}</Text>
        <Text style={styles.rowTime}>
          {new Date(item.created_at).toLocaleDateString()}
        </Text>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  screen:     { flex: 1, backgroundColor: Colors.surface },
  header:     { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', backgroundColor: Colors.white, padding: Spacing[4], paddingTop: Spacing[10], borderBottomWidth: 1, borderBottomColor: Colors.border },
  title:      { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  markAll:    { fontSize: Typography.sizes.sm, color: Colors.primary, fontWeight: Typography.weights.medium },
  list:       { padding: Spacing[4] },
  empty:      { textAlign: 'center', color: Colors.textMuted, marginTop: Spacing[8] },
  row:        { flexDirection: 'row', alignItems: 'flex-start', backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], marginBottom: Spacing[3] },
  rowUnread:  { borderLeftWidth: 3, borderLeftColor: Colors.primary },
  dot:        { width: 8, height: 8, borderRadius: Radii.full, backgroundColor: Colors.primary, marginRight: Spacing[3], marginTop: 4 },
  rowContent: { flex: 1 },
  rowTitle:   { fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: Spacing[1] },
  rowMessage: { fontSize: Typography.sizes.sm, color: Colors.textSecondary },
  rowTime:    { fontSize: Typography.sizes.xs, color: Colors.textMuted, marginTop: Spacing[2] },
});
