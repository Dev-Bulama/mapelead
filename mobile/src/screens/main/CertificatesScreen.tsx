import React from 'react';
import {
  View, Text, FlatList, TouchableOpacity, StyleSheet,
  ActivityIndicator, Share, Linking, Alert,
} from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
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
    return <View style={S.center}><ActivityIndicator color={Colors.primary} size="large" /></View>;
  }

  return (
    <View style={S.screen}>
      <View style={S.header}>
        <Text style={S.title}>Certificates</Text>
        <Text style={S.subtitle}>{data?.length ?? 0} earned</Text>
      </View>

      {!data?.length ? (
        <View style={S.empty}>
          <Ionicons name="ribbon-outline" size={56} color={Colors.gray300} />
          <Text style={S.emptyTitle}>No certificates yet</Text>
          <Text style={S.emptySubtitle}>Complete a course to earn your first certificate</Text>
        </View>
      ) : (
        <FlatList
          data={data}
          keyExtractor={(item) => String(item.id)}
          contentContainerStyle={S.list}
          renderItem={({ item }) => <CertCard cert={item} />}
        />
      )}
    </View>
  );
}

function CertCard({ cert }: { cert: Certificate }) {
  const handleShare = async () => {
    const url = cert.verification_url ?? cert.file_url;
    if (!url) {
      Alert.alert('No link available', 'This certificate does not have a shareable link yet.');
      return;
    }
    try {
      await Share.share({
        message: `I earned a certificate in "${cert.course?.title}" from MAPELEAD LIMITED! Verify it here: ${url}`,
        url,
      });
    } catch {}
  };

  const handleView = async () => {
    const url = cert.file_url ?? cert.verification_url;
    if (!url) {
      Alert.alert('No file available', 'This certificate PDF is not yet available.');
      return;
    }
    Linking.openURL(url).catch(() => Alert.alert('Error', 'Could not open certificate.'));
  };

  return (
    <View style={S.card}>
      {/* Certificate badge */}
      <View style={S.badge}>
        <Ionicons name="ribbon" size={26} color={Colors.primary} />
      </View>

      {/* Info */}
      <View style={S.info}>
        <Text style={S.courseTitle} numberOfLines={2}>{cert.course?.title}</Text>
        <Text style={S.certNumber}>#{cert.certificate_number}</Text>
        <Text style={S.issuedAt}>
          Issued {cert.issued_at ? new Date(cert.issued_at).toLocaleDateString('en-NG', { day: 'numeric', month: 'short', year: 'numeric' }) : '—'}
        </Text>
      </View>

      {/* Actions */}
      <View style={S.actions}>
        <TouchableOpacity style={S.actionBtn} onPress={handleView}>
          <Ionicons name="document-text-outline" size={16} color={Colors.white} />
        </TouchableOpacity>
        <TouchableOpacity style={[S.actionBtn, S.shareBtn]} onPress={handleShare}>
          <Ionicons name="share-outline" size={16} color={Colors.white} />
        </TouchableOpacity>
      </View>
    </View>
  );
}

const S = StyleSheet.create({
  screen:       { flex: 1, backgroundColor: Colors.surface },
  header:       { backgroundColor: Colors.white, paddingHorizontal: Spacing[4], paddingTop: 56, paddingBottom: Spacing[4], borderBottomWidth: 1, borderBottomColor: Colors.gray100 },
  title:        { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  subtitle:     { fontSize: Typography.sizes.sm, color: Colors.textSecondary, marginTop: 2 },
  center:       { flex: 1, justifyContent: 'center', alignItems: 'center' },
  empty:        { flex: 1, justifyContent: 'center', alignItems: 'center', paddingHorizontal: Spacing[8], gap: Spacing[4] },
  emptyTitle:   { fontSize: Typography.sizes.xl, fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  emptySubtitle:{ fontSize: Typography.sizes.sm, color: Colors.textSecondary, textAlign: 'center', marginTop: Spacing[2] },
  list:         { padding: Spacing[4], gap: Spacing[3] },
  card:         { flexDirection: 'row', backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], alignItems: 'center', ...Shadows.sm },
  badge:        { width: 52, height: 52, backgroundColor: '#fef9c3', borderRadius: Radii.full, justifyContent: 'center', alignItems: 'center', marginRight: Spacing[3] },
  info:         { flex: 1 },
  courseTitle:  { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: 2, lineHeight: 18 },
  certNumber:   { fontSize: Typography.sizes.xs, color: Colors.primary, fontWeight: Typography.weights.medium },
  issuedAt:     { fontSize: 10, color: Colors.textMuted, marginTop: 2 },
  actions:      { flexDirection: 'column', gap: 6, marginLeft: Spacing[2] },
  actionBtn:    { width: 34, height: 34, borderRadius: 10, backgroundColor: Colors.gray100, alignItems: 'center', justifyContent: 'center' },
  shareBtn:     { backgroundColor: Colors.primary },
});
