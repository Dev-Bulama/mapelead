import React, { useEffect, useRef, useState, useCallback } from 'react';
import { Animated, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';

export type ToastType = 'success' | 'error' | 'info';

interface ToastProps {
  message:   string;
  type?:     ToastType;
  onDismiss: () => void;
}

const CONFIG: Record<ToastType, { bg: string; icon: React.ComponentProps<typeof Ionicons>['name'] }> = {
  success: { bg: '#10B981', icon: 'checkmark-circle' },
  error:   { bg: Colors.error, icon: 'close-circle' },
  info:    { bg: Colors.primary, icon: 'information-circle' },
};

export function Toast({ message, type = 'info', onDismiss }: ToastProps) {
  const opacity = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    Animated.sequence([
      Animated.timing(opacity, { toValue: 1, duration: 250, useNativeDriver: true }),
      Animated.delay(2800),
      Animated.timing(opacity, { toValue: 0, duration: 300, useNativeDriver: true }),
    ]).start(() => onDismiss());
  }, []);

  const { bg, icon } = CONFIG[type];

  return (
    <Animated.View style={[S.toast, { backgroundColor: bg, opacity }]}>
      <Ionicons name={icon} size={20} color="white" />
      <Text style={S.msg} numberOfLines={2}>{message}</Text>
      <TouchableOpacity onPress={onDismiss} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
        <Ionicons name="close" size={18} color="white" />
      </TouchableOpacity>
    </Animated.View>
  );
}

export function useToast() {
  const [toast, setToast] = useState<{ message: string; type: ToastType } | null>(null);

  const show = useCallback((message: string, type: ToastType = 'info') => {
    setToast({ message, type });
  }, []);

  const dismiss = useCallback(() => setToast(null), []);

  return { toast, show, dismiss };
}

const S = StyleSheet.create({
  toast: {
    position:       'absolute',
    bottom:         88,
    left:           Spacing[4],
    right:          Spacing[4],
    flexDirection:  'row',
    alignItems:     'center',
    borderRadius:   Radii.xl,
    padding:        Spacing[4],
    gap:            Spacing[2],
    zIndex:         9999,
    ...Shadows.lg,
  },
  icon:  { color: 'white', fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold, width: 20, textAlign: 'center' },
  msg:   { flex: 1, color: 'white', fontSize: Typography.sizes.sm },
  close: { color: 'white', fontSize: 22, lineHeight: 24 },
});
