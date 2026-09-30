import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity,
  StyleSheet, ScrollView, KeyboardAvoidingView, Platform,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { useAuthStore } from '@/stores/authStore';
import { extractApiError } from '@/api/client';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { AuthStackParamList } from '@/types';

type Props = NativeStackScreenProps<AuthStackParamList, 'Register'>;

export default function RegisterScreen({ navigation }: Props) {
  const { register, isLoading } = useAuthStore();

  const [form, setForm] = useState({
    first_name: '', last_name: '', email: '', phone: '',
    password: '', password_confirmation: '',
  });
  const [error, setError] = useState<string | null>(null);

  const set = (field: keyof typeof form) => (value: string) =>
    setForm((prev) => ({ ...prev, [field]: value }));

  const handleRegister = async () => {
    const { first_name, last_name, email, password, password_confirmation } = form;
    if (!first_name || !last_name || !email || !password) {
      setError('Please fill in all required fields');
      return;
    }
    if (password !== password_confirmation) {
      setError('Passwords do not match');
      return;
    }
    setError(null);
    try {
      await register({ ...form, email: email.trim().toLowerCase() });
    } catch (err: unknown) {
      setError(extractApiError(err));
    }
  };

  return (
    <KeyboardAvoidingView
      style={styles.flex}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">
        <View style={styles.header}>
          <Text style={styles.logo}>MAPELead</Text>
          <Text style={styles.title}>Create account</Text>
          <Text style={styles.subtitle}>Start your learning journey today</Text>
        </View>

        <View style={styles.card}>
          {error && <Text style={styles.errorBanner}>{error}</Text>}

          <View style={styles.row}>
            <View style={[styles.field, styles.half]}>
              <Text style={styles.label}>First Name *</Text>
              <TextInput style={styles.input} value={form.first_name} onChangeText={set('first_name')} placeholder="Ada" placeholderTextColor={Colors.gray400} autoCapitalize="words" />
            </View>
            <View style={[styles.field, styles.half]}>
              <Text style={styles.label}>Last Name *</Text>
              <TextInput style={styles.input} value={form.last_name} onChangeText={set('last_name')} placeholder="Lovelace" placeholderTextColor={Colors.gray400} autoCapitalize="words" />
            </View>
          </View>

          <View style={styles.field}>
            <Text style={styles.label}>Email *</Text>
            <TextInput style={styles.input} value={form.email} onChangeText={set('email')} placeholder="you@example.com" placeholderTextColor={Colors.gray400} keyboardType="email-address" autoCapitalize="none" autoCorrect={false} />
          </View>

          <View style={styles.field}>
            <Text style={styles.label}>Phone (optional)</Text>
            <TextInput style={styles.input} value={form.phone} onChangeText={set('phone')} placeholder="+234 000 000 0000" placeholderTextColor={Colors.gray400} keyboardType="phone-pad" />
          </View>

          <View style={styles.field}>
            <Text style={styles.label}>Password *</Text>
            <TextInput style={styles.input} value={form.password} onChangeText={set('password')} placeholder="Min 8 chars, upper, number, symbol" placeholderTextColor={Colors.gray400} secureTextEntry />
          </View>

          <View style={styles.field}>
            <Text style={styles.label}>Confirm Password *</Text>
            <TextInput style={styles.input} value={form.password_confirmation} onChangeText={set('password_confirmation')} placeholder="Repeat password" placeholderTextColor={Colors.gray400} secureTextEntry />
          </View>

          <TouchableOpacity
            style={[styles.button, isLoading && styles.buttonDisabled]}
            onPress={handleRegister}
            disabled={isLoading}
          >
            <Text style={styles.buttonText}>{isLoading ? 'Creating account…' : 'Create Account'}</Text>
          </TouchableOpacity>
        </View>

        <View style={styles.footer}>
          <Text style={styles.footerText}>Already have an account? </Text>
          <TouchableOpacity onPress={() => navigation.navigate('Login')}>
            <Text style={styles.footerLink}>Sign In</Text>
          </TouchableOpacity>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  flex:          { flex: 1, backgroundColor: Colors.primary },
  scroll:        { flexGrow: 1, padding: Spacing[6] },
  header:        { alignItems: 'center', paddingVertical: Spacing[8] },
  logo:          { fontSize: Typography.sizes['3xl'], fontWeight: Typography.weights.extrabold, color: Colors.white, letterSpacing: -1 },
  title:         { fontSize: Typography.sizes.xl, fontWeight: Typography.weights.bold, color: Colors.white, marginTop: Spacing[4] },
  subtitle:      { fontSize: Typography.sizes.sm, color: 'rgba(255,255,255,0.8)', marginTop: Spacing[1] },
  card:          { backgroundColor: Colors.white, borderRadius: Radii['2xl'], padding: Spacing[6], ...Shadows.lg },
  errorBanner:   { backgroundColor: '#fef2f2', color: Colors.error, borderRadius: Radii.md, padding: Spacing[3], marginBottom: Spacing[4], fontSize: Typography.sizes.sm },
  row:           { flexDirection: 'row', gap: Spacing[3] },
  field:         { marginBottom: Spacing[4] },
  half:          { flex: 1 },
  label:         { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.medium, color: Colors.gray700, marginBottom: Spacing[1] },
  input:         { borderWidth: 1, borderColor: Colors.gray300, borderRadius: Radii.lg, paddingHorizontal: Spacing[4], paddingVertical: Spacing[3], fontSize: Typography.sizes.base, color: Colors.textPrimary },
  button:        { backgroundColor: Colors.primary, borderRadius: Radii.lg, paddingVertical: Spacing[4], alignItems: 'center', marginTop: Spacing[2] },
  buttonDisabled:{ opacity: 0.6 },
  buttonText:    { color: Colors.white, fontSize: Typography.sizes.md, fontWeight: Typography.weights.semibold },
  footer:        { flexDirection: 'row', justifyContent: 'center', marginTop: Spacing[6] },
  footerText:    { color: 'rgba(255,255,255,0.8)', fontSize: Typography.sizes.sm },
  footerLink:    { color: Colors.white, fontSize: Typography.sizes.sm, fontWeight: Typography.weights.bold },
});
