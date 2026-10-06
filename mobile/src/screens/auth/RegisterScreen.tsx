import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity,
  StyleSheet, ScrollView, KeyboardAvoidingView, Platform,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import { useAuthStore } from '@/stores/authStore';
import { extractApiError } from '@/api/client';
import AlertModal from '@/components/common/AlertModal';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { AuthStackParamList } from '@/types';

type IoniconsName = React.ComponentProps<typeof Ionicons>['name'];

type Props = NativeStackScreenProps<AuthStackParamList, 'Register'>;
type AccountType = 'student' | 'instructor';

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
  box:  { width: 36, height: 36, borderRadius: 8, backgroundColor: Colors.white, alignItems: 'center', justifyContent: 'center' },
  m:    { fontSize: 22, fontWeight: '900', color: Colors.navy, letterSpacing: -1 },
  dots: { flexDirection: 'column', gap: 3 },
  dot:  { width: 6, height: 6, borderRadius: 3 },
});

function IconInput({
  icon, value, onChangeText, placeholder, secureTextEntry, keyboardType, autoCapitalize,
  rightElement,
}: {
  icon: IoniconsName; value: string; onChangeText: (v: string) => void; placeholder: string;
  secureTextEntry?: boolean; keyboardType?: any; autoCapitalize?: any;
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
        autoCapitalize={autoCapitalize ?? 'none'}
      />
      {rightElement}
    </View>
  );
}
const inp = StyleSheet.create({
  wrap:  { flexDirection: 'row', alignItems: 'center', borderWidth: 1, borderColor: Colors.gray200, borderRadius: Radii.lg, paddingHorizontal: Spacing[4], backgroundColor: Colors.gray50 },
  field: { flex: 1, paddingVertical: 14, fontSize: Typography.sizes.base, color: Colors.textPrimary },
});

