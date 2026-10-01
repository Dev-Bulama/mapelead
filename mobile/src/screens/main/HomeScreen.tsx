import React, { useState } from 'react';
import {
  View, Text, ScrollView, TouchableOpacity,
  StyleSheet, FlatList, Image, ActivityIndicator,
  TextInput, Dimensions, Alert,
} from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import { coursesApi } from '@/api/courses';
import { enrollmentsApi } from '@/api/enrollments';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { authApi } from '@/api/auth';
import { useAuthStore, selectUser } from '@/stores/authStore';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { RootStackParamList, Course, Enrollment, CourseCategory } from '@/types';

type Nav = NativeStackNavigationProp<RootStackParamList>;
const { width: SCREEN_W } = Dimensions.get('window');

// ── Progress Ring (pure-RN arc via border quadrants) ──────────────────────────
function ProgressRing({ progress = 0, size = 110 }: { progress: number; size?: number }) {
  const strokeWidth = 12;
  const half = size / 2;
  const innerSize = size - strokeWidth * 2;

  // Quadrant borders — each border = 90° of arc (corner arcs with borderRadius)
  const angle = progress * 360;
  const topColor    = angle > 0   ? Colors.cyan : 'transparent';
  const rightColor  = angle > 90  ? Colors.cyan : 'transparent';
  const bottomColor = angle > 180 ? Colors.cyan : 'transparent';
  const leftColor   = angle > 270 ? Colors.cyan : 'transparent';

  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      {/* Track */}
      <View style={{ position: 'absolute', width: size, height: size, borderRadius: half, borderWidth: strokeWidth, borderColor: 'rgba(255,255,255,0.15)' }} />
      {/* Arc (quadrant-level) */}
      <View style={{
        position: 'absolute', width: size, height: size, borderRadius: half,
        borderWidth: strokeWidth,
        borderTopColor: topColor,
        borderRightColor: rightColor,
        borderBottomColor: bottomColor,
        borderLeftColor: leftColor,
        transform: [{ rotate: '-90deg' }],
      }} />
      {/* Center */}
      <View style={{ width: innerSize, height: innerSize, borderRadius: innerSize / 2, alignItems: 'center', justifyContent: 'center' }}>
        <Text style={{ fontSize: 22, fontWeight: '800', color: Colors.white }}>{Math.round(progress * 100)}%</Text>
        <Text style={{ fontSize: 9, color: 'rgba(255,255,255,0.6)', marginTop: 2 }}>Overall</Text>
      </View>
    </View>
  );
}

// ── Header ─────────────────────────────────────────────────────────────────────
function HomeHeader({ userName, notifCount, onBell }: { userName: string; notifCount: number; onBell: () => void }) {
  return (
    <View style={H.wrap}>
      <View style={H.brandRow}>
        <View style={H.logoBox}><Text style={H.logoM}>M</Text></View>
        <View>
          <Text style={H.brandName}>MAPELEAD LIMITED</Text>
          <Text style={H.brandSub}>Learning Platform</Text>
        </View>
      </View>
      <View style={H.actions}>
        <TouchableOpacity style={H.bellWrap} onPress={onBell}>
          <Ionicons name={notifCount > 0 ? 'notifications' : 'notifications-outline'} size={22} color={Colors.white} />
          {notifCount > 0 && (
            <View style={H.badge}><Text style={H.badgeText}>{notifCount > 9 ? '9+' : notifCount}</Text></View>
          )}
        </TouchableOpacity>
        <View style={H.avatar}><Ionicons name="person-outline" size={18} color={Colors.white} /></View>
      </View>
    </View>
  );
}
const H = StyleSheet.create({
  wrap:      { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingHorizontal: Spacing[4], paddingVertical: 14, backgroundColor: Colors.white, borderBottomWidth: 1, borderBottomColor: Colors.gray100 },
  brandRow:  { flexDirection: 'row', alignItems: 'center', gap: 10 },
  logoBox:   { width: 32, height: 32, borderRadius: 8, backgroundColor: Colors.navy, alignItems: 'center', justifyContent: 'center' },
  logoM:     { fontSize: 18, fontWeight: '900', color: Colors.white },
  brandName: { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.bold, color: Colors.navy, letterSpacing: 0.5 },
  brandSub:  { fontSize: Typography.sizes.xs, color: Colors.textMuted },
  actions:   { flexDirection: 'row', alignItems: 'center', gap: Spacing[3] },
  bellWrap:  { position: 'relative' },
  bellIcon:  { fontSize: 20 },
  badge:     { position: 'absolute', top: -4, right: -4, backgroundColor: Colors.error, borderRadius: 8, minWidth: 16, height: 16, alignItems: 'center', justifyContent: 'center', paddingHorizontal: 3 },
  badgeText: { fontSize: 9, color: Colors.white, fontWeight: Typography.weights.bold },
  avatar:    { width: 34, height: 34, borderRadius: 17, backgroundColor: Colors.gray100, alignItems: 'center', justifyContent: 'center' },
});

