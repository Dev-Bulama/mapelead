import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity,
  StyleSheet, ScrollView, KeyboardAvoidingView, Platform,
  Alert, ActivityIndicator,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { WebView } from 'react-native-webview';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { useMutation, useQuery } from '@tanstack/react-query';
import { apiClient, extractApiError } from '@/api/client';
import { API } from '@/api/endpoints';
import { useAuthStore, selectUser } from '@/stores/authStore';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { ApiResponse, RootStackParamList } from '@/types';

type Props = NativeStackScreenProps<RootStackParamList, 'Security'>;

interface TwoFASetupData {
  manual_entry_key: string;
  otpauth_url:      string;
  qr_code_svg:      string;
}

// ── Reusable password field ───────────────────────────────────────────────────
function PwdField({ label, value, onChangeText, show, onToggle, placeholder }: {
  label: string; value: string; onChangeText: (v: string) => void;
  show: boolean; onToggle: () => void; placeholder?: string;
}) {
  return (
    <View style={S.field}>
      <Text style={S.label}>{label}</Text>
      <View style={S.inputWrap}>
        <TextInput
          style={S.input}
          value={value} onChangeText={onChangeText}
          placeholder={placeholder ?? '••••••••'}
          placeholderTextColor={Colors.gray400}
          secureTextEntry={!show}
        />
        <TouchableOpacity onPress={onToggle} style={S.eyeBtn} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
          <Ionicons name={show ? 'eye-off-outline' : 'eye-outline'} size={18} color={Colors.gray400} />
        </TouchableOpacity>
      </View>
    </View>
  );
}

