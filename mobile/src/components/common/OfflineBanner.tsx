import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { useNetworkStatus } from '@/utils/network';
import { Colors, Typography, Spacing } from '@/theme';

export default function OfflineBanner() {
  const { isOnline } = useNetworkStatus();

  if (isOnline) return null;

  return (
    <View style={styles.banner}>
      <Text style={styles.text}>⚠ No internet connection</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  banner: {
    backgroundColor: Colors.warning,
    paddingVertical:   Spacing[2],
    paddingHorizontal: Spacing[4],
    alignItems:        'center',
  },
  text: {
    color:      Colors.white,
    fontSize:   Typography.sizes.sm,
    fontWeight: Typography.weights.semibold,
  },
});
