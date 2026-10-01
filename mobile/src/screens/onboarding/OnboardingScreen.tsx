import React, { useRef, useState } from 'react';
import {
  View, Text, TouchableOpacity, StyleSheet, Dimensions,
  ScrollView, NativeSyntheticEvent, NativeScrollEvent,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import { useAuthStore } from '@/stores/authStore';
import { Colors, Typography, Spacing, Radii } from '@/theme';
import type { RootStackParamList } from '@/types';

type Props = NativeStackScreenProps<RootStackParamList, 'Onboarding'>;
type IoniconsName = React.ComponentProps<typeof Ionicons>['name'];

const { width: SCREEN_W } = Dimensions.get('window');

interface Slide {
  icon:        IoniconsName;
  iconColor:   string;
  iconBg:      string;
  title:       string;
  subtitle:    string;
}

const SLIDES: Slide[] = [
  {
    icon:      'school-outline',
    iconColor: Colors.primary,
    iconBg:    '#e8ecff',
    title:     'Welcome to MAPELEAD',
    subtitle:  'Discover expert-led courses designed to help you grow professionally and academically.',
  },
  {
    icon:      'play-circle-outline',
    iconColor: '#059669',
    iconBg:    '#d1fae5',
    title:     'Learn at Your Own Pace',
    subtitle:  'Access video lessons, interactive quizzes, and assignments whenever it suits you.',
  },
  {
    icon:      'trophy-outline',
    iconColor: '#d97706',
    iconBg:    '#fef3c7',
    title:     'Earn Certificates',
    subtitle:  'Complete courses, track your streak, and earn recognised certificates to showcase your skills.',
  },
];

export default function OnboardingScreen({ navigation }: Props) {
  const { markOnboardingDone } = useAuthStore();
  const scrollRef = useRef<ScrollView>(null);
  const [activeIdx, setActiveIdx] = useState(0);

  const handleScroll = (e: NativeSyntheticEvent<NativeScrollEvent>) => {
    const idx = Math.round(e.nativeEvent.contentOffset.x / SCREEN_W);
    setActiveIdx(idx);
  };

  const goTo = (idx: number) => {
    scrollRef.current?.scrollTo({ x: idx * SCREEN_W, animated: true });
    setActiveIdx(idx);
  };

  const finish = async () => {
    await markOnboardingDone();
    navigation.replace('Main');
  };

  const isLast = activeIdx === SLIDES.length - 1;

  return (
    <View style={S.screen}>

      {/* Skip */}
      <TouchableOpacity style={S.skip} onPress={finish}>
        <Text style={S.skipText}>Skip</Text>
      </TouchableOpacity>

      {/* Slides */}
      <ScrollView
        ref={scrollRef}
        horizontal
        pagingEnabled
        showsHorizontalScrollIndicator={false}
        onMomentumScrollEnd={handleScroll}
        scrollEventThrottle={16}
        style={{ flex: 1 }}
      >
        {SLIDES.map((slide, i) => (
          <View key={i} style={S.slide}>
            <View style={[S.iconWrap, { backgroundColor: slide.iconBg }]}>
              <Ionicons name={slide.icon} size={64} color={slide.iconColor} />
            </View>
            <Text style={S.title}>{slide.title}</Text>
            <Text style={S.subtitle}>{slide.subtitle}</Text>
          </View>
        ))}
      </ScrollView>

      {/* Dots */}
      <View style={S.dots}>
        {SLIDES.map((_, i) => (
          <TouchableOpacity key={i} onPress={() => goTo(i)}>
            <View style={[S.dot, i === activeIdx && S.dotActive]} />
          </TouchableOpacity>
        ))}
      </View>

      {/* CTA */}
      <View style={S.footer}>
        {!isLast ? (
          <View style={S.footerRow}>
            <TouchableOpacity style={S.prevBtn} onPress={() => activeIdx > 0 && goTo(activeIdx - 1)}>
              <Ionicons name="arrow-back" size={20} color={Colors.primary} />
            </TouchableOpacity>
            <TouchableOpacity style={S.nextBtn} onPress={() => goTo(activeIdx + 1)}>
              <Text style={S.nextBtnText}>Next</Text>
              <Ionicons name="arrow-forward" size={18} color={Colors.white} style={{ marginLeft: 6 }} />
            </TouchableOpacity>
          </View>
        ) : (
          <TouchableOpacity style={S.startBtn} onPress={finish} activeOpacity={0.85}>
            <Ionicons name="rocket-outline" size={20} color={Colors.white} style={{ marginRight: 8 }} />
            <Text style={S.startBtnText}>Get Started</Text>
          </TouchableOpacity>
        )}
      </View>

    </View>
  );
}

const S = StyleSheet.create({
  screen:    { flex: 1, backgroundColor: Colors.background },

  skip:      { position: 'absolute', top: 56, right: Spacing[5], zIndex: 10 },
  skipText:  { fontSize: Typography.sizes.sm, color: Colors.textMuted, fontWeight: Typography.weights.medium },

  slide:     { width: SCREEN_W, flex: 1, alignItems: 'center', justifyContent: 'center', paddingHorizontal: Spacing[8], paddingTop: 80 },
  iconWrap:  { width: 140, height: 140, borderRadius: 70, alignItems: 'center', justifyContent: 'center', marginBottom: Spacing[8] },
  title:     { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.textPrimary, textAlign: 'center', marginBottom: Spacing[4] },
  subtitle:  { fontSize: Typography.sizes.base, color: Colors.textSecondary, textAlign: 'center', lineHeight: 26 },

  dots:      { flexDirection: 'row', justifyContent: 'center', gap: Spacing[2], marginBottom: Spacing[6] },
  dot:       { width: 8, height: 8, borderRadius: 4, backgroundColor: Colors.gray200 },
  dotActive: { width: 24, backgroundColor: Colors.primary },

  footer:    { paddingHorizontal: Spacing[6], paddingBottom: 48 },
  footerRow: { flexDirection: 'row', alignItems: 'center', gap: Spacing[3] },
  prevBtn:   { width: 52, height: 52, borderRadius: 26, borderWidth: 1.5, borderColor: Colors.primary, alignItems: 'center', justifyContent: 'center' },
  nextBtn:   { flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', backgroundColor: Colors.primary, borderRadius: Radii.xl, paddingVertical: 16 },
  nextBtnText: { fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold, color: Colors.white },
  startBtn:  { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', backgroundColor: Colors.primary, borderRadius: Radii.xl, paddingVertical: 16 },
  startBtnText: { fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold, color: Colors.white },
});
