import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, Image,
  StyleSheet, ScrollView, KeyboardAvoidingView, Platform, ActivityIndicator,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { useAuthStore } from '@/stores/authStore';
import { extractApiError } from '@/api/client';
import { useAppSettings } from '@/hooks/useAppSettings';
import AlertModal from '@/components/common/AlertModal';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { AuthStackParamList } from '@/types';

type Props = NativeStackScreenProps<AuthStackParamList, 'Login'>;
type IoniconsName = React.ComponentProps<typeof Ionicons>['name'];

// ── Google coloured "G" logo ──────────────────────────────────────────────────
function GoogleLogo() {
  return (
    <View style={{ width: 18, height: 18, alignItems: 'center', justifyContent: 'center' }}>
      <Text style={{ fontSize: 15, fontWeight: '700', color: '#4285F4', letterSpacing: -0.5 }}>G</Text>
    </View>
  );
}

// ── Logo mark (static fallback) ───────────────────────────────────────────────
function LogoMark() {
  return (
    <View style={logo.wrap}>
      <View style={logo.box}>
        <Text style={logo.m}>M</Text>
      </View>
      <View style={logo.dots}>
        <View style={[logo.dot, { backgroundColor: '#00c9e4' }]} />
        <View style={[logo.dot, { backgroundColor: '#ff6b6b' }]} />
        <View style={[logo.dot, { backgroundColor: '#ffd93d' }]} />
      </View>
    </View>
  );
}
const logo = StyleSheet.create({
  wrap: { flexDirection: 'row', alignItems: 'center', gap: 6 },
  box:  { width: 44, height: 44, borderRadius: 10, backgroundColor: Colors.white, alignItems: 'center', justifyContent: 'center' },
  m:    { fontSize: 26, fontWeight: '900', color: Colors.navy, letterSpacing: -1 },
  dots: { flexDirection: 'column', gap: 3 },
  dot:  { width: 6, height: 6, borderRadius: 3 },
});

// ── AppLogo — fetched from backend, falls back to LogoMark ───────────────────
function AppLogo({ logoUrl }: { logoUrl?: string | null }) {
  if (logoUrl) {
    return (
      <Image
        source={{ uri: logoUrl }}
        style={{ width: 72, height: 72, borderRadius: 16, resizeMode: 'contain' }}
      />
    );
  }
  return <LogoMark />;
}

// ── Social button (Google / Apple) ───────────────────────────────────────────
function SocialButton({
  icon, label, onPress,
}: { icon: React.ReactNode; label: string; onPress?: () => void }) {
  return (
    <TouchableOpacity style={soc.btn} onPress={onPress} activeOpacity={0.7}>
      {icon}
      <Text style={soc.label}>{label}</Text>
    </TouchableOpacity>
  );
}
const soc = StyleSheet.create({
  btn:   { flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8, borderWidth: 1, borderColor: Colors.gray200, borderRadius: Radii.lg, paddingVertical: 12 },
  label: { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.medium, color: Colors.textPrimary },
});

// ── Icon input ────────────────────────────────────────────────────────────────
function IconInput({
  icon, value, onChangeText, placeholder, secureTextEntry, keyboardType, autoCapitalize, autoCorrect,
  rightElement,
}: {
  icon: IoniconsName; value: string; onChangeText: (v: string) => void; placeholder: string;
  secureTextEntry?: boolean; keyboardType?: any; autoCapitalize?: any; autoCorrect?: boolean;
  rightElement?: React.ReactNode;
}) {
  return (
    <View style={inp.wrap}>
      <Ionicons name={icon} size={16} color={Colors.gray400} style={{ marginRight: Spacing[2] }} />
      <TextInput
        style={inp.field}
        value={value} onChangeText={onChangeText} placeholder={placeholder}
        placeholderTextColor={Colors.gray400}
        secureTextEntry={secureTextEntry} keyboardType={keyboardType}
        autoCapitalize={autoCapitalize} autoCorrect={autoCorrect}
      />
      {rightElement}
    </View>
  );
}
const inp = StyleSheet.create({
  wrap:  { flexDirection: 'row', alignItems: 'center', borderWidth: 1, borderColor: Colors.gray200, borderRadius: Radii.lg, paddingHorizontal: Spacing[4], backgroundColor: Colors.gray50 },
  field: { flex: 1, paddingVertical: 14, fontSize: Typography.sizes.base, color: Colors.textPrimary },
});