// ── Progress Hero Card ─────────────────────────────────────────────────────────
function ProgressCard({ firstName, overallProgress, inProgressCount, certificatesCount, streakDays }: {
  firstName: string; overallProgress: number; inProgressCount: number; certificatesCount: number; streakDays: number;
}) {
  const hour = new Date().getHours();
  const greeting = hour < 12 ? 'Good morning' : hour < 17 ? 'Good afternoon' : 'Good evening';

  return (
    <View style={PC.card}>
      <View style={PC.inner}>
        {/* Left: greeting + stats */}
        <View style={PC.left}>
          <Text style={PC.greeting}>{greeting},</Text>
          <Text style={PC.name}>{firstName} 👋</Text>
          <Text style={PC.sub}>Keep up the great work!</Text>

          <View style={PC.statsRow}>
            <View style={PC.statItem}>
              <Text style={PC.statNum}>{inProgressCount}</Text>
              <Text style={PC.statLabel}>In Progress</Text>
            </View>
            <View style={PC.statDivider} />
            <View style={PC.statItem}>
              <Text style={PC.statNum}>{certificatesCount}</Text>
              <Text style={PC.statLabel}>Certificates</Text>
            </View>
            <View style={PC.statDivider} />
            <View style={PC.statItem}>
              <Text style={PC.statNum}>{streakDays}</Text>
              <Text style={PC.statLabel}>Day Streak</Text>
            </View>
          </View>
        </View>

        {/* Right: ring */}
        <ProgressRing progress={overallProgress} size={110} />
      </View>

      {/* Carousel dots (decorative) */}
      <View style={PC.dots}>
        {[0, 1, 2, 3].map(i => (
          <View key={i} style={[PC.dot, i === 0 && PC.dotActive]} />
        ))}
      </View>
    </View>
  );
}
const PC = StyleSheet.create({
  card:       { marginHorizontal: Spacing[4], marginTop: Spacing[4], borderRadius: Radii['2xl'], backgroundColor: Colors.navy, padding: Spacing[5], ...Shadows.lg },
  inner:      { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  left:       { flex: 1, paddingRight: Spacing[3] },
  greeting:   { fontSize: Typography.sizes.sm, color: 'rgba(255,255,255,0.7)' },
  name:       { fontSize: Typography.sizes.xl, fontWeight: Typography.weights.bold, color: Colors.white, marginTop: 2 },
  sub:        { fontSize: Typography.sizes.xs, color: 'rgba(255,255,255,0.6)', marginTop: 2, marginBottom: Spacing[4] },
  statsRow:   { flexDirection: 'row', alignItems: 'center', gap: 12 },
  statItem:   { alignItems: 'center' },
  statNum:    { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.extrabold, color: Colors.cyan },
  statLabel:  { fontSize: 9, color: 'rgba(255,255,255,0.6)', marginTop: 2, textAlign: 'center' },
  statDivider:{ width: 1, height: 28, backgroundColor: 'rgba(255,255,255,0.15)' },
  dots:       { flexDirection: 'row', justifyContent: 'center', gap: 6, marginTop: Spacing[4] },
  dot:        { width: 6, height: 6, borderRadius: 3, backgroundColor: 'rgba(255,255,255,0.25)' },
  dotActive:  { backgroundColor: Colors.cyan, width: 18 },
});

// ── Course Card ─────────────────────────────────────────────────────────────────
function CourseCard({ course, onPress }: { course: Course; onPress: () => void }) {
  return (
    <TouchableOpacity style={CC.card} onPress={onPress} activeOpacity={0.85}>
      {course.thumbnail_url ? (
        <Image source={{ uri: course.thumbnail_url }} style={CC.thumb} />
      ) : (
        <View style={[CC.thumb, { backgroundColor: Colors.gray200, alignItems: 'center', justifyContent: 'center' }]}>
          <Ionicons name="book-outline" size={28} color={Colors.gray400} />
        </View>
      )}
      <View style={CC.info}>
        <Text style={CC.category} numberOfLines={1}>{course.category?.name ?? 'General'}</Text>
        <Text style={CC.title} numberOfLines={2}>{course.title}</Text>
        <View style={CC.footer}>
          <Text style={CC.price}>{course.is_free ? 'Free' : `₦${course.price.online.toLocaleString()}`}</Text>
          {course.level && <View style={CC.levelBadge}><Text style={CC.levelText}>{course.level}</Text></View>}
        </View>
      </View>
    </TouchableOpacity>
  );
}
const CC = StyleSheet.create({
  card:       { width: 175, backgroundColor: Colors.white, borderRadius: Radii.xl, marginRight: Spacing[3], ...Shadows.sm, overflow: 'hidden' },
  thumb:      { width: '100%', height: 100 },
  info:       { padding: Spacing[3] },
  category:   { fontSize: 10, color: Colors.primary, fontWeight: Typography.weights.semibold, textTransform: 'uppercase', letterSpacing: 0.5, marginBottom: 3 },
  title:      { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, lineHeight: 18, marginBottom: 6 },
  footer:     { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  price:      { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.bold, color: Colors.primary },
  levelBadge: { backgroundColor: Colors.gray100, borderRadius: 4, paddingHorizontal: 5, paddingVertical: 2 },
  levelText:  { fontSize: 9, color: Colors.textSecondary, textTransform: 'capitalize' },
});

// ── Category Chip ────────────────────────────────────────────────────────────
function CategoryChip({ cat, onPress }: { cat: CourseCategory; onPress: () => void }) {
  return (
    <TouchableOpacity style={Cat.chip} onPress={onPress} activeOpacity={0.75}>
      <View style={Cat.iconWrap}>
        <Ionicons name="grid-outline" size={22} color={Colors.primary} />
      </View>
      <Text style={Cat.label} numberOfLines={1}>{cat.name}</Text>
    </TouchableOpacity>
  );
}
const Cat = StyleSheet.create({
  chip:     { width: 78, alignItems: 'center', marginRight: Spacing[3] },
  iconWrap: { width: 56, height: 56, borderRadius: 16, backgroundColor: Colors.white, alignItems: 'center', justifyContent: 'center', ...Shadows.sm, marginBottom: 6 },
  label:    { fontSize: Typography.sizes.xs, color: Colors.textSecondary, textAlign: 'center', fontWeight: Typography.weights.medium },
});

// ── Main Screen ────────────────────────────────────────────────────────────────
export default function HomeScreen() {
  const navigation  = useNavigation<Nav>();
  const user        = useAuthStore(selectUser);
  const [search, setSearch]     = useState('');
  const [resending, setResending] = useState(false);
  const isInstructor = user?.roles?.includes('instructor') ?? false;

  const handleResendVerification = async () => {
    setResending(true);
    try {
      await authApi.resendVerification();
      Alert.alert('Email sent', 'A verification link has been sent to your email address.');
    } catch {
      Alert.alert('Error', 'Could not send verification email. Please try again.');
    } finally {
      setResending(false);
    }
  };

  const { data: unreadData } = useQuery({
    queryKey:  ['notifications', 'unread-count'],
    queryFn:   () => apiClient.get<{ success: boolean; data: { count: number } }>(API.NOTIFICATIONS_UNREAD).then(r => r.data.data),
    staleTime: 30_000,
  });

  const { data: featured, isLoading: featLoading } = useQuery({
    queryKey: ['courses', 'featured'],
    queryFn:  () => coursesApi.featured().then(r => r.data.data),
  });

  const { data: categories } = useQuery({
    queryKey: ['categories'],
    queryFn:  () => coursesApi.categories().then(r => r.data.data),
  });

  const { data: enrollments } = useQuery({
    queryKey: ['enrollments'],
    queryFn:  () => enrollmentsApi.myCourses().then(r => r.data.data),
  });

  const { data: streakData } = useQuery({
    queryKey: ['streak'],
    queryFn:  () => apiClient.get<{ success: boolean; data: { current_streak: number } }>(API.STREAK).then(r => r.data.data),
    staleTime: 60_000,
  });

  const inProgress = (enrollments as Enrollment[] | undefined)?.filter(e => e.status === 'active' && e.progress_percent < 100) ?? [];
  const certs      = (enrollments as Enrollment[] | undefined)?.filter(e => e.status === 'completed') ?? [];
  const avgProgress = inProgress.length > 0
    ? inProgress.reduce((acc, e) => acc + e.progress_percent, 0) / inProgress.length / 100
    : 0;

  const handleSearchSubmit = () => {
    if (search.trim()) {
      // Navigate to the Explore tab inside the Main tab stack
      navigation.navigate('Main', {
        screen: 'Main',
        params: { screen: 'Explore', params: { query: search.trim() } },
      } as any);
      setSearch('');
    }
  };

  return (
    <View style={S.screen}>
      <HomeHeader
        userName={user?.first_name ?? 'Learner'}
        notifCount={unreadData?.count ?? 0}
        onBell={() => navigation.navigate('Notifications')}
      />

      <ScrollView contentContainerStyle={S.scroll} showsVerticalScrollIndicator={false}>

        {/* Email verification banner */}
        {user && !user.email_verified && (
          <View style={S.verifyBanner}>
            <Ionicons name="warning-outline" size={18} color="#92400e" style={{ marginRight: 8, flexShrink: 0 }} />
            <Text style={S.verifyText}>Please verify your email address to unlock all features.</Text>
            <TouchableOpacity onPress={handleResendVerification} disabled={resending} style={{ marginLeft: 8 }}>
              <Text style={S.verifyResend}>{resending ? 'Sending…' : 'Resend'}</Text>
            </TouchableOpacity>
          </View>
        )}

        {/* Search bar */}
        <View style={S.searchWrap}>
          <Ionicons name="search-outline" size={16} color={Colors.gray400} style={{ marginRight: Spacing[2] }} />
          <TextInput
            style={S.searchInput}
            value={search} onChangeText={setSearch}
            placeholder="Search courses, topics…"
            placeholderTextColor={Colors.gray400}
            onSubmitEditing={handleSearchSubmit}
            returnKeyType="search"
          />
        </View>

        {/* Progress hero */}
        <ProgressCard
          firstName={user?.first_name ?? 'Learner'}
          overallProgress={avgProgress}
          inProgressCount={inProgress.length}
          certificatesCount={certs.length}
          streakDays={streakData?.current_streak ?? 0}
        />

        {/* Instructor shortcut */}
        {isInstructor && (
          <TouchableOpacity
            style={S.instructorBanner}
            onPress={() => navigation.navigate('InstructorDashboard')}
            activeOpacity={0.8}
          >
            <Ionicons name="school-outline" size={22} color={Colors.white} />
            <View style={{ flex: 1 }}>
              <Text style={S.instructorBannerTitle}>Instructor Panel</Text>
              <Text style={S.instructorBannerSub}>View your courses and student activity</Text>
            </View>
            <Ionicons name="chevron-forward" size={20} color="rgba(255,255,255,0.7)" />
          </TouchableOpacity>
        )}

        {/* Continue Learning */}
        {inProgress.length > 0 && (
          <View style={S.section}>
            <View style={S.sectionHeader}>
              <Text style={S.sectionTitle}>Continue Learning</Text>
              <TouchableOpacity onPress={() => navigation.navigate('Main', { screen: 'MyLearning' } as any)}>
                <Text style={S.seeAll}>See all</Text>
              </TouchableOpacity>
            </View>
            {inProgress.slice(0, 2).map((e: Enrollment) => (
              <TouchableOpacity
                key={e.id}
                style={S.continueCard}
                onPress={() => navigation.navigate('CourseDetail', { slug: e.course!.slug })}
                activeOpacity={0.85}
              >
                <View style={S.continueThumbnail}>
                  {e.course?.thumbnail_url
                    ? <Image source={{ uri: e.course.thumbnail_url }} style={{ width: '100%', height: '100%' }} />
                    : <Ionicons name="book-outline" size={24} color={Colors.gray400} />}
                </View>
                <View style={S.continueInfo}>
                  <Text style={S.continueTitle} numberOfLines={2}>{e.course?.title}</Text>
                  <View style={S.progressBar}>
                    <View style={[S.progressFill, { width: `${e.progress_percent}%` as any }]} />
                  </View>
                  <Text style={S.progressPct}>{Math.round(e.progress_percent)}% complete</Text>
                </View>
              </TouchableOpacity>
            ))}
          </View>
        )}

        {/* Popular Courses */}
        <View style={S.section}>
          <View style={S.sectionHeader}>
            <Text style={S.sectionTitle}>Popular Courses</Text>
            <TouchableOpacity onPress={() => navigation.navigate('Main', { screen: 'Explore' } as any)}>
              <Text style={S.seeAll}>See all</Text>
            </TouchableOpacity>
          </View>
          {featLoading ? (
            <ActivityIndicator color={Colors.primary} style={{ marginTop: Spacing[4] }} />
          ) : (
            <FlatList
              data={featured ?? []}
              keyExtractor={item => String(item.id)}
              horizontal showsHorizontalScrollIndicator={false}
              contentContainerStyle={{ paddingBottom: 4 }}
              renderItem={({ item }) => (
                <CourseCard course={item} onPress={() => navigation.navigate('CourseDetail', { slug: item.slug })} />
              )}
            />
          )}
        </View>

        {/* Categories */}
        {(categories ?? []).length > 0 && (
          <View style={S.section}>
            <View style={S.sectionHeader}>
              <Text style={S.sectionTitle}>Browse Categories</Text>
            </View>
            <FlatList
              data={categories ?? []}
              keyExtractor={item => String(item.id)}
              horizontal showsHorizontalScrollIndicator={false}
              renderItem={({ item }) => (
                <CategoryChip
                  cat={item}
                  onPress={() => navigation.navigate('Main', { screen: 'Explore', params: { query: item.slug } } as any)}
                />
              )}
            />
          </View>
        )}

        <View style={{ height: Spacing[6] }} />
      </ScrollView>
    </View>
  );
}

const S = StyleSheet.create({
  screen:        { flex: 1, backgroundColor: Colors.surface },
  scroll:        { paddingBottom: Spacing[10] },

  // Email verification banner
  verifyBanner:  { flexDirection: 'row', alignItems: 'center', backgroundColor: '#fef3c7', marginHorizontal: Spacing[4], marginTop: Spacing[3], borderRadius: Radii.lg, padding: Spacing[3], borderWidth: 1, borderColor: '#fde68a' },
  verifyText:    { flex: 1, fontSize: Typography.sizes.xs, color: '#92400e', lineHeight: 16 },
  verifyResend:  { fontSize: Typography.sizes.xs, color: '#92400e', fontWeight: Typography.weights.bold, textDecorationLine: 'underline' },

  // Search
  searchWrap:    { flexDirection: 'row', alignItems: 'center', marginHorizontal: Spacing[4], marginTop: Spacing[4], backgroundColor: Colors.white, borderRadius: Radii.xl, paddingHorizontal: Spacing[4], ...Shadows.sm },
  searchInput:   { flex: 1, paddingVertical: 12, fontSize: Typography.sizes.base, color: Colors.textPrimary },

  // Sections
  section:       { marginTop: Spacing[6], paddingHorizontal: Spacing[4] },
  sectionHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: Spacing[3] },
  sectionTitle:  { fontSize: Typography.sizes.md, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  seeAll:        { fontSize: Typography.sizes.sm, color: Colors.primary, fontWeight: Typography.weights.medium },

  // Instructor banner
  instructorBanner:      { flexDirection: 'row', alignItems: 'center', marginHorizontal: Spacing[4], marginTop: Spacing[4], backgroundColor: Colors.primary, borderRadius: Radii.xl, padding: Spacing[4], gap: Spacing[3], ...Shadows.md },
  instructorBannerTitle: { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.bold, color: Colors.white },
  instructorBannerSub:   { fontSize: Typography.sizes.xs, color: 'rgba(255,255,255,0.75)', marginTop: 2 },

  // Continue learning card
  continueCard:  { flexDirection: 'row', backgroundColor: Colors.white, borderRadius: Radii.xl, padding: Spacing[3], marginBottom: Spacing[3], gap: Spacing[3], ...Shadows.sm },
  continueThumbnail: { width: 70, height: 70, borderRadius: Radii.lg, backgroundColor: Colors.gray100, alignItems: 'center', justifyContent: 'center', overflow: 'hidden' },
  continueInfo:  { flex: 1, justifyContent: 'space-between' },
  continueTitle: { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, lineHeight: 18 },
  progressBar:   { height: 6, backgroundColor: Colors.gray200, borderRadius: Radii.full, overflow: 'hidden', marginTop: 8 },
  progressFill:  { height: '100%', backgroundColor: Colors.cyan, borderRadius: Radii.full },
  progressPct:   { fontSize: Typography.sizes.xs, color: Colors.textSecondary, marginTop: 4 },
});
