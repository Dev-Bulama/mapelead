import React, { useRef, useState } from 'react';
import {
  View, Text, TouchableOpacity, StyleSheet, ActivityIndicator, Alert,
} from 'react-native';
import { WebView } from 'react-native-webview';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { enrollmentsApi } from '@/api/enrollments';
import { extractApiError } from '@/api/client';
import { Colors, Typography, Spacing, Radii } from '@/theme';
import type { RootStackParamList } from '@/types';

type Props = NativeStackScreenProps<RootStackParamList, 'PaymentWebView'>;

// Paystack redirects to this URL pattern on success / cancel
const SUCCESS_PATTERN = /paystack\.com\/checkout\/confirm/i;
const CANCEL_PATTERN  = /paystack\.com\/checkout\/cancel/i;

export default function PaymentWebViewScreen({ route, navigation }: Props) {
  const { url, reference } = route.params;
  const qc        = useQueryClient();
  const webRef    = useRef<WebView>(null);
  const [loading, setLoading] = useState(true);
  const [verified, setVerified] = useState(false);

  const { mutate: verify, isPending: verifying } = useMutation({
    mutationFn: () => enrollmentsApi.verifyPayment(reference),
    onSuccess: (res) => {
      qc.invalidateQueries({ queryKey: ['enrollments'] });
      setVerified(true);
      Alert.alert(
        'Payment Successful! 🎉',
        'You are now enrolled. Start learning right away.',
        [{ text: 'Start Learning', onPress: () => navigation.pop(2) }]
      );
    },
    onError: (err) => {
      Alert.alert(
        'Verification failed',
        extractApiError(err) + '\n\nPlease contact support if money was deducted.',
        [{ text: 'Go back', onPress: () => navigation.goBack() }]
      );
    },
  });

  const handleNavigationChange = (event: { url: string }) => {
    const { url: navUrl } = event;

    if (SUCCESS_PATTERN.test(navUrl) && !verified) {
      setLoading(true);
      verify();
      return;
    }

    if (CANCEL_PATTERN.test(navUrl)) {
      Alert.alert('Payment cancelled', 'Your payment was not completed.', [
        { text: 'Try again', onPress: () => webRef.current?.reload() },
        { text: 'Go back', style: 'cancel', onPress: () => navigation.goBack() },
      ]);
    }
  };

  return (
    <View style={s.flex}>
      {/* Header */}
      <View style={s.header}>
        <TouchableOpacity onPress={() => {
          Alert.alert('Cancel payment?', 'Your payment will not be completed.', [
            { text: 'Continue paying', style: 'cancel' },
            { text: 'Cancel', style: 'destructive', onPress: () => navigation.goBack() },
          ]);
        }}>
          <Text style={s.cancelBtn}>Cancel</Text>
        </TouchableOpacity>
        <Text style={s.headerTitle}>Secure Payment</Text>
        <View style={s.lockIcon}><Text>🔒</Text></View>
      </View>

      {/* Loading overlay */}
      {(loading || verifying) && (
        <View style={s.loadingOverlay}>
          <ActivityIndicator color={Colors.primary} size="large" />
          <Text style={s.loadingText}>{verifying ? 'Verifying payment…' : 'Loading…'}</Text>
        </View>
      )}

      <WebView
        ref={webRef}
        source={{ uri: url }}
        onLoadStart={() => setLoading(true)}
        onLoadEnd={() => setLoading(false)}
        onNavigationStateChange={handleNavigationChange}
        javaScriptEnabled
        domStorageEnabled
        startInLoadingState={false}
        style={s.webview}
      />
    </View>
  );
}

const s = StyleSheet.create({
  flex:           { flex: 1, backgroundColor: Colors.white },
  header:         { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingTop: Spacing[12], paddingHorizontal: Spacing[4], paddingBottom: Spacing[3], backgroundColor: Colors.white, borderBottomWidth: 1, borderBottomColor: Colors.border },
  cancelBtn:      { fontSize: Typography.sizes.base, color: Colors.error, fontWeight: Typography.weights.medium, width: 60 },
  headerTitle:    { fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  lockIcon:       { width: 60, alignItems: 'flex-end' },
  loadingOverlay: { position: 'absolute', top: 80, left: 0, right: 0, bottom: 0, backgroundColor: 'rgba(255,255,255,0.9)', justifyContent: 'center', alignItems: 'center', zIndex: 10 },
  loadingText:    { marginTop: Spacing[3], fontSize: Typography.sizes.base, color: Colors.textSecondary },
  webview:        { flex: 1 },
});
