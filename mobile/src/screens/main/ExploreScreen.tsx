import React, { useState, useEffect } from 'react';
import {
  View, Text, TextInput, FlatList, TouchableOpacity, Image,
  StyleSheet, ActivityIndicator, ScrollView, Modal,
} from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { useNavigation, useRoute, RouteProp } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import { coursesApi, CourseFilters } from '@/api/courses';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { Course, CourseCategory, RootStackParamList, MainTabParamList } from '@/types';

type Nav   = NativeStackNavigationProp<RootStackParamList>;
type Route = RouteProp<MainTabParamList, 'Explore'>;

const LEVELS = ['beginner', 'intermediate', 'advanced'] as const;
type Level = typeof LEVELS[number];

function useDebounce<T>(value: T, delay: number): T {
  const [debounced, setDebounced] = useState(value);
  useEffect(() => {
    const t = setTimeout(() => setDebounced(value), delay);
    return () => clearTimeout(t);
  }, [value, delay]);
  return debounced;
}

export default function ExploreScreen() {
  const navigation = useNavigation<Nav>();
  const route      = useRoute<Route>();

  const [search, setSearch]                     = useState(route.params?.query ?? '');
  const [selectedCategory, setSelectedCategory] = useState<string | null>(null);

  // Sync if navigated to with a new query
  useEffect(() => {
    if (route.params?.query) setSearch(route.params.query);
  }, [route.params?.query]);
  const [levelFilter, setLevelFilter]           = useState<Level | null>(null);
  const [priceFilter, setPriceFilter]           = useState<'free' | 'paid' | null>(null);
  const [filterModal, setFilterModal]           = useState(false);

  const debouncedSearch = useDebounce(search, 400);

  const { data: categories } = useQuery({
    queryKey:  ['categories'],
    queryFn:   () => coursesApi.categories().then(r => r.data.data),
    staleTime: 30 * 60 * 1000,
  });

  const activeFilters: CourseFilters = {
    ...(selectedCategory           ? { category: selectedCategory }    : {}),
    ...(levelFilter                ? { level: levelFilter }             : {}),
    ...(priceFilter === 'free'     ? { is_free: true }                 : {}),
    ...(priceFilter === 'paid'     ? { is_free: false }                : {}),
    ...(debouncedSearch.length > 1 ? { search: debouncedSearch }       : {}),
  };

  const { data, isLoading } = useQuery({
    queryKey: ['courses', activeFilters],
    queryFn:  () => coursesApi.list(activeFilters).then(r => r.data.data),
  });

  const activeFilterCount = (levelFilter ? 1 : 0) + (priceFilter ? 1 : 0);

  const clearFilters = () => {
    setLevelFilter(null);
    setPriceFilter(null);
  };

  return (
    <View style={S.screen}>
      {/* Search + filter row */}
      <View style={S.searchRow}>
        <View style={S.searchBox}>
          <Ionicons name="search-outline" size={16} color={Colors.gray400} style={{ marginRight: Spacing[1] }} />
          <TextInput
            style={S.searchInput}
            value={search}
            onChangeText={setSearch}
            placeholder="Search courses…"
            placeholderTextColor={Colors.gray400}
            clearButtonMode="while-editing"
            returnKeyType="search"
          />
        </View>
        <TouchableOpacity
          style={[S.filterBtn, activeFilterCount > 0 && S.filterBtnActive]}
          onPress={() => setFilterModal(true)}
        >
          <Ionicons
            name="options-outline"
            size={15}
            color={activeFilterCount > 0 ? Colors.white : Colors.textSecondary}
            style={{ marginRight: 4 }}
          />
          <Text style={[S.filterBtnText, activeFilterCount > 0 && { color: Colors.white }]}>
            {activeFilterCount > 0 ? `Filter (${activeFilterCount})` : 'Filter'}
          </Text>
        </TouchableOpacity>
      </View>

      {/* Category pills */}
      <ScrollView
        horizontal
        showsHorizontalScrollIndicator={false}
        contentContainerStyle={S.pills}
      >
        <TouchableOpacity
          style={[S.pill, !selectedCategory && S.pillActive]}
          onPress={() => setSelectedCategory(null)}
        >
          <Text style={[S.pillText, !selectedCategory && S.pillTextActive]}>All</Text>
        </TouchableOpacity>
        {(categories ?? []).map((cat: CourseCategory) => (
          <TouchableOpacity
            key={cat.id}
            style={[S.pill, selectedCategory === cat.slug && S.pillActive]}
            onPress={() => setSelectedCategory(selectedCategory === cat.slug ? null : cat.slug)}
          >
            <Text style={[S.pillText, selectedCategory === cat.slug && S.pillTextActive]}>
              {cat.name}
            </Text>
          </TouchableOpacity>
        ))}
      </ScrollView>

      {/* Active filter chips */}
      {activeFilterCount > 0 && (
        <View style={S.activeFilters}>
          {levelFilter && (
            <TouchableOpacity style={S.chip} onPress={() => setLevelFilter(null)}>
              <Text style={S.chipText}>{levelFilter}</Text>
              <Ionicons name="close-circle-outline" size={13} color={Colors.primary} />
            </TouchableOpacity>
          )}
          {priceFilter && (
            <TouchableOpacity style={S.chip} onPress={() => setPriceFilter(null)}>
              <Text style={S.chipText}>{priceFilter}</Text>
              <Ionicons name="close-circle-outline" size={13} color={Colors.primary} />
            </TouchableOpacity>
          )}
          <TouchableOpacity onPress={clearFilters}>
            <Text style={S.clearAll}>Clear all</Text>
          </TouchableOpacity>
        </View>
      )}

      {/* Results */}
      {isLoading ? (
        <ActivityIndicator style={{ marginTop: Spacing[8] }} color={Colors.primary} />
      ) : (
        <FlatList
          data={data?.courses ?? []}
          keyExtractor={item => String(item.id)}
          contentContainerStyle={S.list}
          renderItem={({ item }) => (
            <CourseRow
              course={item}
              onPress={() => navigation.navigate('CourseDetail', { slug: item.slug })}
            />
          )}
          ListEmptyComponent={
            <View style={S.emptyWrap}>
              <Ionicons name="search-outline" size={48} color={Colors.gray300} />
              <Text style={S.empty}>No courses found</Text>
              <Text style={S.emptySub}>Try adjusting your search or filters</Text>
            </View>
          }
        />
      )}

      {/* Filter Modal */}
      <Modal
        visible={filterModal}
        animationType="slide"
        transparent
        onRequestClose={() => setFilterModal(false)}
      >
        <TouchableOpacity style={S.modalBackdrop} activeOpacity={1} onPress={() => setFilterModal(false)} />
        <View style={S.modalSheet}>
          <View style={S.modalHandle} />
          <Text style={S.modalTitle}>Filter Courses</Text>

          <Text style={S.filterLabel}>Level</Text>
          <View style={S.filterRow}>
            {LEVELS.map(l => (
              <TouchableOpacity
                key={l}
                style={[S.filterOption, levelFilter === l && S.filterOptionActive]}
                onPress={() => setLevelFilter(levelFilter === l ? null : l)}
              >
                <Text style={[S.filterOptionText, levelFilter === l && S.filterOptionTextActive]}>
                  {l.charAt(0).toUpperCase() + l.slice(1)}
                </Text>
              </TouchableOpacity>
            ))}
          </View>

          <Text style={S.filterLabel}>Price</Text>
          <View style={S.filterRow}>
            {(['free', 'paid'] as const).map(p => (
              <TouchableOpacity
                key={p}
                style={[S.filterOption, priceFilter === p && S.filterOptionActive]}
                onPress={() => setPriceFilter(priceFilter === p ? null : p)}
              >
                <Text style={[S.filterOptionText, priceFilter === p && S.filterOptionTextActive]}>
                  {p.charAt(0).toUpperCase() + p.slice(1)}
                </Text>
              </TouchableOpacity>
            ))}
          </View>

          <View style={S.modalActions}>
            <TouchableOpacity style={S.modalClear} onPress={() => { clearFilters(); setFilterModal(false); }}>
              <Text style={S.modalClearText}>Clear filters</Text>
            </TouchableOpacity>
            <TouchableOpacity style={S.modalApply} onPress={() => setFilterModal(false)}>
              <Text style={S.modalApplyText}>Apply</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </View>
  );
}

