import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  KeyboardAvoidingView, Platform, ScrollView,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { apiClient, extractApiError } from '@/api/client';
import { API } from '@/api/endpoints';
import AlertModal from '@/components/common/AlertModal';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { AuthStackParamList } from '@/types';

type Props = NativeStackScreenProps<AuthStackParamList, 'ForgotPassword'>;

export default function ForgotPasswordScreen({ navigation }: Props) {
  const [email,   setEmail]   = useState('');
  const [loading, setLoading] = useState(false);
  const [sent,    setSent]    = useState(false);
  const [alertMsg, setAlertMsg] = useState<string | null>(null);

  const handleSubmit = async () => {
    if (!email.trim()) { setAlertMsg('Please enter your email address'); return; }
    setLoading(true);
    try {
      await apiClient.post(API.AUTH_FORGOT_PASSWORD, { email: email.trim().toLowerCase() });
      setSent(true);
    } catch (err) {
      setAlertMsg(extractApiError(err));
    } finally {
      setLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView style={styles.flex} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">
        <View style={styles.header}>
          <Text style={styles.logo}>MAPELEAD</Text>
          <Text style={styles.title}>Reset password</Text>
          <Text style={styles.subtitle}>We'll send a reset link to your email</Text>
        </View>

        <View style={styles.card}>
          {sent ? (
            <View style={styles.successBox}>
              <Ionicons name="checkmark-circle" size={48} color="#10B981" style={{ marginBottom: Spacing[3] }} />
              <Text style={styles.successTitle}>Check your inbox</Text>
              <Text style={styles.successText}>
                A password reset link has been sent to {email}
              </Text>
              <TouchableOpacity style={styles.backBtn} onPress={() => navigation.navigate('Login')}>
                <Text style={styles.backBtnText}>Back to Sign In</Text>
              </TouchableOpacity>
            </View>
          ) : (
            <>
              <View style={styles.field}>
                <Text style={styles.label}>Email Address</Text>
                <View style={styles.inputWrap}>
                  <Ionicons name="mail-outline" size={16} color={Colors.gray400} style={{ marginRight: Spacing[2] }} />
                  <TextInput
                    style={styles.input}
                    value={email}
                    onChangeText={setEmail}
                    placeholder="you@example.com"
                    placeholderTextColor={Colors.gray400}
                    keyboardType="email-address"
                    autoCapitalize="none"
                    autoCorrect={false}
                  />
                </View>
              </View>
              <TouchableOpacity
                style={[styles.button, loading && styles.buttonDisabled]}
                onPress={handleSubmit}
                disabled={loading}
              >
                <Text style={styles.buttonText}>{loading ? 'Sending…' : 'Send Reset Link'}</Text>
              </TouchableOpacity>
            </>
          )}
        </View>

        {!sent && (
          <TouchableOpacity style={styles.backRow} onPress={() => navigation.navigate('Login')}>
            <Ionicons name="arrow-back" size={14} color={Colors.white} style={{ marginRight: 4 }} />
            <Text style={styles.backLink}>Back to Sign In</Text>
          </TouchableOpacity>
        )}
      </ScrollView>

      <AlertModal
        visible={!!alertMsg}
        type="error"
        title="Oops"
        message={alertMsg ?? ''}
        onClose={() => setAlertMsg(null)}
      />
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  flex:          { flex: 1, backgroundColor: Colors.primary },
  scroll:        { flexGrow: 1, padding: Spacing[6] },
  header:        { alignItems: 'center', paddingVertical: Spacing[10] },
  logo:          { fontSize: Typography.sizes['3xl'], fontWeight: Typography.weights.extrabold, color: Colors.white, letterSpacing: -1 },
  title:         { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.white, marginTop: Spacing[4] },
  subtitle:      { fontSize: Typography.sizes.sm, color: 'rgba(255,255,255,0.8)', marginTop: Spacing[1] },
  card:          { backgroundColor: Colors.white, borderRadius: Radii['2xl'], padding: Spacing[6], ...Shadows.lg },
  field:         { marginBottom: Spacing[4] },
  label:         { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.medium, color: Colors.gray700, marginBottom: Spacing[2] },
  inputWrap:     { flexDirection: 'row', alignItems: 'center', borderWidth: 1, borderColor: Colors.gray300, borderRadius: Radii.lg, paddingHorizontal: Spacing[4], backgroundColor: Colors.gray50 },
  input:         { flex: 1, paddingVertical: Spacing[3], fontSize: Typography.sizes.base, color: Colors.textPrimary },
  button:        { backgroundColor: Colors.primary, borderRadius: Radii.lg, paddingVertical: Spacing[4], alignItems: 'center', marginTop: Spacing[2] },
  buttonDisabled:{ opacity: 0.6 },
  buttonText:    { color: Colors.white, fontSize: Typography.sizes.md, fontWeight: Typography.weights.semibold },
  successBox:    { alignItems: 'center', paddingVertical: Spacing[4] },
  successTitle:  { fontSize: Typography.sizes.xl, fontWeight: Typography.weights.bold, color: Colors.textPrimary, marginBottom: Spacing[2] },
  successText:   { color: Colors.textSecondary, fontSize: Typography.sizes.sm, textAlign: 'center', lineHeight: 20 },
  backBtn:       { marginTop: Spacing[6], backgroundColor: Colors.primary, borderRadius: Radii.lg, paddingVertical: 12, paddingHorizontal: Spacing[8] },
  backBtnText:   { color: Colors.white, fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold },
  backRow:       { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', marginTop: Spacing[6] },
  backLink:      { color: Colors.white, fontSize: Typography.sizes.sm, fontWeight: Typography.weights.medium },
});