// ── Screen ────────────────────────────────────────────────────────────────────
export default function LoginScreen({ navigation }: Props) {
  const { login, isLoading } = useAuthStore();
  const { data: settings }   = useAppSettings();

  const [email,      setEmail]      = useState('');
  const [password,   setPassword]   = useState('');
  const [showPwd,    setShowPwd]    = useState(false);
  const [totpCode,   setTotpCode]   = useState('');
  const [needs2FA,   setNeeds2FA]   = useState(false);
  const [rememberMe, setRememberMe] = useState(false);
  const [alertMsg,   setAlertMsg]   = useState<string | null>(null);

  const handleLogin = async () => {
    if (!email.trim() || !password.trim()) {
      setAlertMsg('Please enter your email and password');
      return;
    }
    try {
      await login({ email: email.trim().toLowerCase(), password, totp_code: totpCode || undefined });
    } catch (err: unknown) {
      const msg = extractApiError(err);
      if (msg.toLowerCase().includes('two-factor')) {
        setNeeds2FA(true);
        setAlertMsg('Please enter your 2FA code to continue.');
      } else {
        setAlertMsg(msg);
      }
    }
  };

  return (
    <KeyboardAvoidingView style={S.flex} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <ScrollView contentContainerStyle={S.scroll} keyboardShouldPersistTaps="handled" showsVerticalScrollIndicator={false}>

        {/* Hero */}
        <View style={S.hero}>
          <AppLogo logoUrl={settings?.logo_url} />
          <Text style={S.brand}>{settings?.app_name ?? 'MAPELEAD LIMITED'}</Text>
          <Text style={S.tagline}>{settings?.tagline ?? 'Learn Today, Build Tomorrow'}</Text>
        </View>

        {/* Card */}
        <View style={S.card}>
          <Text style={S.heading}>Welcome Back</Text>
          <Text style={S.subheading}>Sign in to continue your learning journey</Text>

          {/* Social SSO */}
          <View style={S.socialRow}>
            <SocialButton
              icon={<GoogleLogo />}
              label="Google"
            />
            <SocialButton
              icon={<Ionicons name="logo-apple" size={18} color="#1a1a1a" />}
              label="Apple"
            />
          </View>

          <View style={S.divider}>
            <View style={S.divLine} />
            <Text style={S.divText}>or sign in with email</Text>
            <View style={S.divLine} />
          </View>

          {/* Email */}
          <View style={S.fieldGap}>
            <IconInput
              icon="mail-outline"
              value={email} onChangeText={setEmail}
              placeholder="Email address"
              keyboardType="email-address" autoCapitalize="none" autoCorrect={false}
            />
          </View>

          {/* Password */}
          <View style={S.fieldGap}>
            <IconInput
              icon="lock-closed-outline"
              value={password} onChangeText={setPassword}
              placeholder="Password" secureTextEntry={!showPwd}
              rightElement={
                <TouchableOpacity onPress={() => setShowPwd(v => !v)} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
                  <Ionicons name={showPwd ? 'eye-off-outline' : 'eye-outline'} size={18} color={Colors.gray400} style={{ paddingLeft: 8 }} />
                </TouchableOpacity>
              }
            />
          </View>

          {/* 2FA */}
          {needs2FA && (
            <View style={S.fieldGap}>
              <IconInput
                icon="keypad-outline"
                value={totpCode} onChangeText={setTotpCode}
                placeholder="6-digit 2FA code" keyboardType="number-pad"
              />
            </View>
          )}

          {/* Remember + Forgot */}
          <View style={S.meta}>
            <TouchableOpacity style={S.rememberRow} onPress={() => setRememberMe(v => !v)}>
              <View style={[S.checkbox, rememberMe && S.checkboxActive]}>
                {rememberMe && <Ionicons name="checkmark" size={11} color={Colors.white} />}
              </View>
              <Text style={S.rememberText}>Remember me</Text>
            </TouchableOpacity>
            <TouchableOpacity onPress={() => navigation.navigate('ForgotPassword')}>
              <Text style={S.forgotText}>Forgot password?</Text>
            </TouchableOpacity>
          </View>

          {/* CTA */}
          <TouchableOpacity
            style={[S.cta, isLoading && S.ctaDisabled]}
            onPress={handleLogin}
            disabled={isLoading}
            activeOpacity={0.85}
          >
            {isLoading
              ? <ActivityIndicator color={Colors.white} />
              : <Text style={S.ctaText}>Login</Text>
            }
          </TouchableOpacity>

          {/* Register link */}
          <View style={S.registerRow}>
            <Text style={S.registerText}>Don't have an account? </Text>
            <TouchableOpacity onPress={() => navigation.navigate('Register')}>
              <Text style={S.registerLink}>Sign Up</Text>
            </TouchableOpacity>
          </View>
        </View>

        {/* Security badge */}
        <View style={S.secBadge}>
          <Ionicons name="lock-closed" size={12} color="rgba(255,255,255,0.5)" />
          <Text style={S.secText}>256-bit SSL encryption · Your data is safe</Text>
        </View>

      </ScrollView>

      {/* Error popup */}
      <AlertModal
        visible={!!alertMsg}
        type={needs2FA ? 'info' : 'error'}
        title={needs2FA ? 'Two-Factor Auth' : 'Sign In Failed'}
        message={alertMsg ?? ''}
        onClose={() => setAlertMsg(null)}
        buttonLabel="OK"
      />
    </KeyboardAvoidingView>
  );
}

