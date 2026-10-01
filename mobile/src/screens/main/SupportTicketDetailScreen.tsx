import React, { useState, useRef } from 'react';
import {
  View, Text, ScrollView, TouchableOpacity, TextInput,
  StyleSheet, ActivityIndicator, Alert, KeyboardAvoidingView, Platform,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { extractApiError } from '@/api/client';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import { relativeTime } from '@/utils/time';
import type { ApiResponse, SupportTicket, TicketReply, RootStackParamList } from '@/types';

type Props = NativeStackScreenProps<RootStackParamList, 'SupportTicketDetail'>;

type IoniconsName = React.ComponentProps<typeof Ionicons>['name'];
const STATUS_CONFIG: Record<string, { label: string; color: string; bg: string; icon: IoniconsName }> = {
  open:        { label: 'Open',        color: Colors.primary,  bg: '#eff6ff', icon: 'chatbubble-ellipses-outline' },
  in_progress: { label: 'In Progress', color: '#d97706',       bg: '#fef3c7', icon: 'time-outline' },
  resolved:    { label: 'Resolved',    color: Colors.success,  bg: '#dcfce7', icon: 'checkmark-circle-outline' },
  closed:      { label: 'Closed',      color: Colors.gray500,  bg: Colors.gray100, icon: 'lock-closed-outline' },
};

export default function SupportTicketDetailScreen({ route, navigation }: Props) {
  const { ticketId } = route.params;
  const qc = useQueryClient();
  const scrollRef = useRef<ScrollView>(null);
  const [replyText, setReplyText] = useState('');

  const { data: ticket, isLoading } = useQuery({
    queryKey: ['support-ticket', ticketId],
    queryFn: () =>
      apiClient.get<ApiResponse<SupportTicket>>(API.SUPPORT_TICKET(ticketId)).then(r => r.data.data),
  });

  const { mutate: sendReply, isPending: sending } = useMutation({
    mutationFn: () => apiClient.post(API.SUPPORT_TICKET_REPLY(ticketId), { message: replyText.trim() }),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['support-ticket', ticketId] });
      qc.invalidateQueries({ queryKey: ['support-tickets'] });
      setReplyText('');
      setTimeout(() => scrollRef.current?.scrollToEnd({ animated: true }), 300);
    },
    onError: (err) => Alert.alert('Error', extractApiError(err)),
  });

  const { mutate: closeTicket, isPending: closing } = useMutation({
    mutationFn: () => apiClient.post(API.SUPPORT_TICKET_CLOSE(ticketId)),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['support-ticket', ticketId] });
      qc.invalidateQueries({ queryKey: ['support-tickets'] });
      Alert.alert('Ticket Closed', 'Your ticket has been closed.');
    },
    onError: (err) => Alert.alert('Error', extractApiError(err)),
  });

  const handleReply = () => {
    if (!replyText.trim()) return;
    sendReply();
  };

  const handleClose = () => {
    Alert.alert('Close Ticket', 'Are you sure you want to close this ticket?', [
      { text: 'Cancel', style: 'cancel' },
      { text: 'Close', style: 'destructive', onPress: () => closeTicket() },
    ]);
  };

  if (isLoading || !ticket) {
    return <View style={S.center}><ActivityIndicator color={Colors.primary} size="large" /></View>;
  }

  const cfg = STATUS_CONFIG[ticket.status] ?? STATUS_CONFIG.open;
  const isClosed = ticket.status === 'closed' || ticket.status === 'resolved';

  return (
    <KeyboardAvoidingView style={S.screen} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <View style={S.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={S.back}>‹ Back</Text>
        </TouchableOpacity>
        <Text style={S.headerTitle} numberOfLines={1}>Ticket</Text>
        {!isClosed ? (
          <TouchableOpacity onPress={handleClose} disabled={closing}>
            <Text style={[S.closeBtn, closing && { opacity: 0.5 }]}>Close</Text>
          </TouchableOpacity>
        ) : (
          <View style={{ width: 50 }} />
        )}
      </View>

      <ScrollView
        ref={scrollRef}
        contentContainerStyle={S.scroll}
        showsVerticalScrollIndicator={false}
      >
        {/* Ticket info card */}
        <View style={S.card}>
          <View style={S.cardTop}>
            <View style={[S.statusBadge, { backgroundColor: cfg.bg }]}>
              <Ionicons name={cfg.icon} size={12} color={cfg.color} style={{ marginRight: 4 }} />
              <Text style={[S.statusText, { color: cfg.color }]}>{cfg.label}</Text>
            </View>
            {ticket.ticket_number && (
              <Text style={S.ticketNum}>#{ticket.ticket_number}</Text>
            )}
          </View>
          <Text style={S.subject}>{ticket.subject}</Text>
          <Text style={S.description}>{ticket.description}</Text>
          <View style={S.metaRow}>
            {ticket.category && <Text style={S.meta}>{ticket.category}</Text>}
            <Text style={S.meta}>{ticket.priority} priority</Text>
            <Text style={S.meta}>{relativeTime(ticket.created_at)}</Text>
          </View>
        </View>

        {/* Reply thread */}
        {(ticket.replies ?? []).length > 0 && (
          <View style={S.thread}>
            <Text style={S.threadLabel}>Replies</Text>
            {(ticket.replies!).map(reply => (
              <ReplyBubble key={reply.id} reply={reply} />
            ))}
          </View>
        )}

        <View style={{ height: Spacing[4] }} />
      </ScrollView>

      {/* Reply input */}
      {!isClosed ? (
        <View style={S.replyBar}>
          <TextInput
            style={S.replyInput}
            value={replyText}
            onChangeText={setReplyText}
            placeholder="Write a reply…"
            placeholderTextColor={Colors.gray400}
            multiline
            maxLength={5000}
          />
          <TouchableOpacity
            style={[S.sendBtn, (!replyText.trim() || sending) && S.sendBtnDisabled]}
            onPress={handleReply}
            disabled={!replyText.trim() || sending}
          >
            {sending ? (
              <ActivityIndicator size="small" color={Colors.white} />
            ) : (
              <Ionicons name="send" size={18} color={Colors.white} />
            )}
          </TouchableOpacity>
        </View>
      ) : (
        <View style={S.closedBar}>
          <Ionicons name="lock-closed-outline" size={16} color={Colors.gray400} />
          <Text style={S.closedText}>This ticket is {ticket.status}</Text>
        </View>
      )}
    </KeyboardAvoidingView>
  );
}

