import React from 'react';
import {
  View, Text, ScrollView, StyleSheet,
  ActivityIndicator, TouchableOpacity,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { useQuery } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { ApiResponse, RootStackParamList } from '@/types';

type Props = NativeStackScreenProps<RootStackParamList, 'LearningStats'>;

interface StreakDay {
  date: string;
  minutes_studied: number;
}

interface StreakResponse {
  current_streak: number;
  streaks: StreakDay[];
}

interface ProfileStats {
  enrollments_count: number;
  completed_courses: number;
  streak_days: number;
}

// ── Streak calendar (30-day grid) ─────────────────────────────────────────────

function StreakCalendar({ streaks }: { streaks: StreakDay[] }) {
  // Build a map from YYYY-MM-DD → minutes
  const map = new Map(streaks.map(s => [s.date, s.minutes_studied]));

  // Generate last 35 days (5 weeks × 7 columns) for a grid layout
  const days: Array<{ date: string; minutes: number }> = [];
  for (let i = 34; i >= 0; i--) {
    const d = new Date();
    d.setDate(d.getDate() - i);
    const key = d.toISOString().slice(0, 10);
    days.push({ date: key, minutes: map.get(key) ?? 0 });
  }

  const maxMin = Math.max(...days.map(d => d.minutes), 1);

  return (
    <View style={Cal.grid}>
      {days.map((day, idx) => {
        const intensity = day.minutes > 0 ? Math.max(0.25, day.minutes / maxMin) : 0;
        const bg = day.minutes > 0
          ? `rgba(0, 201, 228, ${intensity})`
          : Colors.gray100;
        const today = day.date === new Date().toISOString().slice(0, 10);
        return (
          <View key={idx} style={[Cal.cell, { backgroundColor: bg }, today && Cal.today]} />
        );
      })}
    </View>
  );
}

const Cal = StyleSheet.create({
  grid:  { flexDirection: 'row', flexWrap: 'wrap', gap: 4 },
  cell:  { width: 36, height: 36, borderRadius: 6 },
  today: { borderWidth: 2, borderColor: Colors.primary },
});

// ── Stat tile ─────────────────────────────────────────────────────────────────

type IoniconsName = React.ComponentProps<typeof Ionicons>['name'];

function StatTile({ icon, value, label, color = Colors.primary }: { icon: IoniconsName; value: string | number; label: string; color?: string }) {
  return (
    <View style={Tile.wrap}>
      <Ionicons name={icon} size={26} color={color} />
      <Text style={[Tile.value, { color }]}>{value}</Text>
      <Text style={Tile.label}>{label}</Text>
    </View>
  );
}
const Tile = StyleSheet.create({
  wrap:  { flex: 1, backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], alignItems: 'center', gap: 4, ...Shadows.sm },
  value: { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.extrabold },
  label: { fontSize: 10, color: Colors.textSecondary, textAlign: 'center' },
});

// ── Main screen ───────────────────────────────────────────────────────────────