export default function RegisterScreen({ navigation }: Props) {
  const { register, isLoading } = useAuthStore();

  const [accountType, setAccountType] = useState<AccountType>('student');
  const [form, setForm] = useState({
    first_name: '', last_name: '', email: '', phone: '',
    password: '', password_confirmation: '', instructor_code: '',
  });
  const [showPwd,     setShowPwd]     = useState(false);
  const [showConfirm, setShowConfirm] = useState(false);
  const [agreedTerms, setAgreedTerms] = useState(false);
  const [alertMsg,    setAlertMsg]    = useState<string | null>(null);

  const set = (field: keyof typeof form) => (value: string) =>
    setForm((prev) => ({ ...prev, [field]: value }));

  const handleRegister = async () => {
    const { first_name, last_name, email, password, password_confirmation, instructor_code } = form;
    if (!first_name || !last_name || !email || !password) {
      setAlertMsg('Please fill in all required fields');
      return;
    }
    if (password !== password_confirmation) {
      setAlertMsg('Passwords do not match');
      return;
    }
    if (accountType === 'instructor' && !instructor_code.trim()) {
      setAlertMsg('Instructor ID is required for instructor registration');
      return;
    }
    if (!agreedTerms) {
      setAlertMsg('Please agree to the Terms & Conditions');
      return;
    }
    try {
      await register({
        ...form,
        email: email.trim().toLowerCase(),
        account_type: accountType,
        instructor_code: accountType === 'instructor' ? instructor_code.trim() : undefined,
      });
    } catch (err: unknown) {
      setAlertMsg(extractApiError(err));
    }
  };

  return (
    <KeyboardAvoidingView style={S.flex} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <ScrollView contentContainerStyle={S.scroll} keyboardShouldPersistTaps="handled" showsVerticalScrollIndicator={false}>

        {/* ── Hero ── */}
        <View style={S.hero}>
          <LogoMark />
          <Text style={S.brand}>MAPELEAD LIMITED</Text>
          <Text style={S.taglineBase}>Create Your Account</Text>
          <Text style={S.taglineCyan}>and Start Learning</Text>
        </View>

        {/* ── Card ── */}
        <View style={S.card}>

          {/* Account type toggle */}
          <View style={S.toggle}>
            <TouchableOpacity
              style={[S.toggleBtn, accountType === 'student' && S.toggleActive]}
              onPress={() => setAccountType('student')}
            >
              <View style={{ flexDirection: 'row', alignItems: 'center', gap: 6 }}>
                <Ionicons name="school-outline" size={15} color={accountType === 'student' ? Colors.primary : Colors.textSecondary} />
                <Text style={[S.toggleText, accountType === 'student' && S.toggleTextActive]}>Student</Text>
              </View>
            </TouchableOpacity>
            <TouchableOpacity
              style={[S.toggleBtn, accountType === 'instructor' && S.toggleActive]}
              onPress={() => setAccountType('instructor')}
            >
              <View style={{ flexDirection: 'row', alignItems: 'center', gap: 6 }}>
                <Ionicons name="briefcase-outline" size={15} color={accountType === 'instructor' ? Colors.primary : Colors.textSecondary} />
                <Text style={[S.toggleText, accountType === 'instructor' && S.toggleTextActive]}>Instructor</Text>
              </View>
            </TouchableOpacity>
          </View>


          {/* Name row */}
          <View style={S.row}>
            <View style={S.half}>
              <IconInput icon="person-outline" value={form.first_name} onChangeText={set('first_name')} placeholder="First name" autoCapitalize="words" />
            </View>
            <View style={S.half}>
              <IconInput icon="person-outline" value={form.last_name} onChangeText={set('last_name')} placeholder="Last name" autoCapitalize="words" />
            </View>
          </View>

          <View style={S.fieldGap}>
            <IconInput icon="mail-outline" value={form.email} onChangeText={set('email')} placeholder="Email address" keyboardType="email-address" />
          </View>

          <View style={S.fieldGap}>
            <IconInput icon="phone-portrait-outline" value={form.phone} onChangeText={set('phone')} placeholder="Phone number (optional)" keyboardType="phone-pad" />
          </View>

          <View style={S.fieldGap}>
            <IconInput
              icon="lock-closed-outline" value={form.password} onChangeText={set('password')}
              placeholder="Password (8+ chars, upper, number, symbol)"
              secureTextEntry={!showPwd}
              rightElement={
                <TouchableOpacity onPress={() => setShowPwd(v => !v)} style={{ paddingLeft: 8 }}>
                  <Ionicons name={showPwd ? 'eye-off-outline' : 'eye-outline'} size={18} color={Colors.gray400} />
                </TouchableOpacity>
              }
            />
          </View>

          <View style={S.fieldGap}>
            <IconInput
              icon="lock-closed-outline" value={form.password_confirmation} onChangeText={set('password_confirmation')}
              placeholder="Confirm password" secureTextEntry={!showConfirm}
              rightElement={
                <TouchableOpacity onPress={() => setShowConfirm(v => !v)} style={{ paddingLeft: 8 }}>
                  <Ionicons name={showConfirm ? 'eye-off-outline' : 'eye-outline'} size={18} color={Colors.gray400} />
                </TouchableOpacity>
              }
            />
          </View>

          {/* Instructor ID — only shown for instructor accounts */}
          {accountType === 'instructor' && (
            <View style={S.fieldGap}>
              <IconInput icon="id-card-outline" value={form.instructor_code} onChangeText={set('instructor_code')} placeholder="Instructor ID (pre-assigned by admin)" />
              <Text style={S.instructorHint}>Your Instructor ID is provided by the MAPELEAD admin team.</Text>
            </View>
          )}

          {/* Terms */}
          <TouchableOpacity style={S.termsRow} onPress={() => setAgreedTerms(v => !v)}>
            <View style={[S.checkbox, agreedTerms && S.checkboxActive]}>
              {agreedTerms && <Ionicons name="checkmark" size={12} color={Colors.white} />}
            </View>
            <Text style={S.termsText}>
              I agree to the <Text style={S.termsLink}>Terms & Conditions</Text> and <Text style={S.termsLink}>Privacy Policy</Text>
            </Text>
          </TouchableOpacity>

          {/* CTA */}
          <TouchableOpacity style={[S.cta, isLoading && S.ctaDisabled]} onPress={handleRegister} disabled={isLoading} activeOpacity={0.85}>
            <Text style={S.ctaText}>{isLoading ? 'Creating account…' : 'Create Account'}</Text>
          </TouchableOpacity>

          {/* Social SSO */}
          <View style={S.divider}>
            <View style={S.divLine} />
            <Text style={S.divText}>or continue with</Text>
            <View style={S.divLine} />
          </View>
          <View style={S.socialRow}>
            <TouchableOpacity style={S.socialBtn}>
              <Text style={{ fontSize: 15, fontWeight: '700', color: '#4285F4', letterSpacing: -0.5 }}>G</Text>
              <Text style={S.socialLabel}>Google</Text>
            </TouchableOpacity>
            <TouchableOpacity style={S.socialBtn}>
              <Ionicons name="logo-apple" size={18} color="#1a1a1a" />
              <Text style={S.socialLabel}>Apple</Text>
            </TouchableOpacity>
          </View>

          {/* Login link */}
          <View style={S.loginRow}>
            <Text style={S.loginText}>Already have an account? </Text>
            <TouchableOpacity onPress={() => navigation.navigate('Login')}>
              <Text style={S.loginLink}>Sign In</Text>
            </TouchableOpacity>
          </View>

        </View>
      </ScrollView>

      <AlertModal
        visible={!!alertMsg}
        type="error"
        title="Registration Error"
        message={alertMsg ?? ''}
        onClose={() => setAlertMsg(null)}
      />
    </KeyboardAvoidingView>
  );
}

