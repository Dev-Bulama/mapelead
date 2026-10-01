import React, { useState } from 'react';
import {
  View, Text, ScrollView, TouchableOpacity, Image, StyleSheet,
  ActivityIndicator, Alert, Modal, TextInput,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { coursesApi } from '@/api/courses';
import { enrollmentsApi } from '@/api/enrollments';
import { apiClient, extractApiError } from '@/api/client';
import { API } from '@/api/endpoints';
import { useAuthStore, selectUser } from '@/stores/authStore';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import { formatDate } from '@/utils/time';
import type { RootStackParamList, Enrollment, ModuleSummary, CourseReview, ApiResponse } from '@/types';

type Props = NativeStackScreenProps<RootStackParamList, 'CourseDetail'>;

export default function CourseDetailScreen({ route, navigation }: Props) {
  const { slug } = route.params;
  const user = useAuthStore(selectUser);
  const qc   = useQueryClient();
  const [expandedModule, setExpandedModule] = useState<number | null>(null);
  const [reviewModal, setReviewModal] = useState(false);
  const [reviewRating, setReviewRating] = useState(5);
  const [reviewTitle, setReviewTitle] = useState('');
  const [reviewBody, setReviewBody] = useState('');

  const { data: course, isLoading } = useQuery({
    queryKey: ['course', slug],
    queryFn:  () => coursesApi.detail(slug).then((r) => r.data.data),
  });

  const { data: enrollments } = useQuery({
    queryKey: ['enrollments'],
    queryFn:  () => enrollmentsApi.myCourses().then((r) => r.data.data),
    enabled:  !!user,
  });

  const enrollment = enrollments?.find(
    (e: Enrollment) => e.course?.slug === slug && e.payment_status === 'paid'
  );

  const { data: reviews } = useQuery({
    queryKey: ['course-reviews', slug],
    queryFn:  () => apiClient.get<ApiResponse<{ reviews: CourseReview[]; avg_rating: number; total: number }>>(
      API.COURSE_REVIEWS(course?.id ?? 0)
    ).then(r => r.data.data),
    enabled: !!course,
    staleTime: 60_000,
  });

  const { data: myReview } = useQuery({
    queryKey: ['course-my-review', slug],
    queryFn:  () => apiClient.get<ApiResponse<CourseReview | null>>(
      API.COURSE_MY_REVIEW(course?.id ?? 0)
    ).then(r => r.data.data),
    enabled: !!course && !!user,
    staleTime: 60_000,
  });

  const { data: certEligibility, refetch: refetchEligibility } = useQuery({
    queryKey: ['cert-eligibility', slug],
    queryFn:  () => apiClient.get<ApiResponse<{ eligible: boolean; reasons: string[] }>>(
      API.CERTIFICATE_ELIGIBILITY(course?.id ?? 0)
    ).then(r => r.data.data),
    enabled: !!course && !!enrollment && (course.has_certificate ?? false),
  });

  const { data: ownedCert, refetch: refetchCert } = useQuery({
    queryKey: ['my-cert', slug],
    queryFn:  () => apiClient.get<ApiResponse<any[]>>(API.CERTIFICATES)
      .then(r => r.data.data.find((c: any) => c.course?.slug === slug) ?? null),
    enabled: !!enrollment && (course?.has_certificate ?? false),
  });

  const { mutate: claimCertificate, isPending: claiming } = useMutation({
    mutationFn: () => apiClient.post(API.CERTIFICATE_CLAIM(course!.id)),
    onSuccess: () => {
      refetchCert();
      refetchEligibility();
      Alert.alert('Certificate Issued!', 'Your certificate has been issued. View it in the Certificates tab.');
    },
    onError: (err) => Alert.alert('Not eligible', extractApiError(err)),
  });

  const { mutate: submitReview, isPending: submittingReview } = useMutation({
    mutationFn: () => apiClient.post(API.COURSE_REVIEWS(course!.id), {
      rating: reviewRating,
      title:  reviewTitle.trim() || undefined,
      body:   reviewBody.trim()  || undefined,
    }),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['course-reviews', slug] });
      qc.invalidateQueries({ queryKey: ['course-my-review', slug] });
      setReviewModal(false);
      setReviewTitle('');
      setReviewBody('');
      setReviewRating(5);
      Alert.alert('Review Submitted', 'Your review is pending approval.');
    },
    onError: (err) => Alert.alert('Error', extractApiError(err)),
  });

  const { mutate: enroll, isPending: enrolling } = useMutation({
    mutationFn: () => enrollmentsApi.enroll(course!.id, { training_type: 'online' }),
    onSuccess: (res) => {
      const { is_free, payment_url } = res.data.data;
      if (is_free) {
        qc.invalidateQueries({ queryKey: ['enrollments'] });
        Alert.alert('Enrolled!', 'You now have access to this course.');
      } else if (payment_url) {
        navigation.navigate('PaymentWebView', {
          url: payment_url,
          reference: res.data.data.payment_reference!,
        });
      }
    },
    onError: (err) => Alert.alert('Error', extractApiError(err)),
  });

  if (isLoading) {
    return <View style={styles.center}><ActivityIndicator color={Colors.primary} size="large" /></View>;
  }

  if (!course) {
    return <View style={styles.center}><Text>Course not found</Text></View>;
  }

  return (
    <View style={styles.flex}>
      <ScrollView contentContainerStyle={styles.scroll}>
        {/* Hero */}
        {course.thumbnail_url ? (
          <Image source={{ uri: course.thumbnail_url }} style={styles.hero} />
        ) : (
          <View style={[styles.hero, styles.heroFallback]} />
        )}

        {/* Back button */}
        <TouchableOpacity style={styles.backBtn} onPress={() => navigation.goBack()}>
          <Text style={styles.backBtnText}>‹</Text>
        </TouchableOpacity>

        <View style={styles.body}>
          {/* Category & Title */}
          <Text style={styles.category}>{course.category?.name ?? 'Course'}</Text>
          <Text style={styles.title}>{course.title}</Text>

          {/* Stats row */}
          <View style={styles.statsRow}>
            <StatChip label={`${course.total_lessons} lessons`} />
            {course.level && <StatChip label={course.level} />}
            {course.has_certificate && <StatChip label="Certificate" highlight />}
            {course.duration_hours ? <StatChip label={`${course.duration_hours}h`} /> : null}
          </View>

          {/* Description */}
          {course.description && (
            <View style={styles.section}>
              <Text style={styles.sectionTitle}>About this course</Text>
              <Text style={styles.bodyText}>{course.description}</Text>
            </View>
          )}

          {/* Curriculum */}
          {(course.modules ?? []).length > 0 && (
            <View style={styles.section}>
              <Text style={styles.sectionTitle}>Curriculum</Text>
              {(course.modules ?? []).map((mod: ModuleSummary) => (
                <View key={mod.id} style={styles.module}>
                  <TouchableOpacity
                    style={styles.moduleHeader}
                    onPress={() => setExpandedModule(expandedModule === mod.id ? null : mod.id)}
                  >
                    <Text style={styles.moduleTitle}>{mod.title}</Text>
                    <Text style={styles.moduleChevron}>
                      {expandedModule === mod.id ? '▲' : '▼'}
                    </Text>
                  </TouchableOpacity>
                  {expandedModule === mod.id && mod.lessons.map((lesson) => (
                    <TouchableOpacity
                      key={lesson.id}
                      style={styles.lessonRow}
                      onPress={() => {
                        if (enrollment || lesson.is_free_preview) {
                          navigation.navigate('LessonView', { lessonId: lesson.id, courseSlug: slug });
                        } else {
                          Alert.alert('Enroll required', 'Please enroll to access this lesson.');
                        }
                      }}
                    >
                      <Ionicons
                        name={lesson.type === 'video' ? 'play-circle-outline' : lesson.type === 'quiz' ? 'help-circle-outline' : 'document-text-outline'}
                        size={16}
                        color={Colors.gray500}
                      />
                      <Text style={styles.lessonTitle} numberOfLines={1}>{lesson.title}</Text>
                      {lesson.is_free_preview && (
                        <Text style={styles.freeTag}>Free</Text>
                      )}
                      {lesson.duration_minutes && (
                        <Text style={styles.duration}>{lesson.duration_minutes}m</Text>
                      )}
                    </TouchableOpacity>
                  ))}
                </View>
              ))}
            </View>
          )}
          {/* Reviews */}
          <View style={styles.section}>
            <View style={styles.reviewsHeader}>
              <Text style={styles.sectionTitle}>Reviews</Text>
              {(reviews?.avg_rating ?? course.avg_rating ?? 0) > 0 && (
                <View style={styles.ratingBadge}>
                  <Text style={styles.ratingBadgeStar}>★</Text>
                  <Text style={styles.ratingBadgeNum}>
                    {(reviews?.avg_rating ?? course.avg_rating ?? 0).toFixed(1)}
                  </Text>
                  <Text style={styles.ratingBadgeCount}>
                    ({reviews?.total ?? course.review_count ?? 0})
                  </Text>
                </View>
              )}
            </View>

            {(reviews?.reviews ?? []).slice(0, 3).map((rv: CourseReview) => (
              <View key={rv.id} style={styles.reviewCard}>
                <View style={styles.reviewTop}>
                  <View style={styles.reviewAvatar}>
                    {rv.reviewer?.avatar_url ? (
                      <Image source={{ uri: rv.reviewer.avatar_url }} style={styles.reviewAvatarImg} />
                    ) : (
                      <Text style={styles.reviewAvatarInitial}>
                        {rv.reviewer?.full_name?.[0] ?? '?'}
                      </Text>
                    )}
                  </View>
                  <View style={{ flex: 1 }}>
                    <Text style={styles.reviewerName}>{rv.reviewer?.full_name ?? 'Anonymous'}</Text>
                    <Text style={styles.reviewDate}>{formatDate(rv.created_at ?? '')}</Text>
                  </View>
                  <Text style={styles.reviewStars}>{'★'.repeat(rv.rating)}{'☆'.repeat(5 - rv.rating)}</Text>
                </View>
                {rv.title ? <Text style={styles.reviewTitle}>{rv.title}</Text> : null}
                {rv.body  ? <Text style={styles.reviewBody}>{rv.body}</Text>   : null}
              </View>
            ))}

            {enrollment && !myReview && (
              <TouchableOpacity style={styles.writeReviewBtn} onPress={() => setReviewModal(true)}>
                <Ionicons name="create-outline" size={16} color={Colors.primary} style={{ marginRight: 6 }} />
                <Text style={styles.writeReviewText}>Write a Review</Text>
              </TouchableOpacity>
            )}
            {myReview && (
              <View style={styles.alreadyReviewed}>
                <Ionicons
                  name={myReview.is_approved ? 'checkmark-circle-outline' : 'time-outline'}
                  size={14}
                  color={Colors.textMuted}
                  style={{ marginRight: 4 }}
                />
                <Text style={styles.alreadyReviewedText}>
                  {myReview.is_approved ? 'Your review is published' : 'Your review is pending approval'}
                </Text>
              </View>
            )}
          </View>

          {/* Certificate claim */}
          {enrollment && course?.has_certificate && (
            <View style={styles.section}>
              <View style={styles.certSection}>
                <View style={styles.certIcon}>
                  <Ionicons name="ribbon" size={28} color={Colors.primary} />
                </View>
                <View style={{ flex: 1 }}>
                  <Text style={styles.certTitle}>Certificate of Completion</Text>
                  {ownedCert ? (
                    <Text style={styles.certSubtitle}>
                      Issued {ownedCert.issued_at
                        ? new Date(ownedCert.issued_at).toLocaleDateString('en-NG', { day: 'numeric', month: 'short', year: 'numeric' })
                        : ''}
                    </Text>
                  ) : certEligibility?.eligible ? (
                    <Text style={styles.certSubtitle}>You have completed this course!</Text>
                  ) : (
                    <Text style={styles.certSubtitle}>Complete all lessons to earn your certificate</Text>
                  )}
                </View>
                {ownedCert ? (
                  <View style={styles.certBadge}>
                    <Ionicons name="checkmark-circle" size={14} color={Colors.success} />
                    <Text style={styles.certBadgeText}>Earned</Text>
                  </View>
                ) : certEligibility?.eligible ? (
                  <TouchableOpacity
                    style={[styles.claimBtn, claiming && { opacity: 0.6 }]}
                    onPress={() => claimCertificate()}
                    disabled={claiming}
                  >
                    {claiming
                      ? <ActivityIndicator size="small" color={Colors.white} />
                      : <Text style={styles.claimBtnText}>Claim</Text>
                    }
                  </TouchableOpacity>
                ) : null}
              </View>
            </View>
          )}
        </View>
      </ScrollView>

      {/* Write Review Modal */}
      <Modal visible={reviewModal} animationType="slide" transparent onRequestClose={() => setReviewModal(false)}>
        <View style={styles.modalOverlay}>
          <View style={styles.modalSheet}>
            <View style={styles.modalHandle} />
            <View style={styles.modalTitleRow}>
              <Text style={styles.modalTitle}>Write a Review</Text>
              <TouchableOpacity onPress={() => setReviewModal(false)}>
                <Ionicons name="close" size={24} color={Colors.gray400} />
              </TouchableOpacity>
            </View>

            <Text style={styles.inputLabel}>Rating</Text>
            <View style={styles.starRow}>
              {[1, 2, 3, 4, 5].map(n => (
                <TouchableOpacity key={n} onPress={() => setReviewRating(n)}>
                  <Text style={[styles.starBtn, n <= reviewRating && styles.starBtnActive]}>★</Text>
                </TouchableOpacity>
              ))}
            </View>

            <Text style={styles.inputLabel}>Title (optional)</Text>
            <TextInput
              style={styles.textInput}
              value={reviewTitle}
              onChangeText={setReviewTitle}
              placeholder="Summarize your experience"
              placeholderTextColor={Colors.gray400}
              maxLength={120}
            />

            <Text style={styles.inputLabel}>Review (optional)</Text>
            <TextInput
              style={[styles.textInput, styles.textArea]}
              value={reviewBody}
              onChangeText={setReviewBody}
              placeholder="Share details of your experience"
              placeholderTextColor={Colors.gray400}
              multiline
              numberOfLines={4}
              maxLength={1000}
            />

            <TouchableOpacity
              style={[styles.submitBtn, submittingReview && styles.submitBtnDisabled]}
              onPress={() => submitReview()}
              disabled={submittingReview}
            >
              <Text style={styles.submitBtnText}>{submittingReview ? 'Submitting…' : 'Submit Review'}</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>

      {/* CTA */}
      <View style={styles.cta}>
        {enrollment ? (
          <TouchableOpacity
            style={styles.ctaBtn}
            onPress={() =>
              navigation.navigate('LessonView', {
                lessonId: course.modules?.[0]?.lessons?.[0]?.id ?? 0,
                courseSlug: slug,
              })
            }
          >
            <Text style={styles.ctaBtnText}>Continue Learning →</Text>
          </TouchableOpacity>
        ) : (
          <TouchableOpacity
            style={[styles.ctaBtn, enrolling && styles.ctaBtnDisabled]}
            onPress={() => enroll()}
            disabled={enrolling}
          >
            <Text style={styles.ctaBtnText}>
              {enrolling
                ? 'Processing…'
                : course.is_free
                ? 'Enroll Free'
                : `Enroll — ₦${course.price.online.toLocaleString()}`}
            </Text>
          </TouchableOpacity>
        )}
      </View>
    </View>
  );
}