function ReplyBubble({ reply }: { reply: TicketReply }) {
  return (
    <View style={[S.bubble, reply.is_staff ? S.bubbleStaff : S.bubbleUser]}>
      <View style={S.bubbleHeader}>
        <Text style={[S.bubbleAuthor, reply.is_staff && S.bubbleAuthorStaff]}>
          {reply.is_staff ? `${reply.author?.full_name ?? 'Support'} (Staff)` : (reply.author?.full_name ?? 'You')}
        </Text>
        <Text style={S.bubbleTime}>{relativeTime(reply.created_at)}</Text>
      </View>
      <Text style={S.bubbleMsg}>{reply.message}</Text>
    </View>
  );
}

const S = StyleSheet.create({
  screen:       { flex: 1, backgroundColor: Colors.surface },
  center:       { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header:       { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', backgroundColor: Colors.white, paddingHorizontal: Spacing[4], paddingTop: 56, paddingBottom: Spacing[3], borderBottomWidth: 1, borderBottomColor: Colors.gray100 },
  back:         { fontSize: Typography.sizes.base, color: Colors.primary, fontWeight: Typography.weights.medium, width: 60 },
  headerTitle:  { flex: 1, textAlign: 'center', fontSize: Typography.sizes.lg, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  closeBtn:     { fontSize: Typography.sizes.sm, color: Colors.error, fontWeight: Typography.weights.medium, width: 50, textAlign: 'right' },
  scroll:       { padding: Spacing[4] },
  card:         { backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], ...Shadows.sm, marginBottom: Spacing[4] },
  cardTop:      { flexDirection: 'row', alignItems: 'center', gap: Spacing[2], marginBottom: Spacing[3] },
  statusBadge:  { flexDirection: 'row', alignItems: 'center', borderRadius: Radii.full, paddingHorizontal: 10, paddingVertical: 4 },
  statusText:   { fontSize: 11, fontWeight: Typography.weights.semibold },
  ticketNum:    { fontSize: Typography.sizes.xs, color: Colors.textMuted },
  subject:      { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.bold, color: Colors.textPrimary, marginBottom: Spacing[2] },
  description:  { fontSize: Typography.sizes.base, color: Colors.textSecondary, lineHeight: 22, marginBottom: Spacing[3] },
  metaRow:      { flexDirection: 'row', gap: Spacing[2], flexWrap: 'wrap' },
  meta:         { fontSize: Typography.sizes.xs, color: Colors.textMuted, backgroundColor: Colors.gray100, borderRadius: Radii.full, paddingHorizontal: 8, paddingVertical: 3, textTransform: 'capitalize' },
  thread:       { gap: Spacing[3] },
  threadLabel:  { fontSize: Typography.sizes.xs, fontWeight: Typography.weights.bold, color: Colors.textMuted, textTransform: 'uppercase', letterSpacing: 1, marginBottom: Spacing[2] },
  bubble:       { borderRadius: Radii.xl, padding: Spacing[3], maxWidth: '90%' },
  bubbleUser:   { backgroundColor: Colors.white, alignSelf: 'flex-end', borderBottomRightRadius: 4, ...Shadows.sm },
  bubbleStaff:  { backgroundColor: '#eff6ff', alignSelf: 'flex-start', borderBottomLeftRadius: 4, ...Shadows.sm },
  bubbleHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 4, gap: Spacing[3] },
  bubbleAuthor: { fontSize: Typography.sizes.xs, fontWeight: Typography.weights.semibold, color: Colors.textSecondary },
  bubbleAuthorStaff: { color: Colors.primary },
  bubbleTime:   { fontSize: 10, color: Colors.textMuted },
  bubbleMsg:    { fontSize: Typography.sizes.sm, color: Colors.textPrimary, lineHeight: 20 },
  replyBar:     { flexDirection: 'row', alignItems: 'flex-end', padding: Spacing[3], backgroundColor: Colors.white, borderTopWidth: 1, borderTopColor: Colors.gray100, gap: Spacing[2] },
  replyInput:   { flex: 1, borderWidth: 1.5, borderColor: Colors.gray200, borderRadius: Radii.xl, paddingHorizontal: Spacing[4], paddingVertical: Spacing[2], fontSize: Typography.sizes.base, color: Colors.textPrimary, maxHeight: 120 },
  sendBtn:      { width: 40, height: 40, borderRadius: 20, backgroundColor: Colors.primary, alignItems: 'center', justifyContent: 'center' },
  sendBtnDisabled: { opacity: 0.4 },
  closedBar:    { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: Spacing[2], padding: Spacing[4], backgroundColor: Colors.white, borderTopWidth: 1, borderTopColor: Colors.gray100 },
  closedText:   { fontSize: Typography.sizes.sm, color: Colors.gray400, textTransform: 'capitalize' },
});
