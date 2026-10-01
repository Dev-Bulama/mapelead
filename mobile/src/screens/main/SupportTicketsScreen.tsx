import React, { useState } from 'react';
import {
  View, Text, FlatList, TouchableOpacity, StyleSheet,
  ActivityIndicator, RefreshControl, Modal, TextInput, Alert, ScrollView,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { extractApiError } from '@/api/client';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import { relativeTime } from '@/utils/time';
import type { ApiResponse, SupportTicket, RootStackParamList } from '@/types';

type Nav = NativeStackNavigationProp<RootStackParamList>;
type IoniconsName = React.ComponentProps<typeof Ionicons>['name'];

const STATUS_CONFIG: Record<string, { label: string; color: string; bg: string; icon: IoniconsName }> = {
  open:        { label: 'Open',        color: Colors.primary,  bg: '#eff6ff', icon: 'chatbubble-ellipses-outline' },
  in_progress: { label: 'In Progress', color: '#d97706',       bg: '#fef3c7', icon: 'time-outline' },
  resolved:    { label: 'Resolved',    color: Colors.success,  bg: '#dcfce7', icon: 'checkmark-circle-outline' },
  closed:      { label: 'Closed',      color: Colors.gray500,  bg: Colors.gray100, icon: 'lock-closed-outline' },
};

const PRIORITY_COLORS: Record<string, string> = {
  low: Colors.gray400, normal: Colors.primary, high: '#d97706', urgent: Colors.error,
};

const CATEGORIES = ['General', 'Technical', 'Billing', 'Course Content', 'Account', 'Other'];

export default function SupportTicketsScreen() {
  const navigation = useNavigation<Nav>();
  const qc = useQueryClient();
  const [modalVisible, setModalVisible] = useState(false);
  const [subject, setSubject] = useState('');
  const [description, setDescription] = useState('');
  const [category, setCategory] = useState('General');
  const [priority, setPriority] = useState<'low' | 'normal' | 'high' | 'urgent'>('normal');

  const { data, isLoading, refetch, isRefetching } = useQuery({
    queryKey: ['support-tickets'],
    queryFn: () => apiClient.get<ApiResponse<SupportTicket[]>>(API.SUPPORT_TICKETS).then(r => r.data.data),
  });

  const { mutate: createTicket, isPending: creating } = useMutation({
    mutationFn: () => apiClient.post(API.SUPPORT_TICKETS, {
      subject: subject.trim(),
      description: description.trim(),
      category,
      priority,
    }),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['support-tickets'] });
      setModalVisible(false);
      setSubject(''); setDescription(''); setCategory('General'); setPriority('normal');
      Alert.alert('Ticket Created', 'Your support ticket has been submitted. We\'ll respond shortly.');
    },
    onError: (err) => Alert.alert('Error', extractApiError(err)),
  });

  const handleCreate = () => {
    if (!subject.trim()) { Alert.alert('Required', 'Please enter a subject.'); return; }
    if (!description.trim()) { Alert.alert('Required', 'Please describe your issue.'); return; }
    createTicket();
  };

  if (isLoading) {
    return <View style={S.center}><ActivityIndicator color={Colors.primary} size="large" /></View>;
  }

  return (
    <View style={S.screen}>
      <View style={S.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={S.back}>‹ Back</Text>
        </TouchableOpacity>
        <Text style={S.title}>Support Tickets</Text>
        <TouchableOpacity style={S.newBtn} onPress={() => setModalVisible(true)}>
          <Ionicons name="add" size={20} color={Colors.white} />
        </TouchableOpacity>
      </View>

      {!data?.length ? (
        <View style={S.empty}>
          <Ionicons name="chatbubble-ellipses-outline" size={52} color={Colors.gray300} />
          <Text style={S.emptyTitle}>No tickets yet</Text>
          <Text style={S.emptySub}>Need help? Create a support ticket and our team will assist you.</Text>
          <TouchableOpacity style={S.emptyBtn} onPress={() => setModalVisible(true)}>
            <Text style={S.emptyBtnText}>Create Ticket</Text>
          </TouchableOpacity>
        </View>
      ) : (
        <FlatList
          data={data}
          keyExtractor={item => String(item.id)}
          contentContainerStyle={S.list}
          refreshControl={<RefreshControl refreshing={isRefetching} onRefresh={refetch} tintColor={Colors.primary} />}
          renderItem={({ item }) => (
            <TicketRow ticket={item} onPress={() => navigation.navigate('SupportTicketDetail', { ticketId: item.id })} />
          )}
        />
      )}

      {/* New Ticket Modal */}
      <Modal visible={modalVisible} animationType="slide" presentationStyle="pageSheet" onRequestClose={() => setModalVisible(false)}>
        <View style={S.modal}>
          <View style={S.modalHeader}>
            <Text style={S.modalTitle}>New Ticket</Text>
            <TouchableOpacity onPress={() => setModalVisible(false)}>
              <Ionicons name="close" size={24} color={Colors.textPrimary} />
            </TouchableOpacity>
          </View>

          <ScrollView contentContainerStyle={S.modalBody} showsVerticalScrollIndicator={false} keyboardShouldPersistTaps="handled">
            <Text style={S.fieldLabel}>Subject *</Text>
            <TextInput
              style={S.input}
              value={subject}
              onChangeText={setSubject}
              placeholder="Brief summary of your issue"
              placeholderTextColor={Colors.gray400}
              maxLength={255}
            />

            <Text style={S.fieldLabel}>Description *</Text>
            <TextInput
              style={[S.input, S.textarea]}
              value={description}
              onChangeText={setDescription}
              placeholder="Describe your issue in detail…"
              placeholderTextColor={Colors.gray400}
              multiline
              numberOfLines={5}
              textAlignVertical="top"
              maxLength={5000}
            />

            <Text style={S.fieldLabel}>Category</Text>
            <View style={S.chipRow}>
              {CATEGORIES.map(c => (
                <TouchableOpacity
                  key={c}
                  style={[S.chip, category === c && S.chipActive]}
                  onPress={() => setCategory(c)}
                >
                  <Text style={[S.chipText, category === c && S.chipTextActive]}>{c}</Text>
                </TouchableOpacity>
              ))}
            </View>

            <Text style={S.fieldLabel}>Priority</Text>
            <View style={S.chipRow}>
              {(['low', 'normal', 'high', 'urgent'] as const).map(p => (
                <TouchableOpacity
                  key={p}
                  style={[S.chip, priority === p && S.chipActive]}
                  onPress={() => setPriority(p)}
                >
                  <Text style={[S.chipText, priority === p && S.chipTextActive, { color: priority === p ? Colors.white : PRIORITY_COLORS[p] }]}>
                    {p.charAt(0).toUpperCase() + p.slice(1)}
                  </Text>
                </TouchableOpacity>
              ))}
            </View>

            <TouchableOpacity
              style={[S.submitBtn, creating && S.submitBtnDisabled]}
              onPress={handleCreate}
              disabled={creating}
            >
              {creating ? (
                <ActivityIndicator size="small" color={Colors.white} />
              ) : (
                <Text style={S.submitBtnText}>Submit Ticket</Text>
              )}
            </TouchableOpacity>
          </ScrollView>
        </View>
      </Modal>
    </View>
  );
}