function CourseRow({ course, onPress }: { course: Course; onPress: () => void }) {
  return (
    <TouchableOpacity style={S.row} onPress={onPress} activeOpacity={0.8}>
      {course.thumbnail_url ? (
        <Image source={{ uri: course.thumbnail_url }} style={S.thumbnail} />
      ) : (
        <View style={[S.thumbnail, S.thumbnailFallback]}>
          <Ionicons name="book-outline" size={22} color={Colors.gray400} />
        </View>
      )}
      <View style={S.info}>
        <Text style={S.category}>{course.category?.name ?? 'General'}</Text>
        <Text style={S.title} numberOfLines={2}>{course.title}</Text>
        <Text style={S.meta}>
          {course.total_lessons} lessons · {course.level ?? 'All levels'}
        </Text>
        <Text style={S.price}>
          {course.is_free ? 'Free' : `₦${course.price.online.toLocaleString()}`}
        </Text>
      </View>
    </TouchableOpacity>
  );
}

const S = StyleSheet.create({
  screen:               { flex: 1, backgroundColor: Colors.surface },
  searchRow:            { flexDirection: 'row', alignItems: 'center', backgroundColor: Colors.white, padding: Spacing[4], paddingTop: Spacing[10], borderBottomWidth: 1, borderBottomColor: Colors.border, gap: Spacing[2] },
  searchBox:            { flex: 1, flexDirection: 'row', alignItems: 'center', backgroundColor: Colors.gray100, borderRadius: Radii.lg, paddingHorizontal: Spacing[3] },
  searchInput:          { flex: 1, paddingVertical: Spacing[3], fontSize: Typography.sizes.base, color: Colors.textPrimary },
  filterBtn:            { flexDirection: 'row', alignItems: 'center', paddingHorizontal: Spacing[3], paddingVertical: Spacing[3], borderRadius: Radii.lg, backgroundColor: Colors.gray100 },
  filterBtnActive:      { backgroundColor: Colors.primary },
  filterBtnText:        { fontSize: Typography.sizes.sm, color: Colors.textSecondary, fontWeight: Typography.weights.medium },
  pills:                { padding: Spacing[3], gap: Spacing[2] },
  pill:                 { paddingHorizontal: Spacing[4], paddingVertical: Spacing[2], borderRadius: Radii.full, backgroundColor: Colors.gray100, marginRight: Spacing[2] },
  pillActive:           { backgroundColor: Colors.primary },
  pillText:             { fontSize: Typography.sizes.sm, color: Colors.textSecondary, fontWeight: Typography.weights.medium },
  pillTextActive:       { color: Colors.white },
  activeFilters:        { flexDirection: 'row', alignItems: 'center', paddingHorizontal: Spacing[4], paddingBottom: Spacing[2], gap: Spacing[2], flexWrap: 'wrap' },
  chip:                 { flexDirection: 'row', alignItems: 'center', gap: 4, backgroundColor: 'rgba(30,58,219,0.1)', borderRadius: Radii.full, paddingHorizontal: Spacing[3], paddingVertical: 4 },
  chipText:             { fontSize: Typography.sizes.xs, color: Colors.primary, fontWeight: Typography.weights.medium },
  clearAll:             { fontSize: Typography.sizes.xs, color: Colors.error, fontWeight: Typography.weights.medium },
  list:                 { padding: Spacing[4] },
  emptyWrap:            { alignItems: 'center', marginTop: Spacing[12], gap: Spacing[2] },
  empty:                { fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  emptySub:             { fontSize: Typography.sizes.sm, color: Colors.textMuted },
  row:                  { flexDirection: 'row', backgroundColor: Colors.white, borderRadius: Radii.xl, marginBottom: Spacing[4], overflow: 'hidden', ...Shadows.sm },
  thumbnail:            { width: 100, height: 90 },
  thumbnailFallback:    { backgroundColor: Colors.gray100, alignItems: 'center', justifyContent: 'center' },
  info:                 { flex: 1, padding: Spacing[3] },
  category:             { fontSize: Typography.sizes.xs, color: Colors.primary, fontWeight: Typography.weights.semibold, textTransform: 'uppercase', marginBottom: Spacing[1] },
  title:                { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: Spacing[1] },
  meta:                 { fontSize: Typography.sizes.xs, color: Colors.textMuted, marginBottom: Spacing[1] },
  price:                { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.bold, color: Colors.primary },

  // Filter modal
  modalBackdrop:        { flex: 1, backgroundColor: 'rgba(0,0,0,0.45)' },
  modalSheet:           { backgroundColor: Colors.white, borderTopLeftRadius: 24, borderTopRightRadius: 24, padding: Spacing[5], paddingBottom: Spacing[8] },
  modalHandle:          { width: 40, height: 4, borderRadius: 2, backgroundColor: Colors.gray200, alignSelf: 'center', marginBottom: Spacing[4] },
  modalTitle:           { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.bold, color: Colors.textPrimary, marginBottom: Spacing[4] },
  filterLabel:          { fontSize: Typography.sizes.xs, fontWeight: Typography.weights.semibold, color: Colors.textMuted, textTransform: 'uppercase', letterSpacing: 0.8, marginBottom: Spacing[2], marginTop: Spacing[3] },
  filterRow:            { flexDirection: 'row', flexWrap: 'wrap', gap: Spacing[2] },
  filterOption:         { paddingHorizontal: Spacing[4], paddingVertical: Spacing[2], borderRadius: Radii.full, backgroundColor: Colors.gray100, borderWidth: 1.5, borderColor: 'transparent' },
  filterOptionActive:   { backgroundColor: 'rgba(30,58,219,0.1)', borderColor: Colors.primary },
  filterOptionText:     { fontSize: Typography.sizes.sm, color: Colors.textSecondary, fontWeight: Typography.weights.medium },
  filterOptionTextActive: { color: Colors.primary },
  modalActions:         { flexDirection: 'row', gap: Spacing[3], marginTop: Spacing[6] },
  modalClear:           { flex: 1, padding: Spacing[4], borderRadius: Radii.xl, borderWidth: 1.5, borderColor: Colors.gray200, alignItems: 'center' },
  modalClearText:       { color: Colors.textSecondary, fontWeight: Typography.weights.semibold },
  modalApply:           { flex: 1, padding: Spacing[4], borderRadius: Radii.xl, backgroundColor: Colors.primary, alignItems: 'center' },
  modalApplyText:       { color: Colors.white, fontWeight: Typography.weights.semibold },
});
