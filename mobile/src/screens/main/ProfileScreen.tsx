import React from 'react';
import {
  View, Text, TouchableOpacity, StyleSheet, ScrollView, Image, Alert,
} from 'react-native';
import { useAuthStore, selectUser } from '@/stores/authStore';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';

export default function ProfileScreen() {
  const user    = useAuthStore(selectUser);
  const logout  = useAuthStore((s) => s.logout);

  const handleLogout = () => {
    Alert.alert('Sign Out', 'Are you sure you want to sign out?', [
      { text: 'Cancel', style: 'cancel' },
      { text: 'Sign Out', style: 'destructive', onPress: logout },
    ]);
  };

  return (
    <ScrollView style={styles.screen}>
      {/* Header */}
      <View style={styles.header}>
        {user?.avatar_url ? (
          <Image source={{ uri: user.avatar_url }} style={styles.avatar} />
        ) : (
          <View style={styles.avatarFallback}>
            <Text style={styles.avatarInitials}>
              {(user?.first_name?.[0] ?? '') + (user?.last_name?.[0] ?? '')}
            </Text>
          </View>
        )}
        <Text style={styles.name}>{user?.full_name ?? '—'}</Text>
        <Text style={styles.email}>{user?.email}</Text>
        {user?.admission_number && (
          <View style={styles.admissionBadge}>
            <Text style={styles.admissionText}>ID: {user.admission_number}</Text>
          </View>
        )}
      </View>

      {/* Stats */}
      <View style={styles.statsRow}>
        <StatTile label="Courses" value={String(user?.enrollments_count ?? 0)} />
        <StatTile label="Completed" value={String(user?.completed_courses ?? 0)} />
      </View>

      {/* Menu */}
      <View style={styles.menu}>
        <MenuItem icon="🔔" label="Notifications" />
        <MenuItem icon="🔒" label="Security" />
        <MenuItem icon="❓" label="Help & Support" />
      </View>

      {/* Sign out */}
      <TouchableOpacity style={styles.logoutBtn} onPress={handleLogout}>
        <Text style={styles.logoutText}>Sign Out</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

function StatTile({ label, value }: { label: string; value: string }) {
  return (
    <View style={styles.stat}>
      <Text style={styles.statValue}>{value}</Text>
      <Text style={styles.statLabel}>{label}</Text>
    </View>
  );
}

function MenuItem({ icon, label }: { icon: string; label: string }) {
  return (
    <TouchableOpacity style={styles.menuItem}>
      <Text style={styles.menuIcon}>{icon}</Text>
      <Text style={styles.menuLabel}>{label}</Text>
      <Text style={styles.menuChevron}>›</Text>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  screen:         { flex: 1, backgroundColor: Colors.surface },
  header:         { backgroundColor: Colors.primary, alignItems: 'center', paddingTop: Spacing[12], paddingBottom: Spacing[8] },
  avatar:         { width: 88, height: 88, borderRadius: Radii.full, borderWidth: 3, borderColor: Colors.white },
  avatarFallback: { width: 88, height: 88, borderRadius: Radii.full, backgroundColor: 'rgba(255,255,255,0.25)', justifyContent: 'center', alignItems: 'center', borderWidth: 3, borderColor: Colors.white },
  avatarInitials: { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.white },
  name:           { fontSize: Typography.sizes.xl, fontWeight: Typography.weights.bold, color: Colors.white, marginTop: Spacing[3] },
  email:          { fontSize: Typography.sizes.sm, color: 'rgba(255,255,255,0.8)', marginTop: Spacing[1] },
  admissionBadge: { backgroundColor: 'rgba(255,255,255,0.2)', borderRadius: Radii.full, paddingHorizontal: Spacing[3], paddingVertical: Spacing[1], marginTop: Spacing[2] },
  admissionText:  { color: Colors.white, fontSize: Typography.sizes.xs, fontWeight: Typography.weights.medium },
  statsRow:       { flexDirection: 'row', margin: Spacing[4], gap: Spacing[3] },
  stat:           { flex: 1, backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], alignItems: 'center', ...Shadows.sm },
  statValue:      { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.primary },
  statLabel:      { fontSize: Typography.sizes.sm, color: Colors.textSecondary, marginTop: Spacing[1] },
  menu:           { backgroundColor: Colors.white, margin: Spacing[4], borderRadius: Radii.xl, overflow: 'hidden', ...Shadows.sm },
  menuItem:       { flexDirection: 'row', alignItems: 'center', padding: Spacing[4], borderBottomWidth: 1, borderBottomColor: Colors.border },
  menuIcon:       { fontSize: 20, marginRight: Spacing[3] },
  menuLabel:      { flex: 1, fontSize: Typography.sizes.base, color: Colors.textPrimary },
  menuChevron:    { fontSize: Typography.sizes.xl, color: Colors.gray400 },
  logoutBtn:      { margin: Spacing[4], backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], alignItems: 'center', borderWidth: 1, borderColor: Colors.error, ...Shadows.sm },
  logoutText:     { color: Colors.error, fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold },
});
