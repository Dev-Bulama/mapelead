import React, { useEffect, useState } from 'react';
import {
  View, Text, TouchableOpacity, Switch, StyleSheet,
  ScrollView, Alert, ActivityIndicator,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import Constants from 'expo-constants';
import { secureStorage } from '@/utils/secureStorage';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { RootStackParamList } from '@/types';

type Props = NativeStackScreenProps<RootStackParamList, 'AppSettings'>;
type IoniconsName = React.ComponentProps<typeof Ionicons>['name'];

const APP_VERSION = Constants.expoConfig?.version ?? '1.0.0';

function SettingsRow({
  icon, label, value, onValueChange, loading, last,
}: {
  icon: IoniconsName; label: string;
  value?: boolean; onValueChange?: (v: boolean) => void;
  loading?: boolean; last?: boolean;
}) {
  return (
    <View style={[R.row, !last && R.rowBorder]}>
      <View style={R.iconWrap}>
        <Ionicons name={icon} size={18} color={Colors.primary} />
      </View>
      <Text style={R.label}>{label}</Text>
      <View style={{ marginLeft: 'auto' }}>
        {loading ? (
          <ActivityIndicator size="small" color={Colors.primary} />
        ) : (
          <Switch
            value={value}
            onValueChange={onValueChange}
            trackColor={{ false: Colors.gray200, true: Colors.primary }}
            thumbColor={Colors.white}
          />
        )}
      </View>
    </View>
  );
}

function InfoRow({ icon, label, value, last }: { icon: IoniconsName; label: string; value: string; last?: boolean }) {
  return (
    <View style={[R.row, !last && R.rowBorder]}>
      <View style={R.iconWrap}>
        <Ionicons name={icon} size={18} color={Colors.textMuted} />
      </View>
      <Text style={R.label}>{label}</Text>
      <Text style={[R.label, { marginLeft: 'auto', color: Colors.textMuted }]}>{value}</Text>
    </View>
  );
}

export default function AppSettingsScreen({ navigation }: Props) {
  const [pushEnabled, setPushEnabled] = useState(true);
  const [pushLoading, setPushLoading] = useState(false);
  const [initialized, setInitialized] = useState(false);

  useEffect(() => {
    secureStorage.getPushEnabled().then((v) => {
      setPushEnabled(v);
      setInitialized(true);
    });
  }, []);

  const handlePushToggle = async (enabled: boolean) => {
    setPushLoading(true);
    try {
      if (!enabled) {
        await apiClient.delete(API.DEVICE_TOKEN).catch(() => {});
      }
      await secureStorage.setPushEnabled(enabled);
      setPushEnabled(enabled);
    } catch {
      Alert.alert('Error', 'Could not update notification setting. Please try again.');
    } finally {
      setPushLoading(false);
    }
  };

  if (!initialized) {
    return <View style={S.center}><ActivityIndicator color={Colors.primary} /></View>;
  }

  return (
    <View style={S.screen}>
      <View style={S.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={{ width: 44 }}>
          <Ionicons name="arrow-back" size={22} color={Colors.primary} />
        </TouchableOpacity>
        <Text style={S.headerTitle}>App Settings</Text>
        <View style={{ width: 44 }} />
      </View>

      <ScrollView contentContainerStyle={S.scroll} showsVerticalScrollIndicator={false}>

        <Text style={S.sectionLabel}>Notifications</Text>
        <View style={S.card}>
          <SettingsRow
            icon="notifications-outline"
            label="Push Notifications"
            value={pushEnabled}
            onValueChange={handlePushToggle}
            loading={pushLoading}
            last
          />
        </View>
        <Text style={S.sectionHint}>
          Turn off to stop receiving alerts about new lessons, grades, and announcements.
          Re-enabling will take effect the next time you open the app.
        </Text>

        <Text style={S.sectionLabel}>About</Text>
        <View style={S.card}>
          <InfoRow icon="phone-portrait-outline" label="App Version"  value={APP_VERSION} />
          <InfoRow icon="globe-outline"          label="Environment"  value="Production" />
          <InfoRow icon="server-outline"         label="API"          value="mapelead.org" last />
        </View>

        <Text style={S.sectionLabel}>Legal</Text>
        <View style={S.card}>
          <TouchableOpacity style={[R.row, R.rowBorder]}
            onPress={() => Alert.alert('Terms & Conditions', 'Visit mapelead.org/terms for the full text.')}
          >
            <View style={R.iconWrap}>
              <Ionicons name="document-text-outline" size={18} color={Colors.textMuted} />
            </View>
            <Text style={R.label}>Terms &amp; Conditions</Text>
            <Ionicons name="chevron-forward" size={16} color={Colors.gray300} style={{ marginLeft: 'auto' }} />
          </TouchableOpacity>
          <TouchableOpacity style={R.row}
            onPress={() => Alert.alert('Privacy Policy', 'Visit mapelead.org/privacy for the full text.')}
          >
            <View style={R.iconWrap}>
              <Ionicons name="shield-checkmark-outline" size={18} color={Colors.textMuted} />
            </View>
            <Text style={R.label}>Privacy Policy</Text>
            <Ionicons name="chevron-forward" size={16} color={Colors.gray300} style={{ marginLeft: 'auto' }} />
          </TouchableOpacity>
        </View>

        <View style={{ height: Spacing[8] }} />
      </ScrollView>
    </View>
  );
}

const R = StyleSheet.create({
  row:       { flexDirection: 'row', alignItems: 'center', paddingVertical: 14, paddingHorizontal: Spacing[4] },
  rowBorder: { borderBottomWidth: 1, borderBottomColor: Colors.gray100 },
  iconWrap:  { width: 32, height: 32, borderRadius: 8, backgroundColor: Colors.surface, alignItems: 'center', justifyContent: 'center', marginRight: Spacing[3] },
  label:     { fontSize: Typography.sizes.base, color: Colors.textPrimary },
});

const S = StyleSheet.create({
  screen:       { flex: 1, backgroundColor: Colors.surface },
  center:       { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header:       { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', backgroundColor: Colors.white, paddingHorizontal: Spacing[4], paddingTop: 56, paddingBottom: Spacing[3], borderBottomWidth: 1, borderBottomColor: Colors.gray100 },
  headerTitle:  { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  scroll:       { padding: Spacing[4] },
  sectionLabel: { fontSize: Typography.sizes.xs, fontWeight: Typography.weights.bold, color: Colors.textMuted, textTransform: 'uppercase', letterSpacing: 0.8, marginBottom: Spacing[2], marginTop: Spacing[2] },
  sectionHint:  { fontSize: Typography.sizes.xs, color: Colors.textMuted, marginTop: Spacing[2], marginBottom: Spacing[4], lineHeight: 18 },
  card:         { backgroundColor: Colors.white, borderRadius: Radii.xl, ...Shadows.sm, overflow: 'hidden' },
});