function TicketRow({ ticket, onPress }: { ticket: SupportTicket; onPress: () => void }) {
  const cfg = STATUS_CONFIG[ticket.status] ?? STATUS_CONFIG.open;
  return (
    <TouchableOpacity style={S.card} onPress={onPress} activeOpacity={0.75}>
      <View style={[S.statusIcon, { backgroundColor: cfg.bg }]}>
        <Ionicons name={cfg.icon} size={20} color={cfg.color} />
      </View>
      <View style={S.cardBody}>
        <Text style={S.cardSubject} numberOfLines={2}>{ticket.subject}</Text>
        <View style={S.cardMeta}>
          <View style={[S.statusBadge, { backgroundColor: cfg.bg }]}>
            <Text style={[S.statusText, { color: cfg.color }]}>{cfg.label}</Text>
          </View>
          {ticket.ticket_number && (
            <Text style={S.ticketNum}>#{ticket.ticket_number}</Text>
          )}
          <Text style={S.time}>{relativeTime(ticket.created_at)}</Text>
        </View>
      </View>
      <Ionicons name="chevron-forward" size={16} color={Colors.gray300} />
    </TouchableOpacity>
  );
}

const S = StyleSheet.create({
  screen:       { flex: 1, backgroundColor: Colors.surface },
  center:       { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header:       { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', backgroundColor: Colors.white, paddingHorizontal: Spacing[4], paddingTop: 56, paddingBottom: Spacing[3], borderBottomWidth: 1, borderBottomColor: Colors.gray100 },
  back:         { fontSize: Typography.sizes.base, color: Colors.primary, fontWeight: Typography.weights.medium, width: 60 },
  title:        { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  newBtn:       { width: 36, height: 36, borderRadius: 18, backgroundColor: Colors.primary, alignItems: 'center', justifyContent: 'center' },
  list:         { padding: Spacing[4], gap: Spacing[3] },
  empty:        { flex: 1, alignItems: 'center', justifyContent: 'center', paddingHorizontal: Spacing[8], gap: Spacing[4] },
  emptyTitle:   { fontSize: Typography.sizes.xl, fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  emptySub:     { fontSize: Typography.sizes.sm, color: Colors.textSecondary, textAlign: 'center' },
  emptyBtn:     { backgroundColor: Colors.primary, borderRadius: Radii.xl, paddingHorizontal: Spacing[6], paddingVertical: Spacing[3] },
  emptyBtnText: { color: Colors.white, fontWeight: Typography.weights.semibold, fontSize: Typography.sizes.base },
  card:         { flexDirection: 'row', alignItems: 'center', backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], gap: Spacing[3], ...Shadows.sm },
  statusIcon:   { width: 44, height: 44, borderRadius: Radii.full, alignItems: 'center', justifyContent: 'center' },
  cardBody:     { flex: 1 },
  cardSubject:  { fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: 6, lineHeight: 20 },
  cardMeta:     { flexDirection: 'row', alignItems: 'center', gap: Spacing[2], flexWrap: 'wrap' },
  statusBadge:  { borderRadius: Radii.full, paddingHorizontal: 8, paddingVertical: 3 },
  statusText:   { fontSize: 10, fontWeight: Typography.weights.semibold },
  ticketNum:    { fontSize: Typography.sizes.xs, color: Colors.textMuted },
  time:         { fontSize: Typography.sizes.xs, color: Colors.textMuted },
  modal:        { flex: 1, backgroundColor: Colors.surface },
  modalHeader:  { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', backgroundColor: Colors.white, paddingHorizontal: Spacing[4], paddingTop: 56, paddingBottom: Spacing[4], borderBottomWidth: 1, borderBottomColor: Colors.gray100 },
  modalTitle:   { fontSize: Typography.sizes.xl, fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  modalBody:    { padding: Spacing[4], paddingBottom: Spacing[12] },
  fieldLabel:   { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.medium, color: Colors.gray700, marginBottom: Spacing[2], marginTop: Spacing[4] },
  input:        { borderWidth: 1.5, borderColor: Colors.gray200, borderRadius: Radii.lg, paddingHorizontal: Spacing[4], paddingVertical: Spacing[3], fontSize: Typography.sizes.base, color: Colors.textPrimary },
  textarea:     { minHeight: 120, textAlignVertical: 'top' },
  chipRow:      { flexDirection: 'row', flexWrap: 'wrap', gap: Spacing[2] },
  chip:         { borderWidth: 1.5, borderColor: Colors.gray200, borderRadius: Radii.full, paddingHorizontal: Spacing[3], paddingVertical: 6 },
  chipActive:   { backgroundColor: Colors.primary, borderColor: Colors.primary },
  chipText:     { fontSize: Typography.sizes.sm, color: Colors.textSecondary, fontWeight: Typography.weights.medium },
  chipTextActive: { color: Colors.white },
  submitBtn:    { backgroundColor: Colors.primary, borderRadius: Radii.xl, paddingVertical: Spacing[4], alignItems: 'center', marginTop: Spacing[6] },
  submitBtnDisabled: { opacity: 0.6 },
  submitBtnText:{ color: Colors.white, fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold },
});
