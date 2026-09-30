import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  KeyboardAvoidingView, Platform, ScrollView,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { apiClient } from '@/api/client';
import { extractApiError } from '@/api/client';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { AuthStackParamList } from '@/types';

type Props = NativeStackScreenProps<AuthStackParamList, 'ForgotPassword'>;

export default function ForgotPasswordScreen({ navigation }: Props) {
  const [email,      setEmail]      = useState('');
  const [loading,    setLoading]    = useState(false);
  const [sent,       setSent]       = useState(false);
  const [error,      setError]      = useState<string | null>(null);

  const handleSubmit = async () => {
    if (!email.trim()) { setError('Please enter your email'); return; }
    setError(null);
    setLoading(true);
    try {
      await apiClient.post('/auth/forgot-password', { email: email.trim().toLowerCase() });
      setSent(true);
    } catch (err) {
      setError(extractApiError(err));
    } finally {
      setLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView style={styles.flex} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">
        <View style={styles.header}>
          <Text style={styles.logo}>MAPELead</Text>
          <Text style={styles.title}>Reset password</Text>
          <Text style={styles.subtitle}>We'll send a reset link to your email</Text>
        </View>

        <View style={styles.card}>
          {sent ? (
            <View style={styles.successBox}>
              <Text style={styles.successText}>
                ✅ Check your email for a password reset link.
              </Text>
            </View>
          ) : (
            <>
              {error && <Text style={styles.errorBanner}>{error}</Text>}
              <View style={styles.field}>
                <Text style={styles.label}>Email Address</Text>
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

        <TouchableOpacity style={styles.backRow} onPress={() => navigation.navigate('Login')}>
          <Text style={styles.backLink}>← Back to Sign In</Text>
        </TouchableOpacity>
      </ScrollView>
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
  errorBanner:   { backgroundColor: '#fef2f2', color: Colors.error, borderRadius: Radii.md, padding: Spacing[3], marginBottom: Spacing[4], fontSize: Typography.sizes.sm },
  successBox:    { backgroundColor: '#f0fdf4', borderRadius: Radii.md, padding: Spacing[4] },
  successText:   { color: Colors.success, fontSize: Typography.sizes.base, textAlign: 'center' },
  field:         { marginBottom: Spacing[4] },
  label:         { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.medium, color: Colors.gray700, marginBottom: Spacing[1] },
  input:         { borderWidth: 1, borderColor: Colors.gray300, borderRadius: Radii.lg, paddingHorizontal: Spacing[4], paddingVertical: Spacing[3], fontSize: Typography.sizes.base, color: Colors.textPrimary },
  button:        { backgroundColor: Colors.primary, borderRadius: Radii.lg, paddingVertical: Spacing[4], alignItems: 'center', marginTop: Spacing[2] },
  buttonDisabled:{ opacity: 0.6 },
  buttonText:    { color: Colors.white, fontSize: Typography.sizes.md, fontWeight: Typography.weights.semibold },
  backRow:       { alignItems: 'center', marginTop: Spacing[6] },
  backLink:      { color: Colors.white, fontSize: Typography.sizes.sm, fontWeight: Typography.weights.medium },
});
