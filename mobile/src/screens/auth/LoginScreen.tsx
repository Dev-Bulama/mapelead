import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity,
  StyleSheet, ScrollView, KeyboardAvoidingView, Platform, Alert,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { useAuthStore } from '@/stores/authStore';
import { extractApiError } from '@/api/client';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { AuthStackParamList } from '@/types';

type Props = NativeStackScreenProps<AuthStackParamList, 'Login'>;

export default function LoginScreen({ navigation }: Props) {
  const { login, isLoading } = useAuthStore();

  const [email,     setEmail]     = useState('');
  const [password,  setPassword]  = useState('');
  const [totpCode,  setTotpCode]  = useState('');
  const [needs2FA,  setNeeds2FA]  = useState(false);
  const [error,     setError]     = useState<string | null>(null);

  const handleLogin = async () => {
    if (!email.trim() || !password.trim()) {
      setError('Please enter your email and password');
      return;
    }
    setError(null);
    try {
      await login({ email: email.trim().toLowerCase(), password, totp_code: totpCode || undefined });
    } catch (err: unknown) {
      const msg = extractApiError(err);
      if (msg.toLowerCase().includes('two-factor')) {
        setNeeds2FA(true);
        setError('Please enter your 2FA code.');
      } else {
        setError(msg);
      }
    }
  };

  return (
    <KeyboardAvoidingView
      style={styles.flex}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">
        {/* Header */}
        <View style={styles.header}>
          <Text style={styles.logo}>MAPELead</Text>
          <Text style={styles.title}>Welcome back</Text>
          <Text style={styles.subtitle}>Sign in to continue learning</Text>
        </View>

        {/* Form card */}
        <View style={styles.card}>
          {error && <Text style={styles.errorBanner}>{error}</Text>}

          <View style={styles.field}>
            <Text style={styles.label}>Email</Text>
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

          <View style={styles.field}>
            <Text style={styles.label}>Password</Text>
            <TextInput
              style={styles.input}
              value={password}
              onChangeText={setPassword}
              placeholder="••••••••"
              placeholderTextColor={Colors.gray400}
              secureTextEntry
            />
          </View>

          {needs2FA && (
            <View style={styles.field}>
              <Text style={styles.label}>2FA Code</Text>
              <TextInput
                style={styles.input}
                value={totpCode}
                onChangeText={setTotpCode}
                placeholder="6-digit code"
                placeholderTextColor={Colors.gray400}
                keyboardType="number-pad"
                maxLength={6}
              />
            </View>
          )}

          <TouchableOpacity
            style={[styles.button, isLoading && styles.buttonDisabled]}
            onPress={handleLogin}
            disabled={isLoading}
          >
            <Text style={styles.buttonText}>{isLoading ? 'Signing in…' : 'Sign In'}</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.linkRow}
            onPress={() => navigation.navigate('ForgotPassword')}
          >
            <Text style={styles.link}>Forgot password?</Text>
          </TouchableOpacity>
        </View>

        {/* Register link */}
        <View style={styles.footer}>
          <Text style={styles.footerText}>Don't have an account? </Text>
          <TouchableOpacity onPress={() => navigation.navigate('Register')}>
            <Text style={styles.footerLink}>Sign Up</Text>
          </TouchableOpacity>
        </View>
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
  subtitle:      { fontSize: Typography.sizes.base, color: 'rgba(255,255,255,0.8)', marginTop: Spacing[1] },
  card:          { backgroundColor: Colors.white, borderRadius: Radii['2xl'], padding: Spacing[6], ...Shadows.lg },
  errorBanner:   { backgroundColor: '#fef2f2', color: Colors.error, borderRadius: Radii.md, padding: Spacing[3], marginBottom: Spacing[4], fontSize: Typography.sizes.sm },
  field:         { marginBottom: Spacing[4] },
  label:         { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.medium, color: Colors.gray700, marginBottom: Spacing[1] },
  input:         { borderWidth: 1, borderColor: Colors.gray300, borderRadius: Radii.lg, paddingHorizontal: Spacing[4], paddingVertical: Spacing[3], fontSize: Typography.sizes.base, color: Colors.textPrimary },
  button:        { backgroundColor: Colors.primary, borderRadius: Radii.lg, paddingVertical: Spacing[4], alignItems: 'center', marginTop: Spacing[2] },
  buttonDisabled:{ opacity: 0.6 },
  buttonText:    { color: Colors.white, fontSize: Typography.sizes.md, fontWeight: Typography.weights.semibold },
  linkRow:       { alignItems: 'center', marginTop: Spacing[4] },
  link:          { color: Colors.primary, fontSize: Typography.sizes.sm, fontWeight: Typography.weights.medium },
  footer:        { flexDirection: 'row', justifyContent: 'center', marginTop: Spacing[6] },
  footerText:    { color: 'rgba(255,255,255,0.8)', fontSize: Typography.sizes.sm },
  footerLink:    { color: Colors.white, fontSize: Typography.sizes.sm, fontWeight: Typography.weights.bold },
});
