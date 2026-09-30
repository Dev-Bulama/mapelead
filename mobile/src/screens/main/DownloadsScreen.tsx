import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { Colors, Typography, Spacing } from '@/theme';

export default function DownloadsScreen() {
  return (
    <View style={S.screen}>
      <View style={S.header}>
        <Text style={S.title}>Downloads</Text>
        <Text style={S.subtitle}>Offline content will appear here</Text>
      </View>
      <View style={S.empty}>
        <Text style={S.emptyIcon}>📥</Text>
        <Text style={S.emptyTitle}>No downloads yet</Text>
        <Text style={S.emptyText}>Download lessons to watch them offline.</Text>
      </View>
    </View>
  );
}

const S = StyleSheet.create({
  screen:     { flex: 1, backgroundColor: Colors.surface },
  header:     { paddingHorizontal: Spacing[4], paddingTop: 56, paddingBottom: Spacing[4], backgroundColor: Colors.white, borderBottomWidth: 1, borderBottomColor: Colors.gray100 },
  title:      { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  subtitle:   { fontSize: Typography.sizes.sm, color: Colors.textSecondary, marginTop: 2 },
  empty:      { flex: 1, alignItems: 'center', justifyContent: 'center', paddingHorizontal: Spacing[8] },
  emptyIcon:  { fontSize: 56, marginBottom: Spacing[4] },
  emptyTitle: { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: Spacing[2] },
  emptyText:  { fontSize: Typography.sizes.sm, color: Colors.textSecondary, textAlign: 'center', lineHeight: 22 },
});
