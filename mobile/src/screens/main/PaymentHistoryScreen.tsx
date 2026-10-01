import React, { useState } from 'react';
import {
  View, Text, FlatList, TouchableOpacity, StyleSheet,
  ActivityIndicator, RefreshControl,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { useQuery } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import { formatDate, formatCurrency } from '@/utils/time';
import type { ApiResponse, RootStackParamList } from '@/types';

type Props = NativeStackScreenProps<RootStackParamList, 'PaymentHistory'>;

interface Payment {
  id: number;
  amount: number;
  status: 'paid' | 'pending' | 'failed' | 'refunded';
  payment_type: string | null;
  reference: string | null;
  paid_at: string | null;
  created_at: string;
  course: { title: string; slug: string } | null;
}

type IoniconsName = React.ComponentProps<typeof Ionicons>['name'];
const STATUS_CONFIG: Record<string, { color: string; bg: string; label: string; icon: IoniconsName }> = {
  paid:     { color: Colors.success, bg: '#dcfce7', label: 'Paid',     icon: 'checkmark-circle' },
  pending:  { color: '#d97706',      bg: '#fef3c7', label: 'Pending',  icon: 'time' },
  failed:   { color: Colors.error,   bg: '#fee2e2', label: 'Failed',   icon: 'close-circle' },
  refunded: { color: Colors.gray500, bg: Colors.gray100, label: 'Refunded', icon: 'return-down-back-outline' },
};

export default function PaymentHistoryScreen({ navigation }: Props) {
  const { data, isLoading, refetch, isRefetching } = useQuery({
    queryKey: ['payment-history'],
    queryFn:  () => apiClient.get<ApiResponse<{ data: Payment[]; meta: unknown }>>(API.PAYMENT_HISTORY)
      .then(r => r.data.data),
  });

  const payments: Payment[] = (data as any)?.data ?? (Array.isArray(data) ? data : []);
  const total = payments.filter(p => p.status === 'paid').reduce((acc, p) => acc + p.amount, 0);

  return (
    <View style={S.screen}>
      <View style={S.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={{ width: 50 }}>
          <Ionicons name="arrow-back" size={22} color={Colors.primary} />
        </TouchableOpacity>
        <Text style={S.title}>Payment History</Text>
        <View style={{ width: 50 }} />
      </View>

      {total > 0 && (
        <View style={S.summaryCard}>
          <Text style={S.summaryLabel}>Total Invested</Text>
          <Text style={S.summaryAmount}>{formatCurrency(total)}</Text>
          <Text style={S.summaryNote}>{payments.filter(p => p.status === 'paid').length} successful transactions</Text>
        </View>
      )}

      {isLoading ? (
        <View style={S.center}><ActivityIndicator color={Colors.primary} size="large" /></View>
      ) : !payments.length ? (
        <View style={S.empty}>
          <Ionicons name="card-outline" size={52} color={Colors.gray300} />
          <Text style={S.emptyTitle}>No transactions yet</Text>
          <Text style={S.emptyText}>Your payment history will appear here after you enroll in a paid course.</Text>
        </View>
      ) : (
        <FlatList
          data={payments}
          keyExtractor={(item) => String(item.id)}
          contentContainerStyle={S.list}
          refreshControl={<RefreshControl refreshing={isRefetching} onRefresh={refetch} colors={[Colors.primary]} tintColor={Colors.primary} />}
          renderItem={({ item }) => <PaymentRow payment={item} />}
        />
      )}
    </View>
  );
}

function PaymentRow({ payment }: { payment: Payment }) {
  const cfg = STATUS_CONFIG[payment.status] ?? STATUS_CONFIG.pending;

  return (
    <View style={S.card}>
      <View style={S.cardTop}>
        <View style={{ flex: 1 }}>
          <Text style={S.courseTitle} numberOfLines={2}>{payment.course?.title ?? 'Course'}</Text>
          <Text style={S.ref} numberOfLines={1}>Ref: {payment.reference ?? '—'}</Text>
        </View>
        <View>
          <Text style={S.amount}>{formatCurrency(payment.amount)}</Text>
          <View style={[S.statusBadge, { backgroundColor: cfg.bg }]}>
            <Ionicons name={cfg.icon} size={11} color={cfg.color} />
            <Text style={[S.statusText, { color: cfg.color }]}>{cfg.label}</Text>
          </View>
        </View>
      </View>
      <View style={S.cardBottom}>
        <Text style={S.dateText}>{formatDate(payment.paid_at ?? payment.created_at)}</Text>
        {payment.payment_type && (
          <Text style={S.typeText}>{payment.payment_type}</Text>
        )}
      </View>
    </View>
  );
}

const S = StyleSheet.create({
  screen:        { flex: 1, backgroundColor: Colors.surface },
  header:        { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', backgroundColor: Colors.white, paddingHorizontal: Spacing[4], paddingTop: 56, paddingBottom: Spacing[3], borderBottomWidth: 1, borderBottomColor: Colors.gray100 },
  back:          { width: 50 },
  title:         { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  summaryCard:   { margin: Spacing[4], backgroundColor: Colors.navy, borderRadius: Radii['2xl'], padding: Spacing[5], alignItems: 'center' },
  summaryLabel:  { fontSize: Typography.sizes.sm, color: 'rgba(255,255,255,0.7)' },
  summaryAmount: { fontSize: Typography.sizes['3xl'], fontWeight: Typography.weights.extrabold, color: Colors.white, marginTop: 4 },
  summaryNote:   { fontSize: Typography.sizes.xs, color: 'rgba(255,255,255,0.6)', marginTop: 6 },
  center:        { flex: 1, justifyContent: 'center', alignItems: 'center' },
  empty:         { flex: 1, justifyContent: 'center', alignItems: 'center', paddingHorizontal: Spacing[8], gap: Spacing[4] },
  emptyTitle:    { fontSize: Typography.sizes.xl, fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  emptyText:     { fontSize: Typography.sizes.sm, color: Colors.textSecondary, textAlign: 'center', marginTop: Spacing[2], lineHeight: 22 },
  list:          { padding: Spacing[4], gap: Spacing[3] },
  card:          { backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], ...Shadows.sm },
  cardTop:       { flexDirection: 'row', gap: Spacing[3], marginBottom: Spacing[3] },
  courseTitle:   { fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, lineHeight: 20, marginBottom: 4 },
  ref:           { fontSize: Typography.sizes.xs, color: Colors.textMuted },
  amount:        { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.bold, color: Colors.textPrimary, textAlign: 'right', marginBottom: 4 },
  statusBadge:   { flexDirection: 'row', alignItems: 'center', gap: 4, borderRadius: Radii.full, paddingHorizontal: 8, paddingVertical: 3, alignSelf: 'flex-end' },
  statusText:    { fontSize: 10, fontWeight: Typography.weights.semibold },
  cardBottom:    { flexDirection: 'row', justifyContent: 'space-between', borderTopWidth: 1, borderTopColor: Colors.gray100, paddingTop: Spacing[3] },
  dateText:      { fontSize: Typography.sizes.xs, color: Colors.textMuted },
  typeText:      { fontSize: Typography.sizes.xs, color: Colors.primary, fontWeight: Typography.weights.medium, textTransform: 'capitalize' },
});
