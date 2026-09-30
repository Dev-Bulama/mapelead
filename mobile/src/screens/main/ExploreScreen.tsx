import React, { useState } from 'react';
import {
  View, Text, TextInput, FlatList, TouchableOpacity, Image,
  StyleSheet, ActivityIndicator, ScrollView,
} from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { coursesApi, CourseFilters } from '@/api/courses';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { Course, CourseCategory, RootStackParamList } from '@/types';

type Nav = NativeStackNavigationProp<RootStackParamList>;

export default function ExploreScreen() {
  const navigation = useNavigation<Nav>();
  const [search, setSearch] = useState('');
  const [filters, setFilters] = useState<CourseFilters>({});
  const [selectedCategory, setSelectedCategory] = useState<string | null>(null);

  const { data: categories } = useQuery({
    queryKey: ['categories'],
    queryFn:  () => coursesApi.categories().then((r) => r.data.data),
    staleTime: 30 * 60 * 1000,
  });

  const activeFilters: CourseFilters = {
    ...filters,
    ...(selectedCategory ? { category: selectedCategory } : {}),
    ...(search.length > 1 ? { search } : {}),
  };

  const { data, isLoading } = useQuery({
    queryKey: ['courses', activeFilters],
    queryFn:  () => coursesApi.list(activeFilters).then((r) => r.data.data),
  });

  return (
    <View style={styles.screen}>
      {/* Search bar */}
      <View style={styles.searchRow}>
        <TextInput
          style={styles.searchInput}
          value={search}
          onChangeText={setSearch}
          placeholder="Search courses…"
          placeholderTextColor={Colors.gray400}
          clearButtonMode="while-editing"
        />
      </View>

      {/* Category pills */}
      <ScrollView
        horizontal
        showsHorizontalScrollIndicator={false}
        contentContainerStyle={styles.pills}
      >
        <TouchableOpacity
          style={[styles.pill, !selectedCategory && styles.pillActive]}
          onPress={() => setSelectedCategory(null)}
        >
          <Text style={[styles.pillText, !selectedCategory && styles.pillTextActive]}>All</Text>
        </TouchableOpacity>
        {(categories ?? []).map((cat: CourseCategory) => (
          <TouchableOpacity
            key={cat.id}
            style={[styles.pill, selectedCategory === cat.slug && styles.pillActive]}
            onPress={() => setSelectedCategory(selectedCategory === cat.slug ? null : cat.slug)}
          >
            <Text style={[styles.pillText, selectedCategory === cat.slug && styles.pillTextActive]}>
              {cat.name}
            </Text>
          </TouchableOpacity>
        ))}
      </ScrollView>

      {/* Results */}
      {isLoading ? (
        <ActivityIndicator style={{ marginTop: Spacing[8] }} color={Colors.primary} />
      ) : (
        <FlatList
          data={data?.courses ?? []}
          keyExtractor={(item) => String(item.id)}
          contentContainerStyle={styles.list}
          renderItem={({ item }) => (
            <CourseRow
              course={item}
              onPress={() => navigation.navigate('CourseDetail', { slug: item.slug })}
            />
          )}
          ListEmptyComponent={
            <Text style={styles.empty}>No courses found</Text>
          }
        />
      )}
    </View>
  );
}

function CourseRow({ course, onPress }: { course: Course; onPress: () => void }) {
  return (
    <TouchableOpacity style={styles.row} onPress={onPress}>
      {course.thumbnail_url ? (
        <Image source={{ uri: course.thumbnail_url }} style={styles.thumbnail} />
      ) : (
        <View style={[styles.thumbnail, styles.thumbnailFallback]} />
      )}
      <View style={styles.info}>
        <Text style={styles.category}>{course.category?.name ?? 'General'}</Text>
        <Text style={styles.title} numberOfLines={2}>{course.title}</Text>
        <Text style={styles.meta}>
          {course.total_lessons} lessons · {course.level ?? 'All levels'}
        </Text>
        <Text style={styles.price}>
          {course.is_free ? 'Free' : `₦${course.price.online.toLocaleString()}`}
        </Text>
      </View>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  screen:               { flex: 1, backgroundColor: Colors.surface },
  searchRow:            { backgroundColor: Colors.white, padding: Spacing[4], paddingTop: Spacing[10], borderBottomWidth: 1, borderBottomColor: Colors.border },
  searchInput:          { backgroundColor: Colors.gray100, borderRadius: Radii.lg, paddingHorizontal: Spacing[4], paddingVertical: Spacing[3], fontSize: Typography.sizes.base, color: Colors.textPrimary },
  pills:                { padding: Spacing[3], gap: Spacing[2] },
  pill:                 { paddingHorizontal: Spacing[4], paddingVertical: Spacing[2], borderRadius: Radii.full, backgroundColor: Colors.gray100, marginRight: Spacing[2] },
  pillActive:           { backgroundColor: Colors.primary },
  pillText:             { fontSize: Typography.sizes.sm, color: Colors.textSecondary, fontWeight: Typography.weights.medium },
  pillTextActive:       { color: Colors.white },
  list:                 { padding: Spacing[4] },
  empty:                { textAlign: 'center', color: Colors.textMuted, marginTop: Spacing[8] },
  row:                  { flexDirection: 'row', backgroundColor: Colors.white, borderRadius: Radii.xl, marginBottom: Spacing[4], overflow: 'hidden', ...Shadows.sm },
  thumbnail:            { width: 100, height: 90 },
  thumbnailFallback:    { backgroundColor: Colors.gray200 },
  info:                 { flex: 1, padding: Spacing[3] },
  category:             { fontSize: Typography.sizes.xs, color: Colors.primary, fontWeight: Typography.weights.semibold, textTransform: 'uppercase', marginBottom: Spacing[1] },
  title:                { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: Spacing[1] },
  meta:                 { fontSize: Typography.sizes.xs, color: Colors.textMuted, marginBottom: Spacing[1] },
  price:                { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.bold, color: Colors.primary },
});
