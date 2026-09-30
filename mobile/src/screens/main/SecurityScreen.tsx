import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity,
  StyleSheet, ScrollView, KeyboardAvoidingView, Platform, Alert,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { useMutation } from '@tanstack/react-query';
import { apiClient } from '@/api/client';
import { extractApiError } from '@/api/client';
import { API } from '@/api/endpoints';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { RootStackParamList } from '@/types';

type Props = NativeStackScreenProps<RootStackParamList, 'Security'>;

function PwdField({
  label, value, onChangeText, show, onToggle, placeholder,
}: { label: string; value: string; onChangeText: (v: string) => void; show: boolean; onToggle: () => void; placeholder?: string }) {
  return (
    <View style={S.field}>
      <Text style={S.label}>{label}</Text>
      <View style={S.inputWrap}>
        <TextInput
          style={S.input}
          value={value}
          onChangeText={onChangeText}
          placeholder={placeholder ?? '••••••••'}
          placeholderTextColor={Colors.gray400}
          secureTextEntry={!show}
        />
        <TouchableOpacity onPress={onToggle} style={S.eyeBtn}>
          <Text style={{ fontSize: 18 }}>{show ? '🙈' : '👁️'}</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

export default function SecurityScreen({ navigation }: Props) {
  const [current,  setCurrent]  = useState('');
  const [password, setPassword] = useState('');
  const [confirm,  setConfirm]  = useState('');
  const [show,     setShow]     = useState({ current: false, password: false, confirm: false });

  const toggleShow = (k: keyof typeof show) => () => setShow(v => ({ ...v, [k]: !v[k] }));

  const { mutate: changePassword, isPending } = useMutation({
    mutationFn: () => apiClient.post(API.AUTH_CHANGE_PASSWORD, {
      current_password: current,
      password,
      password_confirmation: confirm,
    }),
    onSuccess: () => {
      Alert.alert('Success', 'Your password has been changed.', [
        { text: 'OK', onPress: () => navigation.goBack() },
      ]);
    },
    onError: (err) => Alert.alert('Error', extractApiError(err)),
  });

  const handleSubmit = () => {
    if (!current || !password || !confirm) {
      Alert.alert('Error', 'Please fill in all fields.');
      return;
    }
    if (password !== confirm) {
      Alert.alert('Error', 'New passwords do not match.');
      return;
    }
    changePassword();
  };

  return (
    <KeyboardAvoidingView style={S.flex} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <View style={S.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={S.back}>‹ Back</Text>
        </TouchableOpacity>
        <Text style={S.headerTitle}>Security</Text>
        <View style={{ width: 50 }} />
      </View>

      <ScrollView contentContainerStyle={S.scroll} keyboardShouldPersistTaps="handled">
        <View style={S.card}>
          <Text style={S.sectionTitle}>🔒 Change Password</Text>
          <Text style={S.sectionSubtitle}>Choose a strong password to keep your account secure</Text>

          <PwdField
            label="Current Password" value={current} onChangeText={setCurrent}
            show={show.current} onToggle={toggleShow('current')}
          />
          <PwdField
            label="New Password" value={password} onChangeText={setPassword}
            show={show.password} onToggle={toggleShow('password')}
            placeholder="8+ chars, upper, number, symbol"
          />
          <PwdField
            label="Confirm New Password" value={confirm} onChangeText={setConfirm}
            show={show.confirm} onToggle={toggleShow('confirm')}
          />

          <TouchableOpacity
            style={[S.cta, isPending && S.ctaDisabled]}
            onPress={handleSubmit}
            disabled={isPending}
            activeOpacity={0.85}
          >
            <Text style={S.ctaText}>{isPending ? 'Saving…' : 'Update Password'}</Text>
          </TouchableOpacity>
        </View>

        <View style={S.tipsCard}>
          <Text style={S.tipsTitle}>Password tips</Text>
          {['At least 8 characters', 'Mix of uppercase & lowercase', 'At least one number', 'At least one symbol (!@#$…)'].map(tip => (
            <Text key={tip} style={S.tip}>• {tip}</Text>
          ))}
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const S = StyleSheet.create({
  flex:         { flex: 1, backgroundColor: Colors.surface },
  header:       { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingHorizontal: Spacing[4], paddingTop: Spacing[12], paddingBottom: Spacing[3], backgroundColor: Colors.white, borderBottomWidth: 1, borderBottomColor: Colors.gray100 },
  back:         { fontSize: Typography.sizes.base, color: Colors.primary, fontWeight: Typography.weights.medium, width: 50 },
  headerTitle:  { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  scroll:       { padding: Spacing[4] },
  card:         { backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[5], ...Shadows.sm, marginBottom: Spacing[4] },
  sectionTitle: { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.bold, color: Colors.textPrimary, marginBottom: 4 },
  sectionSubtitle: { fontSize: Typography.sizes.sm, color: Colors.textSecondary, marginBottom: Spacing[5] },
  field:        { marginBottom: Spacing[4] },
  label:        { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.medium, color: Colors.textSecondary, marginBottom: 6 },
  inputWrap:    { flexDirection: 'row', alignItems: 'center', borderWidth: 1, borderColor: Colors.gray200, borderRadius: Radii.lg, paddingHorizontal: Spacing[4], backgroundColor: Colors.gray50 },
  input:        { flex: 1, paddingVertical: 14, fontSize: Typography.sizes.base, color: Colors.textPrimary },
  eyeBtn:       { paddingLeft: 8 },
  cta:          { backgroundColor: Colors.primary, borderRadius: Radii.xl, paddingVertical: 16, alignItems: 'center', marginTop: Spacing[2], ...Shadows.sm },
  ctaDisabled:  { opacity: 0.6 },
  ctaText:      { color: Colors.white, fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold },
  tipsCard:     { backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[5], ...Shadows.sm },
  tipsTitle:    { fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: Spacing[3] },
  tip:          { fontSize: Typography.sizes.sm, color: Colors.textSecondary, marginBottom: 6, lineHeight: 20 },
});
