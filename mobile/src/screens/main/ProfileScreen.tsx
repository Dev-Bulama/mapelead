import React from 'react';
import {
  View, Text, TouchableOpacity, StyleSheet, ScrollView, Image, Alert,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { useQuery } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { useAuthStore, selectUser } from '@/stores/authStore';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { ApiResponse, RootStackParamList } from '@/types';

type Nav = NativeStackNavigationProp<RootStackParamList>;

export default function ProfileScreen() {
  const navigation = useNavigation<Nav>();
  const user       = useAuthStore(selectUser);
  const logout     = useAuthStore((s) => s.logout);

  const { data: stats } = useQuery({
    queryKey: ['profile', 'stats'],
    queryFn: () => apiClient.get<ApiResponse<{ enrollments_count: number; completed_courses: number; streak_days: number }>>(API.PROFILE_STATS).then(r => r.data.data),
    staleTime: 60_000,
  });

  const handleLogout = () => {
    Alert.alert('Sign Out', 'Are you sure you want to sign out?', [
      { text: 'Cancel', style: 'cancel' },
      { text: 'Sign Out', style: 'destructive', onPress: logout },
    ]);
  };

  const initials = (user?.first_name?.[0] ?? '') + (user?.last_name?.[0] ?? '');
  const isInstructor = user?.roles?.includes('instructor');

  return (
    <ScrollView style={S.screen} showsVerticalScrollIndicator={false}>

      {/* ── Hero header ── */}
      <View style={S.hero}>
        <TouchableOpacity onPress={() => navigation.navigate('ProfileEdit')} style={S.avatarWrap}>
          {user?.avatar_url ? (
            <Image source={{ uri: user.avatar_url }} style={S.avatar} />
          ) : (
            <View style={S.avatarFallback}>
              <Text style={S.initials}>{initials}</Text>
            </View>
          )}
          <View style={S.avatarEdit}>
            <Text style={{ fontSize: 11 }}>✏️</Text>
          </View>
        </TouchableOpacity>

        <Text style={S.name}>{user?.full_name ?? '—'}</Text>
        <Text style={S.email}>{user?.email}</Text>

        <View style={S.badgeRow}>
          {user?.admission_number && (
            <View style={S.admissionBadge}>
              <Text style={S.admissionText}>ID: {user.admission_number}</Text>
            </View>
          )}
          {isInstructor && (
            <View style={[S.admissionBadge, { backgroundColor: 'rgba(0,201,228,0.2)' }]}>
              <Text style={[S.admissionText, { color: Colors.cyan }]}>Instructor</Text>
            </View>
          )}
        </View>
      </View>

      {/* ── Stats ── */}
      <View style={S.statsRow}>
        <StatTile icon="📚" label="Enrolled" value={String(stats?.enrollments_count ?? user?.enrollments_count ?? 0)} />
        <StatTile icon="🎓" label="Completed" value={String(stats?.completed_courses ?? user?.completed_courses ?? 0)} />
        <StatTile icon="🔥" label="Day Streak" value={String(stats?.streak_days ?? 0)} />
      </View>

      {/* ── Menu ── */}
      {isInstructor && (
        <View style={S.section}>
          <Text style={S.sectionLabel}>Instructor</Text>
          <View style={S.menuCard}>
            <MenuItem icon="school-outline" label="Instructor Dashboard" onPress={() => navigation.navigate('InstructorDashboard')} last />
          </View>
        </View>
      )}

      <View style={S.section}>
        <Text style={S.sectionLabel}>Account</Text>
        <View style={S.menuCard}>
          <MenuItem icon="create-outline"        label="Edit Profile"  onPress={() => navigation.navigate('ProfileEdit')} />
          <MenuItem icon="lock-closed-outline"   label="Security"      onPress={() => navigation.navigate('Security')} />
          <MenuItem icon="notifications-outline" label="Notifications" onPress={() => navigation.navigate('Notifications')} last />
        </View>
      </View>

      <View style={S.section}>
        <Text style={S.sectionLabel}>Learning</Text>
        <View style={S.menuCard}>
          <MenuItem icon="stats-chart-outline"   label="Learning Stats"  onPress={() => navigation.navigate('LearningStats')} />
          <MenuItem icon="ribbon-outline"        label="My Certificates" onPress={() => navigation.navigate('Certificates')} />
          <MenuItem icon="bookmark-outline"      label="Bookmarks"       onPress={() => navigation.navigate('Bookmarks')} />
          <MenuItem icon="document-text-outline" label="My Notes"        onPress={() => navigation.navigate('Notes')} />
          <MenuItem icon="card-outline"          label="Payment History" onPress={() => navigation.navigate('PaymentHistory')} />
          <MenuItem icon="cloud-download-outline" label="Downloads"      onPress={() => {}} last />
        </View>
      </View>

      <View style={S.section}>
        <Text style={S.sectionLabel}>Support</Text>
        <View style={S.menuCard}>
          <MenuItem icon="help-circle-outline" label="Help & Support" onPress={() => {}} />
          <MenuItem icon="star-outline"        label="Rate the App"   onPress={() => {}} last />
        </View>
      </View>

      {/* ── Sign out ── */}
      <TouchableOpacity style={S.logoutBtn} onPress={handleLogout} activeOpacity={0.75}>
        <Text style={S.logoutText}>Sign Out</Text>
      </TouchableOpacity>

      <View style={{ height: Spacing[10] }} />
    </ScrollView>
  );
}

function StatTile({ icon, label, value }: { icon: string; label: string; value: string }) {
  return (
    <View style={S.stat}>
      <Text style={{ fontSize: 20 }}>{icon}</Text>
      <Text style={S.statValue}>{value}</Text>
      <Text style={S.statLabel}>{label}</Text>
    </View>
  );
}

type IoniconsName = React.ComponentProps<typeof Ionicons>['name'];

function MenuItem({ icon, label, onPress, last = false }: { icon: IoniconsName; label: string; onPress: () => void; last?: boolean }) {
  return (
    <TouchableOpacity
      style={[S.menuItem, last && S.menuItemLast]}
      onPress={onPress}
      activeOpacity={0.6}
    >
      <View style={S.menuIconWrap}>
        <Ionicons name={icon} size={18} color={Colors.primary} />
      </View>
      <Text style={S.menuLabel}>{label}</Text>
      <Ionicons name="chevron-forward" size={16} color={Colors.gray300} />
    </TouchableOpacity>
  );
}

const S = StyleSheet.create({
  screen:         { flex: 1, backgroundColor: Colors.surface },

  // Hero
  hero:           { backgroundColor: Colors.navy, alignItems: 'center', paddingTop: 56, paddingBottom: Spacing[6] },
  avatarWrap:     { position: 'relative', marginBottom: Spacing[3] },
  avatar:         { width: 90, height: 90, borderRadius: 45, borderWidth: 3, borderColor: Colors.white },
  avatarFallback: { width: 90, height: 90, borderRadius: 45, backgroundColor: 'rgba(255,255,255,0.2)', justifyContent: 'center', alignItems: 'center', borderWidth: 3, borderColor: Colors.white },
  initials:       { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.white },
  avatarEdit:     { position: 'absolute', bottom: 0, right: 0, width: 26, height: 26, borderRadius: 13, backgroundColor: Colors.white, alignItems: 'center', justifyContent: 'center', borderWidth: 1, borderColor: Colors.gray200 },
  name:           { fontSize: Typography.sizes.xl, fontWeight: Typography.weights.bold, color: Colors.white },
  email:          { fontSize: Typography.sizes.sm, color: 'rgba(255,255,255,0.7)', marginTop: 2 },
  badgeRow:       { flexDirection: 'row', gap: 8, marginTop: Spacing[2] },
  admissionBadge: { backgroundColor: 'rgba(255,255,255,0.15)', borderRadius: Radii.full, paddingHorizontal: Spacing[3], paddingVertical: 4 },
  admissionText:  { color: Colors.white, fontSize: Typography.sizes.xs, fontWeight: Typography.weights.medium },

  // Stats
  statsRow:       { flexDirection: 'row', margin: Spacing[4], gap: Spacing[3] },
  stat:           { flex: 1, backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], alignItems: 'center', gap: 4, ...Shadows.sm },
  statValue:      { fontSize: Typography.sizes.xl, fontWeight: Typography.weights.extrabold, color: Colors.primary },
  statLabel:      { fontSize: 10, color: Colors.textSecondary },

  // Sections
  section:        { paddingHorizontal: Spacing[4], marginBottom: Spacing[3] },
  sectionLabel:   { fontSize: Typography.sizes.xs, fontWeight: Typography.weights.semibold, color: Colors.textMuted, textTransform: 'uppercase', letterSpacing: 1, marginBottom: Spacing[2] },
  menuCard:       { backgroundColor: Colors.white, borderRadius: Radii.xl, overflow: 'hidden', ...Shadows.sm },
  menuItem:       { flexDirection: 'row', alignItems: 'center', paddingHorizontal: Spacing[4], paddingVertical: 14, borderBottomWidth: 1, borderBottomColor: Colors.gray100 },
  menuItemLast:   { borderBottomWidth: 0 },
  menuIconWrap:   { width: 30, alignItems: 'flex-start' },
  menuLabel:      { flex: 1, fontSize: Typography.sizes.base, color: Colors.textPrimary },

  // Logout
  logoutBtn:      { marginHorizontal: Spacing[4], marginTop: Spacing[2], backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], alignItems: 'center', borderWidth: 1.5, borderColor: Colors.error, ...Shadows.sm },
  logoutText:     { color: Colors.error, fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold },
});