const S = StyleSheet.create({
  flex:          { flex: 1, backgroundColor: Colors.navy },
  scroll:        { flexGrow: 1 },

  hero:          { alignItems: 'center', paddingTop: 56, paddingBottom: 32, paddingHorizontal: Spacing[6] },
  brand:         { fontSize: 13, fontWeight: Typography.weights.bold, color: 'rgba(255,255,255,0.7)', letterSpacing: 3, marginTop: 12, textTransform: 'uppercase' },
  tagline:       { fontSize: Typography.sizes.md, fontWeight: Typography.weights.semibold, color: Colors.white, marginTop: 8, textAlign: 'center' },

  card:          { backgroundColor: Colors.white, borderTopLeftRadius: 32, borderTopRightRadius: 32, flex: 1, paddingHorizontal: Spacing[6], paddingTop: Spacing[8], paddingBottom: Spacing[6], ...Shadows.lg },
  heading:       { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.extrabold, color: Colors.textPrimary },
  subheading:    { fontSize: Typography.sizes.sm, color: Colors.textSecondary, marginTop: 4, marginBottom: Spacing[6] },

  socialRow:     { flexDirection: 'row', gap: Spacing[3], marginBottom: Spacing[4] },

  divider:       { flexDirection: 'row', alignItems: 'center', gap: Spacing[3], marginBottom: Spacing[4] },
  divLine:       { flex: 1, height: 1, backgroundColor: Colors.gray200 },
  divText:       { fontSize: Typography.sizes.xs, color: Colors.textMuted, flexShrink: 0 },

  fieldGap:      { marginBottom: Spacing[3] },

  meta:          { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginTop: 4, marginBottom: Spacing[5] },
  rememberRow:   { flexDirection: 'row', alignItems: 'center', gap: 8 },
  checkbox:      { width: 18, height: 18, borderRadius: 4, borderWidth: 1.5, borderColor: Colors.gray300, alignItems: 'center', justifyContent: 'center' },
  checkboxActive:{ backgroundColor: Colors.primary, borderColor: Colors.primary },
  rememberText:  { fontSize: Typography.sizes.sm, color: Colors.textSecondary },
  forgotText:    { fontSize: Typography.sizes.sm, color: Colors.primary, fontWeight: Typography.weights.medium },

  cta:           { backgroundColor: Colors.primary, borderRadius: Radii.xl, paddingVertical: 16, alignItems: 'center', ...Shadows.md },
  ctaDisabled:   { opacity: 0.6 },
  ctaText:       { color: Colors.white, fontSize: Typography.sizes.md, fontWeight: Typography.weights.bold },

  registerRow:   { flexDirection: 'row', justifyContent: 'center', marginTop: Spacing[5] },
  registerText:  { color: Colors.textSecondary, fontSize: Typography.sizes.sm },
  registerLink:  { color: Colors.primary, fontSize: Typography.sizes.sm, fontWeight: Typography.weights.bold },

  secBadge:      { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 6, paddingVertical: Spacing[4], backgroundColor: Colors.navy },
  secText:       { fontSize: Typography.sizes.xs, color: 'rgba(255,255,255,0.5)' },
});