function StatChip({ label, highlight }: { label: string; highlight?: boolean }) {
  return (
    <View style={[styles.chip, highlight && styles.chipHighlight]}>
      <Text style={[styles.chipText, highlight && styles.chipTextHighlight]}>{label}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  flex:              { flex: 1, backgroundColor: Colors.white },
  center:            { flex: 1, justifyContent: 'center', alignItems: 'center' },
  scroll:            { paddingBottom: 100 },
  hero:              { width: '100%', height: 240, backgroundColor: Colors.gray200 },
  heroFallback:      { backgroundColor: Colors.primaryLight },
  backBtn:           { position: 'absolute', top: 44, left: Spacing[4], backgroundColor: 'rgba(0,0,0,0.4)', width: 36, height: 36, borderRadius: Radii.full, justifyContent: 'center', alignItems: 'center' },
  backBtnText:       { color: Colors.white, fontSize: 22, fontWeight: Typography.weights.bold, marginTop: -2 },
  body:              { padding: Spacing[4] },
  category:          { fontSize: Typography.sizes.xs, color: Colors.primary, fontWeight: Typography.weights.semibold, textTransform: 'uppercase', marginBottom: Spacing[2] },
  title:             { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.textPrimary, lineHeight: 32, marginBottom: Spacing[3] },
  statsRow:          { flexDirection: 'row', flexWrap: 'wrap', gap: Spacing[2], marginBottom: Spacing[4] },
  chip:              { backgroundColor: Colors.gray100, borderRadius: Radii.full, paddingHorizontal: Spacing[3], paddingVertical: Spacing[1] },
  chipHighlight:     { backgroundColor: Colors.primary },
  chipText:          { fontSize: Typography.sizes.xs, color: Colors.textSecondary, fontWeight: Typography.weights.medium },
  chipTextHighlight: { color: Colors.white },
  section:           { marginBottom: Spacing[6] },
  sectionTitle:      { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: Spacing[3] },
  bodyText:          { fontSize: Typography.sizes.base, color: Colors.textSecondary, lineHeight: 24 },
  module:            { backgroundColor: Colors.surface, borderRadius: Radii.lg, marginBottom: Spacing[3], overflow: 'hidden' },
  moduleHeader:      { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', padding: Spacing[4] },
  moduleTitle:       { flex: 1, fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  moduleChevron:     { fontSize: Typography.sizes.xs, color: Colors.gray500 },
  lessonRow:         { flexDirection: 'row', alignItems: 'center', padding: Spacing[3], paddingLeft: Spacing[4], borderTopWidth: 1, borderTopColor: Colors.border, gap: Spacing[3] },
  lessonIcon:        { fontSize: 14, color: Colors.gray500 },
  lessonTitle:       { flex: 1, fontSize: Typography.sizes.sm, color: Colors.textPrimary },
  freeTag:           { fontSize: Typography.sizes.xs, color: Colors.success, fontWeight: Typography.weights.semibold },
  duration:          { fontSize: Typography.sizes.xs, color: Colors.textMuted },
  cta:               { position: 'absolute', bottom: 0, left: 0, right: 0, backgroundColor: Colors.white, padding: Spacing[4], borderTopWidth: 1, borderTopColor: Colors.border, ...Shadows.lg },
  ctaBtn:            { backgroundColor: Colors.primary, borderRadius: Radii.lg, paddingVertical: Spacing[4], alignItems: 'center' },
  ctaBtnDisabled:    { opacity: 0.6 },
  ctaBtnText:        { color: Colors.white, fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold },

  // Reviews
  reviewsHeader:       { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: Spacing[3] },
  ratingBadge:         { flexDirection: 'row', alignItems: 'center', gap: 3 },
  ratingBadgeStar:     { fontSize: 14, color: '#f59e0b' },
  ratingBadgeNum:      { fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  ratingBadgeCount:    { fontSize: Typography.sizes.sm, color: Colors.textMuted },
  reviewCard:          { backgroundColor: Colors.surface, borderRadius: Radii.lg, padding: Spacing[4], marginBottom: Spacing[3] },
  reviewTop:           { flexDirection: 'row', alignItems: 'center', gap: Spacing[3], marginBottom: Spacing[2] },
  reviewAvatar:        { width: 36, height: 36, borderRadius: 18, backgroundColor: Colors.primary, alignItems: 'center', justifyContent: 'center', overflow: 'hidden' },
  reviewAvatarImg:     { width: 36, height: 36 },
  reviewAvatarInitial: { color: Colors.white, fontWeight: Typography.weights.bold, fontSize: Typography.sizes.base },
  reviewerName:        { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  reviewDate:          { fontSize: Typography.sizes.xs, color: Colors.textMuted },
  reviewStars:         { fontSize: 12, color: '#f59e0b' },
  reviewTitle:         { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: 4 },
  reviewBody:          { fontSize: Typography.sizes.sm, color: Colors.textSecondary, lineHeight: 20 },
  writeReviewBtn:      { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', borderWidth: 1.5, borderColor: Colors.primary, borderRadius: Radii.lg, paddingVertical: Spacing[3], marginTop: Spacing[2] },
  writeReviewText:     { color: Colors.primary, fontWeight: Typography.weights.semibold },
  alreadyReviewed:     { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', backgroundColor: Colors.surface, borderRadius: Radii.lg, paddingVertical: Spacing[3], marginTop: Spacing[2] },
  alreadyReviewedText: { fontSize: Typography.sizes.sm, color: Colors.textMuted },

  // Review modal
  modalOverlay:  { flex: 1, justifyContent: 'flex-end', backgroundColor: 'rgba(0,0,0,0.45)' },
  modalSheet:    { backgroundColor: Colors.white, borderTopLeftRadius: 24, borderTopRightRadius: 24, padding: Spacing[5] },
  modalHandle:   { width: 40, height: 4, borderRadius: 2, backgroundColor: Colors.gray200, alignSelf: 'center', marginBottom: Spacing[4] },
  modalTitleRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: Spacing[4] },
  modalTitle:    { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  inputLabel:    { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold, color: Colors.textSecondary, marginBottom: Spacing[2], marginTop: Spacing[3] },
  starRow:       { flexDirection: 'row', gap: Spacing[2] },
  starBtn:       { fontSize: 30, color: Colors.gray300 },
  starBtnActive: { color: '#f59e0b' },
  textInput:     { backgroundColor: Colors.surface, borderRadius: Radii.lg, paddingHorizontal: Spacing[4], paddingVertical: Spacing[3], fontSize: Typography.sizes.base, color: Colors.textPrimary, borderWidth: 1, borderColor: Colors.border },
  textArea:      { height: 100, textAlignVertical: 'top' },
  submitBtn:     { backgroundColor: Colors.primary, borderRadius: Radii.lg, paddingVertical: Spacing[4], alignItems: 'center', marginTop: Spacing[5] },
  submitBtnDisabled: { opacity: 0.6 },
  submitBtnText: { color: Colors.white, fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold },

  // Certificate section
  certSection:   { flexDirection: 'row', alignItems: 'center', backgroundColor: Colors.surface, borderRadius: Radii.xl, padding: Spacing[4], gap: Spacing[3] },
  certIcon:      { width: 52, height: 52, borderRadius: Radii.full, backgroundColor: '#fef9c3', alignItems: 'center', justifyContent: 'center' },
  certTitle:     { fontSize: Typography.sizes.sm, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },
  certSubtitle:  { fontSize: Typography.sizes.xs, color: Colors.textMuted, marginTop: 2 },
  certBadge:     { flexDirection: 'row', alignItems: 'center', gap: 4, backgroundColor: '#dcfce7', borderRadius: Radii.full, paddingHorizontal: Spacing[2], paddingVertical: 4 },
  certBadgeText: { fontSize: Typography.sizes.xs, color: Colors.success, fontWeight: Typography.weights.semibold },
  claimBtn:      { backgroundColor: Colors.primary, borderRadius: Radii.lg, paddingHorizontal: Spacing[4], paddingVertical: Spacing[2] },
  claimBtnText:  { color: Colors.white, fontSize: Typography.sizes.sm, fontWeight: Typography.weights.bold },
});
