import React from 'react';
import { Modal, View, Text, TouchableOpacity, StyleSheet } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';

type AlertType = 'error' | 'success' | 'warning' | 'info';

interface AlertModalProps {
  visible:   boolean;
  type?:     AlertType;
  title?:    string;
  message:   string;
  onClose:   () => void;
  buttonLabel?: string;
}

const ICON_MAP: Record<AlertType, { name: React.ComponentProps<typeof Ionicons>['name']; color: string; bg: string }> = {
  error:   { name: 'close-circle',      color: Colors.error,   bg: '#fef2f2' },
  success: { name: 'checkmark-circle',  color: '#10B981',      bg: '#f0fdf4' },
  warning: { name: 'warning',           color: Colors.warning, bg: '#fffbeb' },
  info:    { name: 'information-circle', color: Colors.primary, bg: '#eff6ff' },
};

export default function AlertModal({
  visible, type = 'error', title, message, onClose, buttonLabel = 'OK',
}: AlertModalProps) {
  const cfg = ICON_MAP[type];

  return (
    <Modal transparent animationType="fade" visible={visible} onRequestClose={onClose}>
      <View style={s.overlay}>
        <View style={s.card}>
          <View style={[s.iconWrap, { backgroundColor: cfg.bg }]}>
            <Ionicons name={cfg.name} size={40} color={cfg.color} />
          </View>
          {title ? <Text style={s.title}>{title}</Text> : null}
          <Text style={s.message}>{message}</Text>
          <TouchableOpacity
            style={[s.btn, { backgroundColor: cfg.color }]}
            onPress={onClose}
            activeOpacity={0.8}
          >
            <Text style={s.btnText}>{buttonLabel}</Text>
          </TouchableOpacity>
        </View>
      </View>
    </Modal>
  );
}

const s = StyleSheet.create({
  overlay:  { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'center', alignItems: 'center', padding: Spacing[6] },
  card:     { backgroundColor: Colors.white, borderRadius: Radii['2xl'], padding: Spacing[6], alignItems: 'center', width: '100%', maxWidth: 340, ...Shadows.lg },
  iconWrap: { width: 72, height: 72, borderRadius: 36, alignItems: 'center', justifyContent: 'center', marginBottom: Spacing[4] },
  title:    { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.bold, color: Colors.textPrimary, textAlign: 'center', marginBottom: Spacing[2] },
  message:  { fontSize: Typography.sizes.sm, color: Colors.textSecondary, textAlign: 'center', lineHeight: 20, marginBottom: Spacing[5] },
  btn:      { borderRadius: Radii.xl, paddingVertical: 12, paddingHorizontal: Spacing[8], alignItems: 'center', minWidth: 120 },
  btnText:  { color: Colors.white, fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold },
});