// ── Main screen ───────────────────────────────────────────────────────────────
export default function SecurityScreen({ navigation }: Props) {
  const user        = useAuthStore(selectUser);
  const refreshUser = useAuthStore((s) => s.refreshUser);

  // ── Change password ──────────────────────────────────────────────────────────
  const [current,  setCurrent]  = useState('');
  const [password, setPassword] = useState('');
  const [confirm,  setConfirm]  = useState('');
  const [show,     setShow]     = useState({ current: false, password: false, confirm: false });

  const toggleShow = (k: keyof typeof show) => () => setShow(v => ({ ...v, [k]: !v[k] }));

  const { mutate: changePassword, isPending: changingPwd } = useMutation({
    mutationFn: () => apiClient.post(API.AUTH_CHANGE_PASSWORD, {
      current_password: current, password, password_confirmation: confirm,
    }),
    onSuccess: () => {
      setCurrent(''); setPassword(''); setConfirm('');
      Alert.alert('Success', 'Your password has been changed.');
    },
    onError: (err) => Alert.alert('Error', extractApiError(err)),
  });

  const handleChangePwd = () => {
    if (!current || !password || !confirm) { Alert.alert('Error', 'Please fill in all fields.'); return; }
    if (password !== confirm) { Alert.alert('Error', 'New passwords do not match.'); return; }
    changePassword();
  };

  // ── 2FA setup ────────────────────────────────────────────────────────────────
  const [showing2FASetup, setShowing2FASetup] = useState(false);
  const [totpCode,        setTotpCode]        = useState('');
  const [disablePwd,      setDisablePwd]      = useState('');
  const [showDisablePwd,  setShowDisablePwd]  = useState(false);
  const [showingDisable,  setShowingDisable]  = useState(false);

  const { data: setupData, isFetching: loadingSetup, refetch: fetchSetup } = useQuery({
    queryKey: ['2fa', 'setup'],
    queryFn:  () => apiClient.get<ApiResponse<TwoFASetupData>>(API.TWO_FA_SETUP).then(r => r.data.data),
    enabled:  false,
    staleTime: 0,
  });

  const { mutate: enable2FA, isPending: enabling } = useMutation({
    mutationFn: () => apiClient.post<ApiResponse<typeof user>>(API.TWO_FA_ENABLE, { totp_code: totpCode }),
    onSuccess: async () => {
      await refreshUser();
      setShowing2FASetup(false);
      setTotpCode('');
      Alert.alert('2FA Enabled', 'Two-factor authentication is now active on your account.');
    },
    onError: (err) => Alert.alert('Invalid Code', extractApiError(err)),
  });

  const { mutate: disable2FA, isPending: disabling } = useMutation({
    mutationFn: () => apiClient.delete<ApiResponse<typeof user>>(API.TWO_FA_DISABLE, { data: { password: disablePwd } }),
    onSuccess: async () => {
      await refreshUser();
      setShowingDisable(false);
      setDisablePwd('');
      Alert.alert('2FA Disabled', 'Two-factor authentication has been turned off.');
    },
    onError: (err) => Alert.alert('Error', extractApiError(err)),
  });

  const handleShowSetup = async () => {
    setShowing2FASetup(true);
    await fetchSetup();
  };

  const handleEnable = () => {
    if (totpCode.length !== 6) { Alert.alert('Error', 'Enter the 6-digit code from your authenticator app.'); return; }
    enable2FA();
  };

  const handleDisable = () => {
    if (!disablePwd) { Alert.alert('Error', 'Please enter your password to confirm.'); return; }
    disable2FA();
  };

  const is2FAEnabled = user?.two_factor_enabled ?? false;

  return (
    <KeyboardAvoidingView style={S.flex} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      {/* Header */}
      <View style={S.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={{ width: 50 }}>
          <Ionicons name="arrow-back" size={22} color={Colors.primary} />
        </TouchableOpacity>
        <Text style={S.headerTitle}>Security</Text>
        <View style={{ width: 50 }} />
      </View>

      <ScrollView contentContainerStyle={S.scroll} keyboardShouldPersistTaps="handled">

        {/* ── Change Password ── */}
        <View style={S.card}>
          <View style={S.sectionTitleRow}>
            <Ionicons name="lock-closed-outline" size={18} color={Colors.primary} style={{ marginRight: 6 }} />
            <Text style={S.sectionTitle}>Change Password</Text>
          </View>
          <Text style={S.sectionSub}>Choose a strong password to keep your account secure</Text>

          <PwdField label="Current Password"      value={current}  onChangeText={setCurrent}  show={show.current}  onToggle={toggleShow('current')} />
          <PwdField label="New Password"           value={password} onChangeText={setPassword} show={show.password} onToggle={toggleShow('password')} placeholder="8+ chars, upper, number, symbol" />
          <PwdField label="Confirm New Password"   value={confirm}  onChangeText={setConfirm}  show={show.confirm}  onToggle={toggleShow('confirm')} />

          <TouchableOpacity style={[S.cta, changingPwd && S.ctaDisabled]} onPress={handleChangePwd} disabled={changingPwd} activeOpacity={0.85}>
            <Text style={S.ctaText}>{changingPwd ? 'Saving…' : 'Update Password'}</Text>
          </TouchableOpacity>
        </View>

        <View style={S.tipsCard}>
          <Text style={S.tipsTitle}>Password tips</Text>
          {['At least 8 characters', 'Mix of uppercase & lowercase', 'At least one number', 'At least one symbol (!@#$…)'].map(tip => (
            <Text key={tip} style={S.tip}>• {tip}</Text>
          ))}
        </View>

        {/* ── Two-Factor Authentication ── */}
        <View style={[S.card, { marginTop: Spacing[4] }]}>
          <View style={S.twoFAHeader}>
            <View style={{ flex: 1 }}>
              <View style={S.sectionTitleRow}>
                <Ionicons name="key-outline" size={18} color={Colors.primary} style={{ marginRight: 6 }} />
                <Text style={S.sectionTitle}>Two-Factor Authentication</Text>
              </View>
              <Text style={S.sectionSub}>
                {is2FAEnabled
                  ? 'Your account has an extra layer of security.'
                  : 'Add an extra layer of protection with an authenticator app.'}
              </Text>
            </View>
            <View style={[S.statusBadge, is2FAEnabled ? S.statusOn : S.statusOff]}>
              <Text style={S.statusText}>{is2FAEnabled ? 'ON' : 'OFF'}</Text>
            </View>
          </View>

          {/* Enabled state — show disable */}
          {is2FAEnabled && !showingDisable && (
            <TouchableOpacity style={S.ctaOutline} onPress={() => setShowingDisable(true)} activeOpacity={0.8}>
              <Text style={S.ctaOutlineText}>Disable Two-Factor Auth</Text>
            </TouchableOpacity>
          )}

          {/* Disable form */}
          {is2FAEnabled && showingDisable && (
            <View style={S.disableForm}>
              <Text style={S.disableNote}>Enter your password to confirm disabling 2FA.</Text>
              <View style={S.inputWrap}>
                <TextInput
                  style={S.input}
                  value={disablePwd} onChangeText={setDisablePwd}
                  placeholder="Your current password"
                  placeholderTextColor={Colors.gray400}
                  secureTextEntry={!showDisablePwd}
                />
                <TouchableOpacity onPress={() => setShowDisablePwd(v => !v)} style={S.eyeBtn}>
                  <Ionicons name={showDisablePwd ? 'eye-off-outline' : 'eye-outline'} size={18} color={Colors.gray400} />
                </TouchableOpacity>
              </View>
              <View style={S.disableActions}>
                <TouchableOpacity style={S.cancelBtn} onPress={() => { setShowingDisable(false); setDisablePwd(''); }}>
                  <Text style={S.cancelText}>Cancel</Text>
                </TouchableOpacity>
                <TouchableOpacity style={[S.cta, { flex: 1 }, disabling && S.ctaDisabled]} onPress={handleDisable} disabled={disabling}>
                  <Text style={S.ctaText}>{disabling ? 'Disabling…' : 'Confirm Disable'}</Text>
                </TouchableOpacity>
              </View>
            </View>
          )}

          {/* Not enabled — show setup */}
          {!is2FAEnabled && !showing2FASetup && (
            <TouchableOpacity style={S.cta} onPress={handleShowSetup} activeOpacity={0.85}>
              <Text style={S.ctaText}>Set Up Two-Factor Auth</Text>
            </TouchableOpacity>
          )}

          {/* Setup flow */}
          {!is2FAEnabled && showing2FASetup && (
            <View>
              {loadingSetup ? (
                <ActivityIndicator color={Colors.primary} style={{ marginVertical: Spacing[4] }} />
              ) : setupData ? (
                <>
                  <Text style={S.setupStep}>Step 1: Scan this QR code with Google Authenticator, Authy, or any TOTP app.</Text>

                  {/* QR code in WebView */}
                  {setupData.qr_code_svg ? (
                    <View style={S.qrWrap}>
                      <WebView
                        source={{ html: `<!DOCTYPE html><html><body style="margin:0;background:#fff;display:flex;align-items:center;justify-content:center;height:100vh">${setupData.qr_code_svg}</body></html>` }}
                        style={S.qrView}
                        scrollEnabled={false}
                        scalesPageToFit
                      />
                    </View>
                  ) : null}

                  <Text style={S.orText}>Or enter this key manually:</Text>
                  <View style={S.manualKeyWrap}>
                    <Text style={S.manualKey} selectable>{setupData.manual_entry_key}</Text>
                  </View>

                  <Text style={S.setupStep}>Step 2: Enter the 6-digit code shown in your authenticator app.</Text>

                  <View style={S.inputWrap}>
                    <TextInput
                      style={[S.input, { letterSpacing: 6, textAlign: 'center', fontSize: Typography.sizes.xl, fontWeight: Typography.weights.bold }]}
                      value={totpCode} onChangeText={(v) => setTotpCode(v.replace(/\D/g, '').slice(0, 6))}
                      placeholder="000000"
                      placeholderTextColor={Colors.gray300}
                      keyboardType="number-pad"
                      maxLength={6}
                    />
                  </View>

                  <View style={S.disableActions}>
                    <TouchableOpacity style={S.cancelBtn} onPress={() => { setShowing2FASetup(false); setTotpCode(''); }}>
                      <Text style={S.cancelText}>Cancel</Text>
                    </TouchableOpacity>
                    <TouchableOpacity
                      style={[S.cta, { flex: 1 }, (enabling || totpCode.length !== 6) && S.ctaDisabled]}
                      onPress={handleEnable}
                      disabled={enabling || totpCode.length !== 6}
                    >
                      <Text style={S.ctaText}>{enabling ? 'Verifying…' : 'Verify & Enable'}</Text>
                    </TouchableOpacity>
                  </View>
                </>
              ) : (
                <TouchableOpacity style={S.cta} onPress={() => fetchSetup()}>
                  <Text style={S.ctaText}>Retry</Text>
                </TouchableOpacity>
              )}
            </View>
          )}
        </View>

        <View style={{ height: Spacing[8] }} />
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
  sectionTitleRow: { flexDirection: 'row', alignItems: 'center', marginBottom: 4 },
  sectionTitle: { fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  sectionSub:   { fontSize: Typography.sizes.sm, color: Colors.textSecondary, marginBottom: Spacing[4] },
  field:        { marginBottom: Spacing[4] },
  label:        { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.medium, color: Colors.textSecondary, marginBottom: 6 },
  inputWrap:    { flexDirection: 'row', alignItems: 'center', borderWidth: 1, borderColor: Colors.gray200, borderRadius: Radii.lg, paddingHorizontal: Spacing[4], backgroundColor: Colors.gray50 },
  input:        { flex: 1, paddingVertical: 14, fontSize: Typography.sizes.base, color: Colors.textPrimary },
  eyeBtn:       { paddingLeft: 8 },
  cta:          { backgroundColor: Colors.primary, borderRadius: Radii.xl, paddingVertical: 16, alignItems: 'center', marginTop: Spacing[2], ...Shadows.sm },
  ctaDisabled:  { opacity: 0.55 },
  ctaText:      { color: Colors.white, fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold },
  ctaOutline:   { borderWidth: 1.5, borderColor: Colors.error, borderRadius: Radii.xl, paddingVertical: 14, alignItems: 'center', marginTop: Spacing[2] },
  ctaOutlineText:{ color: Colors.error, fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold },
  tipsCard:     { backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[5], ...Shadows.sm },
  tipsTitle:    { fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: Spacing[3] },
  tip:          { fontSize: Typography.sizes.sm, color: Colors.textSecondary, marginBottom: 6, lineHeight: 20 },

  // 2FA
  twoFAHeader:  { flexDirection: 'row', alignItems: 'flex-start', marginBottom: Spacing[2] },
  statusBadge:  { borderRadius: Radii.full, paddingHorizontal: Spacing[3], paddingVertical: 4, alignSelf: 'flex-start' },
  statusOn:     { backgroundColor: 'rgba(16,185,129,0.1)' },
  statusOff:    { backgroundColor: Colors.gray100 },
  statusText:   { fontSize: Typography.sizes.xs, fontWeight: Typography.weights.bold, color: Colors.textSecondary },
  setupStep:    { fontSize: Typography.sizes.sm, color: Colors.textSecondary, marginBottom: Spacing[3], lineHeight: 20 },
  qrWrap:       { width: 200, height: 200, alignSelf: 'center', borderRadius: Radii.lg, overflow: 'hidden', borderWidth: 1, borderColor: Colors.gray200, marginBottom: Spacing[3] },
  qrView:       { flex: 1, backgroundColor: 'white' },
  orText:       { fontSize: Typography.sizes.xs, color: Colors.textMuted, textAlign: 'center', marginBottom: Spacing[2] },
  manualKeyWrap:{ backgroundColor: Colors.gray100, borderRadius: Radii.lg, padding: Spacing[4], alignItems: 'center', marginBottom: Spacing[4] },
  manualKey:    { fontSize: Typography.sizes.sm, fontFamily: 'monospace', color: Colors.navy, fontWeight: Typography.weights.bold, letterSpacing: 2 },
  disableForm:  { marginTop: Spacing[2] },
  disableNote:  { fontSize: Typography.sizes.sm, color: Colors.textSecondary, marginBottom: Spacing[3] },
  disableActions:{ flexDirection: 'row', gap: Spacing[3], marginTop: Spacing[3] },
  cancelBtn:    { flex: 1, borderWidth: 1, borderColor: Colors.gray200, borderRadius: Radii.xl, paddingVertical: 14, alignItems: 'center' },
  cancelText:   { fontSize: Typography.sizes.base, color: Colors.textSecondary, fontWeight: Typography.weights.medium },
});
