import React from 'react';
import {
  View, Text, FlatList, TouchableOpacity, StyleSheet, ActivityIndicator,
} from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { ApiResponse, Certificate } from '@/types';

export default function CertificatesScreen() {
  const { data, isLoading } = useQuery({
    queryKey: ['certificates'],
    queryFn: () =>
      apiClient.get<ApiResponse<Certificate[]>>(API.CERTIFICATES).then((r) => r.data.data),
  });

  if (isLoading) {
    return <View style={styles.center}><ActivityIndicator color={Colors.primary} size="large" /></View>;
  }

  return (
    <View style={styles.screen}>
      <View style={styles.header}>
        <Text style={styles.title}>Certificates</Text>
        <Text style={styles.subtitle}>{data?.length ?? 0} earned</Text>
      </View>

      {!data?.length ? (
        <View style={styles.empty}>
          <Text style={styles.emptyIcon}>🏆</Text>
          <Text style={styles.emptyTitle}>No certificates yet</Text>
          <Text style={styles.emptySubtitle}>Complete a course to earn your certificate</Text>
        </View>
      ) : (
        <FlatList
          data={data}
          keyExtractor={(item) => String(item.id)}
          contentContainerStyle={styles.list}
          renderItem={({ item }) => <CertCard cert={item} />}
        />
      )}
    </View>
  );
}

function CertCard({ cert }: { cert: Certificate }) {
  return (
    <View style={styles.card}>
      <View style={styles.badge}>
        <Text style={styles.badgeText}>🏆</Text>
      </View>
      <View style={styles.info}>
        <Text style={styles.courseTitle} numberOfLines={2}>{cert.course?.title}</Text>
        <Text style={styles.certNumber}>#{cert.certificate_number}</Text>
        <Text style={styles.issuedAt}>
          Issued {cert.issued_at ? new Date(cert.issued_at).toLocaleDateString() : '—'}
        </Text>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  screen:      { flex: 1, backgroundColor: Colors.surface },
  header:      { backgroundColor: Colors.white, padding: Spacing[4], paddingTop: Spacing[10], borderBottomWidth: 1, borderBottomColor: Colors.border },
  title:       { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  subtitle:    { fontSize: Typography.sizes.sm, color: Colors.textSecondary, marginTop: Spacing[1] },
  center:      { flex: 1, justifyContent: 'center', alignItems: 'center' },
  empty:       { flex: 1, justifyContent: 'center', alignItems: 'center', padding: Spacing[8] },
  emptyIcon:   { fontSize: 48, marginBottom: Spacing[4] },
  emptyTitle:  { fontSize: Typography.sizes.xl, fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  emptySubtitle:{ fontSize: Typography.sizes.base, color: Colors.textSecondary, textAlign: 'center', marginTop: Spacing[2] },
  list:        { padding: Spacing[4] },
  card:        { flexDirection: 'row', backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], marginBottom: Spacing[4], alignItems: 'center', ...Shadows.sm },
  badge:       { width: 52, height: 52, backgroundColor: Colors.primary, borderRadius: Radii.full, justifyContent: 'center', alignItems: 'center', marginRight: Spacing[4] },
  badgeText:   { fontSize: 24 },
  info:        { flex: 1 },
  courseTitle: { fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: Spacing[1] },
  certNumber:  { fontSize: Typography.sizes.sm, color: Colors.primary, fontWeight: Typography.weights.medium },
  issuedAt:    { fontSize: Typography.sizes.xs, color: Colors.textMuted, marginTop: 2 },
});