export default function LearningStatsScreen({ navigation }: Props) {
  const { data: streakData, isLoading: streakLoading } = useQuery({
    queryKey: ['streak'],
    queryFn:  () => apiClient.get<ApiResponse<StreakResponse>>(API.STREAK).then(r => r.data.data),
  });

  const { data: stats, isLoading: statsLoading } = useQuery({
    queryKey: ['profile', 'stats'],
    queryFn:  () => apiClient.get<ApiResponse<ProfileStats>>(API.PROFILE_STATS).then(r => r.data.data),
  });

  const isLoading = streakLoading || statsLoading;

  const currentStreak = streakData?.current_streak ?? 0;
  const totalStudyDays = (streakData?.streaks ?? []).filter(s => s.minutes_studied > 0).length;
  const totalMinutes   = (streakData?.streaks ?? []).reduce((a, s) => a + s.minutes_studied, 0);
  const longestStreak  = computeLongest(streakData?.streaks ?? []);

  return (
    <View style={S.screen}>
      <View style={S.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={S.back}>‹ Back</Text>
        </TouchableOpacity>
        <Text style={S.title}>Learning Stats</Text>
        <View style={{ width: 50 }} />
      </View>

      {isLoading ? (
        <View style={S.center}><ActivityIndicator color={Colors.primary} size="large" /></View>
      ) : (
        <ScrollView contentContainerStyle={S.scroll} showsVerticalScrollIndicator={false}>

          {/* Streak hero */}
          <View style={S.streakHero}>
            <Text style={S.fireIcon}>🔥</Text>
            <Text style={S.streakCount}>{currentStreak}</Text>
            <Text style={S.streakLabel}>Day Streak</Text>
            {currentStreak > 0 && (
              <Text style={S.streakMsg}>
                {currentStreak >= 30 ? 'Incredible — 30 days!' : currentStreak >= 7 ? 'Amazing streak! Keep it up!' : 'You\'re on a roll!'}
              </Text>
            )}
          </View>

          {/* Overview stats */}
          <View style={S.section}>
            <Text style={S.sectionTitle}>Overview</Text>
            <View style={S.statsGrid}>
              <StatTile icon="book-outline" value={stats?.enrollments_count ?? 0} label="Enrolled" />
              <StatTile icon="checkmark-circle-outline" value={stats?.completed_courses ?? 0} label="Completed" color={Colors.success} />
            </View>
            <View style={[S.statsGrid, { marginTop: Spacing[3] }]}>
              <StatTile icon="calendar-outline" value={totalStudyDays} label="Study Days (30d)" color={Colors.cyan} />
              <StatTile icon="time-outline" value={`${totalMinutes}m`} label="Study Time (30d)" color={Colors.primary} />
            </View>
            <View style={[S.statsGrid, { marginTop: Spacing[3] }]}>
              <StatTile icon="trophy-outline" value={longestStreak} label="Longest Streak" color="#d97706" />
            </View>
          </View>

          {/* Streak calendar */}
          <View style={S.section}>
            <Text style={S.sectionTitle}>Activity (Last 35 Days)</Text>
            <View style={S.calendarCard}>
              <StreakCalendar streaks={streakData?.streaks ?? []} />
              <View style={S.legend}>
                <View style={[S.legendDot, { backgroundColor: Colors.gray100 }]} />
                <Text style={S.legendText}>No activity</Text>
                <View style={[S.legendDot, { backgroundColor: Colors.cyan }]} />
                <Text style={S.legendText}>Studied</Text>
              </View>
            </View>
          </View>

          <View style={{ height: Spacing[8] }} />
        </ScrollView>
      )}
    </View>
  );
}

function computeLongest(streaks: StreakDay[]): number {
  if (!streaks.length) return 0;
  const sorted = [...streaks].sort((a, b) => a.date.localeCompare(b.date));
  let max = 0, current = 0, prev: Date | null = null;
  for (const s of sorted) {
    if (s.minutes_studied > 0) {
      const d = new Date(s.date);
      if (prev && (d.getTime() - prev.getTime()) === 86_400_000) {
        current++;
      } else {
        current = 1;
      }
      if (current > max) max = current;
      prev = d;
    } else {
      prev = null;
    }
  }
  return max;
}

const S = StyleSheet.create({
  screen:       { flex: 1, backgroundColor: Colors.surface },
  center:       { flex: 1, justifyContent: 'center', alignItems: 'center' },
  header:       { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', backgroundColor: Colors.white, paddingHorizontal: Spacing[4], paddingTop: 56, paddingBottom: Spacing[3], borderBottomWidth: 1, borderBottomColor: Colors.gray100 },
  back:         { fontSize: Typography.sizes.base, color: Colors.primary, fontWeight: Typography.weights.medium, width: 50 },
  title:        { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  scroll:       { padding: Spacing[4] },
  streakHero:   { backgroundColor: Colors.navy, borderRadius: Radii['2xl'], padding: Spacing[6], alignItems: 'center', marginBottom: Spacing[4] },
  fireIcon:     { fontSize: 44, marginBottom: Spacing[2] },
  streakCount:  { fontSize: 64, fontWeight: Typography.weights.extrabold, color: Colors.white, lineHeight: 72 },
  streakLabel:  { fontSize: Typography.sizes.base, color: 'rgba(255,255,255,0.7)', marginTop: 4 },
  streakMsg:    { fontSize: Typography.sizes.sm, color: Colors.cyan, marginTop: Spacing[2], fontWeight: Typography.weights.medium },
  section:      { marginBottom: Spacing[5] },
  sectionTitle: { fontSize: Typography.sizes.md, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: Spacing[3] },
  statsGrid:    { flexDirection: 'row', gap: Spacing[3] },
  calendarCard: { backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[4], ...Shadows.sm },
  legend:       { flexDirection: 'row', alignItems: 'center', gap: 6, marginTop: Spacing[3], justifyContent: 'flex-end' },
  legendDot:    { width: 12, height: 12, borderRadius: 3 },
  legendText:   { fontSize: Typography.sizes.xs, color: Colors.textMuted },
});
