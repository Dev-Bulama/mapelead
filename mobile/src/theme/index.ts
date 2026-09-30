// Brand palette — MAPELEAD official tokens
export const Colors = {
  // Primary brand
  primary:      '#1e3adb', // brand blue
  primaryDark:  '#1530c2',
  primaryLight: '#4a63e8',

  // Brand accent
  cyan:    '#00c9e4', // brand cyan
  navy:    '#0d1b3e', // dark navy (auth hero background)
  navyMid: '#162456', // mid navy

  // Secondary
  secondary: '#7c3aed', // violet-600

  // Semantic
  success:   '#16a34a',
  warning:   '#d97706',
  error:     '#dc2626',
  info:      '#0891b2',

  // Neutrals
  white:     '#ffffff',
  black:     '#000000',
  gray50:    '#f9fafb',
  gray100:   '#f3f4f6',
  gray200:   '#e5e7eb',
  gray300:   '#d1d5db',
  gray400:   '#9ca3af',
  gray500:   '#6b7280',
  gray600:   '#4b5563',
  gray700:   '#374151',
  gray800:   '#1f2937',
  gray900:   '#111827',

  // Background
  background:       '#ffffff',
  backgroundDark:   '#0f172a',
  surface:          '#f9fafb',
  surfaceDark:      '#1e293b',
  border:           '#e5e7eb',
  borderDark:       '#334155',

  // Text
  textPrimary:   '#111827',
  textSecondary: '#4b5563',
  textMuted:     '#9ca3af',
  textInverse:   '#ffffff',
} as const;

export const Typography = {
  fonts: {
    regular: 'System',
    medium:  'System',
    bold:    'System',
  },
  sizes: {
    xs:   11,
    sm:   13,
    base: 15,
    md:   17,
    lg:   19,
    xl:   22,
    '2xl': 26,
    '3xl': 30,
    '4xl': 36,
  },
  lineHeights: {
    tight:  1.2,
    normal: 1.5,
    loose:  1.8,
  },
  weights: {
    normal:    '400' as const,
    medium:    '500' as const,
    semibold:  '600' as const,
    bold:      '700' as const,
    extrabold: '800' as const,
  },
} as const;

export const Spacing = {
  0:    0,
  1:    4,
  2:    8,
  3:    12,
  4:    16,
  5:    20,
  6:    24,
  8:    32,
  10:   40,
  12:   48,
  16:   64,
  px:   1,
  half: 2,
} as const;

export const Radii = {
  none:  0,
  sm:    4,
  md:    8,
  lg:    12,
  xl:    16,
  '2xl': 20,
  full:  9999,
} as const;

export const Shadows = {
  sm: {
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 2,
    elevation: 1,
  },
  md: {
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 3,
  },
  lg: {
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.15,
    shadowRadius: 16,
    elevation: 6,
  },
} as const;

export const Theme = {
  colors:     Colors,
  typography: Typography,
  spacing:    Spacing,
  radii:      Radii,
  shadows:    Shadows,
} as const;

export type ThemeType = typeof Theme;