const S = StyleSheet.create({
  flex:         { flex: 1, backgroundColor: Colors.navy },
  scroll:       { flexGrow: 1 },

  // Hero
  hero:         { alignItems: 'center', paddingTop: 48, paddingBottom: 28, paddingHorizontal: Spacing[6] },
  brand:        { fontSize: 13, fontWeight: Typography.weights.bold, color: 'rgba(255,255,255,0.7)', letterSpacing: 3, marginTop: 12, textTransform: 'uppercase' },
  taglineBase:  { fontSize: Typography.sizes.xl, fontWeight: Typography.weights.bold, color: Colors.white, marginTop: 8 },
  taglineCyan:  { fontSize: Typography.sizes.xl, fontWeight: Typography.weights.bold, color: Colors.cyan, marginTop: 2 },

  // Card
  card:         { backgroundColor: Colors.white, borderTopLeftRadius: 32, borderTopRightRadius: 32, flex: 1, paddingHorizontal: Spacing[6], paddingTop: Spacing[6], paddingBottom: Spacing[8] },

  // Toggle
  toggle:       { flexDirection: 'row', backgroundColor: Colors.gray100, borderRadius: Radii.lg, padding: 4, marginBottom: Spacing[5] },
  toggleBtn:    { flex: 1, paddingVertical: 10, alignItems: 'center', borderRadius: Radii.md },
  toggleActive: { backgroundColor: Colors.white, ...Shadows.sm },
  toggleText:   { fontSize: Typography.sizes.sm, color: Colors.textSecondary, fontWeight: Typography.weights.medium },
  toggleTextActive: { color: Colors.primary, fontWeight: Typography.weights.semibold },

  // Error
  errBanner:    { backgroundColor: '#fef2f2', borderRadius: Radii.md, padding: Spacing[3], marginBottom: Spacing[3] },
  errText:      { color: Colors.error, fontSize: Typography.sizes.sm },

  row:          { flexDirection: 'row', gap: Spacing[2], marginBottom: Spacing[3] },
  half:         { flex: 1 },
  fieldGap:     { marginBottom: Spacing[3] },

  instructorHint: { fontSize: Typography.sizes.xs, color: Colors.textMuted, marginTop: 4 },

  // Terms
  termsRow:     { flexDirection: 'row', alignItems: 'flex-start', gap: 10, marginBottom: Spacing[5] },
  checkbox:     { width: 18, height: 18, borderRadius: 4, borderWidth: 1.5, borderColor: Colors.gray300, alignItems: 'center', justifyContent: 'center', marginTop: 2 },
  checkboxActive:{ backgroundColor: Colors.primary, borderColor: Colors.primary },
  termsText:    { flex: 1, fontSize: Typography.sizes.sm, color: Colors.textSecondary, lineHeight: 20 },
  termsLink:    { color: Colors.primary, fontWeight: Typography.weights.medium },

  // CTA
  cta:          { backgroundColor: Colors.primary, borderRadius: Radii.xl, paddingVertical: 16, alignItems: 'center', ...Shadows.md },
  ctaDisabled:  { opacity: 0.6 },
  ctaText:      { color: Colors.white, fontSize: Typography.sizes.md, fontWeight: Typography.weights.bold },

  // Social
  divider:      { flexDirection: 'row', alignItems: 'center', gap: Spacing[3], marginTop: Spacing[4], marginBottom: Spacing[4] },
  divLine:      { flex: 1, height: 1, backgroundColor: Colors.gray200 },
  divText:      { fontSize: Typography.sizes.xs, color: Colors.textMuted },
  socialRow:    { flexDirection: 'row', gap: Spacing[3] },
  socialBtn:    { flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8, borderWidth: 1, borderColor: Colors.gray200, borderRadius: Radii.lg, paddingVertical: 12 },
  socialLabel:  { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.medium, color: Colors.textPrimary },

  // Login link
  loginRow:     { flexDirection: 'row', justifyContent: 'center', marginTop: Spacing[5] },
  loginText:    { color: Colors.textSecondary, fontSize: Typography.sizes.sm },
  loginLink:    { color: Colors.primary, fontSize: Typography.sizes.sm, fontWeight: Typography.weights.bold },
});
