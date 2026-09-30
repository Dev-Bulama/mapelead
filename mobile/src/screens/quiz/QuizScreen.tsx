import React, { useState, useEffect, useCallback, useRef } from 'react';
import {
  View, Text, TouchableOpacity, StyleSheet, ScrollView,
  Alert, ActivityIndicator, BackHandler,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { useQuery, useMutation } from '@tanstack/react-query';
import { apiClient } from '@/api/client';
import { API } from '@/api/endpoints';
import { extractApiError } from '@/api/client';
import { Colors, Typography, Spacing, Radii, Shadows } from '@/theme';
import type { ApiResponse, RootStackParamList } from '@/types';

type Props = NativeStackScreenProps<RootStackParamList, 'QuizView'>;

interface QuizOption { id: number; option: string }
interface QuizQuestion { id: number; question: string; type: string; points: number; options: QuizOption[] }
interface QuizData {
  id: number; title: string; description: string | null;
  pass_score: number; time_limit_minutes: number | null;
  max_attempts: number; attempts_taken: number; has_passed: boolean;
  questions: QuizQuestion[];
}
interface SubmitResult {
  score_percent: number; earned_points: number; total_points: number;
  pass_score: number; passed: boolean; attempt_number: number;
  correct_answers?: { question_id: number; correct_option_ids: number[]; explanation: string | null }[];
}

export default function QuizScreen({ route, navigation }: Props) {
  const { quizId } = route.params;

  const [currentIdx,    setCurrentIdx]    = useState(0);
  const [answers,       setAnswers]        = useState<Record<number, number[]>>({}); // questionId → optionIds
  const [timeLeft,      setTimeLeft]       = useState<number | null>(null);
  const [started,       setStarted]        = useState(false);
  const [result,        setResult]         = useState<SubmitResult | null>(null);
  const startedAt = useRef<Date>(new Date());

  const { data: quiz, isLoading } = useQuery({
    queryKey: ['quiz', quizId],
    queryFn: () =>
      apiClient.get<ApiResponse<QuizData>>(API.QUIZ(quizId)).then((r) => r.data.data),
  });

  // Timer
  useEffect(() => {
    if (!started || !quiz?.time_limit_minutes) return;
    const total = quiz.time_limit_minutes * 60;
    setTimeLeft(total);
    const interval = setInterval(() => {
      setTimeLeft((prev) => {
        if (prev === null || prev <= 1) { clearInterval(interval); handleSubmit(); return 0; }
        return prev - 1;
      });
    }, 1000);
    return () => clearInterval(interval);
  }, [started, quiz?.time_limit_minutes]);

  // Block hardware back during quiz
  useEffect(() => {
    if (!started || result) return;
    const sub = BackHandler.addEventListener('hardwareBackPress', () => {
      Alert.alert('Quit quiz?', 'Your progress will be lost.', [
        { text: 'Continue', style: 'cancel' },
        { text: 'Quit', style: 'destructive', onPress: () => navigation.goBack() },
      ]);
      return true;
    });
    return () => sub.remove();
  }, [started, result]);

  const { mutate: submitQuiz, isPending: submitting } = useMutation({
    mutationFn: () => {
      const answersPayload = Object.entries(answers).map(([qId, optIds]) => ({
        question_id: Number(qId),
        option_ids:  optIds,
      }));
      const timeTaken = Math.round((Date.now() - startedAt.current.getTime()) / 1000);
      return apiClient.post<ApiResponse<SubmitResult>>(API.QUIZ_SUBMIT(quizId), {
        answers: answersPayload,
        time_taken_seconds: timeTaken,
      });
    },
    onSuccess: (res) => setResult(res.data.data),
    onError: (err) => Alert.alert('Error', extractApiError(err)),
  });

  const handleSubmit = useCallback(() => {
    if (submitting) return;
    const unanswered = quiz?.questions.filter((q) => !answers[q.id]).length ?? 0;
    if (unanswered > 0 && !timeLeft === null) {
      Alert.alert(
        `${unanswered} unanswered`,
        'You have unanswered questions. Submit anyway?',
        [
          { text: 'Go back', style: 'cancel' },
          { text: 'Submit', onPress: () => submitQuiz() },
        ]
      );
    } else {
      submitQuiz();
    }
  }, [quiz, answers, submitting, timeLeft]);

  const toggleOption = (questionId: number, optionId: number, type: string) => {
    setAnswers((prev) => {
      const current = prev[questionId] ?? [];
      if (type === 'multiple') {
        return {
          ...prev,
          [questionId]: current.includes(optionId)
            ? current.filter((id) => id !== optionId)
            : [...current, optionId],
        };
      }
      return { ...prev, [questionId]: [optionId] };
    });
  };

  const formatTime = (secs: number) => {
    const m = Math.floor(secs / 60);
    const s = secs % 60;
    return `${m}:${s.toString().padStart(2, '0')}`;
  };

  // ── Loading ────────────────────────────────────────────────────────────────
  if (isLoading) {
    return <View style={s.center}><ActivityIndicator color={Colors.primary} size="large" /></View>;
  }
  if (!quiz) {
    return <View style={s.center}><Text>Quiz not found</Text></View>;
  }

  // ── Results screen ─────────────────────────────────────────────────────────
  if (result) {
    return (
      <View style={s.flex}>
        <View style={[s.resultHero, result.passed ? s.resultPass : s.resultFail]}>
          <Text style={s.resultEmoji}>{result.passed ? '🎉' : '📚'}</Text>
          <Text style={s.resultTitle}>{result.passed ? 'You Passed!' : 'Not quite yet'}</Text>
          <Text style={s.resultScore}>{result.score_percent.toFixed(0)}%</Text>
          <Text style={s.resultMeta}>
            {result.earned_points}/{result.total_points} pts · Pass mark: {result.pass_score}%
          </Text>
        </View>
        <View style={s.resultActions}>
          <TouchableOpacity style={s.resultBtn} onPress={() => navigation.goBack()}>
            <Text style={s.resultBtnText}>← Back to Lesson</Text>
          </TouchableOpacity>
          {!result.passed && quiz.max_attempts === 0 && (
            <TouchableOpacity
              style={[s.resultBtn, s.resultBtnPrimary]}
              onPress={() => { setResult(null); setAnswers({}); setCurrentIdx(0); setStarted(true); startedAt.current = new Date(); }}
            >
              <Text style={[s.resultBtnText, { color: Colors.white }]}>Try Again</Text>
            </TouchableOpacity>
          )}
        </View>
        {result.correct_answers && (
          <ScrollView contentContainerStyle={{ padding: Spacing[4] }}>
            <Text style={s.reviewTitle}>Answer Review</Text>
            {quiz.questions.map((q, i) => {
              const correct = result.correct_answers?.find((a) => a.question_id === q.id);
              const mine = answers[q.id] ?? [];
              const isCorrect = correct?.correct_option_ids.every((id) => mine.includes(id)) &&
                                mine.every((id) => correct?.correct_option_ids.includes(id));
              return (
                <View key={q.id} style={[s.reviewCard, isCorrect ? s.reviewCorrect : s.reviewWrong]}>
                  <Text style={s.reviewQ}>{i + 1}. {q.question}</Text>
                  {q.options.map((o) => {
                    const isMyAnswer  = mine.includes(o.id);
                    const isRightAnswer = correct?.correct_option_ids.includes(o.id);
                    return (
                      <View key={o.id} style={[s.reviewOpt, isRightAnswer && s.reviewOptCorrect, isMyAnswer && !isRightAnswer && s.reviewOptWrong]}>
                        <Text style={s.reviewOptText}>
                          {isRightAnswer ? '✓ ' : isMyAnswer ? '✗ ' : '  '}{o.option}
                        </Text>
                      </View>
                    );
                  })}
                  {correct?.explanation && <Text style={s.explanation}>{correct.explanation}</Text>}
                </View>
              );
            })}
          </ScrollView>
        )}
      </View>
    );
  }

  // ── Pre-start screen ───────────────────────────────────────────────────────
  if (!started) {
    return (
      <View style={s.flex}>
        <View style={s.preHeader}>
          <TouchableOpacity onPress={() => navigation.goBack()}>
            <Text style={s.backLink}>‹ Back</Text>
          </TouchableOpacity>
        </View>
        <ScrollView contentContainerStyle={s.preCenterScroll}>
          <View style={s.preCard}>
            <Text style={s.preEmoji}>📝</Text>
            <Text style={s.preTitle}>{quiz.title}</Text>
            {quiz.description && <Text style={s.preDesc}>{quiz.description}</Text>}
            <View style={s.preStats}>
              <PreStat label="Questions" value={String(quiz.questions.length)} />
              <PreStat label="Pass mark" value={`${quiz.pass_score}%`} />
              {quiz.time_limit_minutes && <PreStat label="Time limit" value={`${quiz.time_limit_minutes} min`} />}
              {quiz.max_attempts > 0 && <PreStat label="Max attempts" value={String(quiz.max_attempts)} />}
            </View>
            {quiz.attempts_taken > 0 && (
              <Text style={s.preAttempt}>Attempts taken: {quiz.attempts_taken}</Text>
            )}
            {quiz.has_passed && (
              <View style={s.passedBadge}>
                <Text style={s.passedBadgeText}>✅ You have already passed this quiz</Text>
              </View>
            )}
            <TouchableOpacity
              style={s.startBtn}
              onPress={() => { startedAt.current = new Date(); setStarted(true); }}
            >
              <Text style={s.startBtnText}>Start Quiz</Text>
            </TouchableOpacity>
          </View>
        </ScrollView>
      </View>
    );
  }

  // ── Quiz in progress ───────────────────────────────────────────────────────
  const question  = quiz.questions[currentIdx];
  const total     = quiz.questions.length;
  const answered  = Object.keys(answers).length;
  const myAnswers = answers[question.id] ?? [];

  return (
    <View style={s.flex}>
      {/* Top bar */}
      <View style={s.topBar}>
        <Text style={s.qCounter}>{currentIdx + 1} / {total}</Text>
        {timeLeft !== null && (
          <Text style={[s.timer, timeLeft < 60 && s.timerUrgent]}>{formatTime(timeLeft)}</Text>
        )}
        <Text style={s.answeredCount}>{answered} answered</Text>
      </View>

      {/* Progress */}
      <View style={s.progressBarOuter}>
        <View style={[s.progressBarInner, { width: `${((currentIdx + 1) / total) * 100}%` as any }]} />
      </View>

      <ScrollView contentContainerStyle={s.questionScroll}>
        <Text style={s.questionText}>{question.question}</Text>
        {question.type === 'multiple' && (
          <Text style={s.multiHint}>Select all that apply</Text>
        )}
        {question.options.map((opt) => {
          const selected = myAnswers.includes(opt.id);
          return (
            <TouchableOpacity
              key={opt.id}
              style={[s.optionBtn, selected && s.optionBtnSelected]}
              onPress={() => toggleOption(question.id, opt.id, question.type)}
            >
              <View style={[s.optionCircle, selected && s.optionCircleSelected]}>
                {selected && <View style={s.optionDot} />}
              </View>
              <Text style={[s.optionText, selected && s.optionTextSelected]}>{opt.option}</Text>
            </TouchableOpacity>
          );
        })}
      </ScrollView>

      {/* Navigation */}
      <View style={s.navBar}>
        <TouchableOpacity
          style={[s.navBtn, currentIdx === 0 && s.navBtnDisabled]}
          onPress={() => setCurrentIdx((i) => i - 1)}
          disabled={currentIdx === 0}
        >
          <Text style={s.navBtnText}>‹ Prev</Text>
        </TouchableOpacity>

        {currentIdx < total - 1 ? (
          <TouchableOpacity style={[s.navBtn, s.navBtnPrimary]} onPress={() => setCurrentIdx((i) => i + 1)}>
            <Text style={[s.navBtnText, { color: Colors.white }]}>Next ›</Text>
          </TouchableOpacity>
        ) : (
          <TouchableOpacity
            style={[s.navBtn, s.navBtnSubmit, submitting && s.navBtnDisabled]}
            onPress={handleSubmit}
            disabled={submitting}
          >
            <Text style={[s.navBtnText, { color: Colors.white }]}>
              {submitting ? 'Submitting…' : 'Submit'}
            </Text>
          </TouchableOpacity>
        )}
      </View>

      {/* Question dots */}
      <ScrollView horizontal showsHorizontalScrollIndicator={false} style={s.dotsRow} contentContainerStyle={s.dotsContent}>
        {quiz.questions.map((q, i) => (
          <TouchableOpacity key={q.id} onPress={() => setCurrentIdx(i)}>
            <View style={[s.dot, i === currentIdx && s.dotCurrent, answers[q.id] && s.dotAnswered]} />
          </TouchableOpacity>
        ))}
      </ScrollView>
    </View>
  );
}

function PreStat({ label, value }: { label: string; value: string }) {
  return (
    <View style={s.preStat}>
      <Text style={s.preStatValue}>{value}</Text>
      <Text style={s.preStatLabel}>{label}</Text>
    </View>
  );
}

const s = StyleSheet.create({
  flex:               { flex: 1, backgroundColor: Colors.white },
  center:             { flex: 1, justifyContent: 'center', alignItems: 'center' },

  // Pre-start
  preHeader:          { paddingTop: Spacing[12], paddingHorizontal: Spacing[4], paddingBottom: Spacing[2] },
  backLink:           { fontSize: Typography.sizes.base, color: Colors.primary, fontWeight: Typography.weights.medium },
  preCenterScroll:    { flexGrow: 1, justifyContent: 'center', padding: Spacing[4] },
  preCard:            { backgroundColor: Colors.white, borderRadius: Radii['2xl'], padding: Spacing[6], ...Shadows.md, alignItems: 'center' },
  preEmoji:           { fontSize: 48, marginBottom: Spacing[4] },
  preTitle:           { fontSize: Typography.sizes.xl, fontWeight: Typography.weights.bold, color: Colors.textPrimary, textAlign: 'center', marginBottom: Spacing[2] },
  preDesc:            { fontSize: Typography.sizes.sm, color: Colors.textSecondary, textAlign: 'center', marginBottom: Spacing[4] },
  preStats:           { flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'center', gap: Spacing[3], marginBottom: Spacing[4] },
  preStat:            { backgroundColor: Colors.surface, borderRadius: Radii.lg, padding: Spacing[3], alignItems: 'center', minWidth: 80 },
  preStatValue:       { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.bold, color: Colors.primary },
  preStatLabel:       { fontSize: Typography.sizes.xs, color: Colors.textMuted, marginTop: 2 },
  preAttempt:         { fontSize: Typography.sizes.sm, color: Colors.textMuted, marginBottom: Spacing[3] },
  passedBadge:        { backgroundColor: '#f0fdf4', borderRadius: Radii.lg, padding: Spacing[3], marginBottom: Spacing[4] },
  passedBadgeText:    { color: Colors.success, fontSize: Typography.sizes.sm, fontWeight: Typography.weights.medium },
  startBtn:           { backgroundColor: Colors.primary, borderRadius: Radii.lg, paddingVertical: Spacing[4], paddingHorizontal: Spacing[8], marginTop: Spacing[2] },
  startBtnText:       { color: Colors.white, fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold },

  // In-quiz
  topBar:             { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingHorizontal: Spacing[4], paddingTop: Spacing[12], paddingBottom: Spacing[3], backgroundColor: Colors.white, borderBottomWidth: 1, borderBottomColor: Colors.border },
  qCounter:           { fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold, color: Colors.textPrimary },
  timer:              { fontSize: Typography.sizes.base, fontWeight: Typography.weights.bold, color: Colors.primary },
  timerUrgent:        { color: Colors.error },
  answeredCount:      { fontSize: Typography.sizes.sm, color: Colors.textMuted },
  progressBarOuter:   { height: 4, backgroundColor: Colors.gray200 },
  progressBarInner:   { height: '100%', backgroundColor: Colors.primary },
  questionScroll:     { padding: Spacing[6] },
  questionText:       { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, lineHeight: 28, marginBottom: Spacing[4] },
  multiHint:          { fontSize: Typography.sizes.xs, color: Colors.textMuted, marginBottom: Spacing[3], fontStyle: 'italic' },
  optionBtn:          { flexDirection: 'row', alignItems: 'center', borderWidth: 2, borderColor: Colors.border, borderRadius: Radii.xl, padding: Spacing[4], marginBottom: Spacing[3], gap: Spacing[3] },
  optionBtnSelected:  { borderColor: Colors.primary, backgroundColor: '#eff6ff' },
  optionCircle:       { width: 22, height: 22, borderRadius: Radii.full, borderWidth: 2, borderColor: Colors.gray400, justifyContent: 'center', alignItems: 'center' },
  optionCircleSelected:{ borderColor: Colors.primary },
  optionDot:          { width: 12, height: 12, borderRadius: Radii.full, backgroundColor: Colors.primary },
  optionText:         { flex: 1, fontSize: Typography.sizes.base, color: Colors.textPrimary },
  optionTextSelected: { color: Colors.primary, fontWeight: Typography.weights.medium },

  // Nav bar
  navBar:             { flexDirection: 'row', justifyContent: 'space-between', padding: Spacing[4], borderTopWidth: 1, borderTopColor: Colors.border, gap: Spacing[3] },
  navBtn:             { flex: 1, borderWidth: 1.5, borderColor: Colors.border, borderRadius: Radii.lg, paddingVertical: Spacing[3], alignItems: 'center' },
  navBtnPrimary:      { backgroundColor: Colors.primary, borderColor: Colors.primary },
  navBtnSubmit:       { backgroundColor: Colors.success, borderColor: Colors.success },
  navBtnDisabled:     { opacity: 0.35 },
  navBtnText:         { fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },

  // Dots
  dotsRow:            { maxHeight: 36, borderTopWidth: 1, borderTopColor: Colors.border },
  dotsContent:        { paddingHorizontal: Spacing[4], alignItems: 'center', gap: Spacing[2] },
  dot:                { width: 10, height: 10, borderRadius: Radii.full, backgroundColor: Colors.gray300, margin: Spacing[1] },
  dotCurrent:         { backgroundColor: Colors.primary, transform: [{ scale: 1.3 }] },
  dotAnswered:        { backgroundColor: Colors.success },

  // Results
  resultHero:         { alignItems: 'center', paddingVertical: Spacing[8], paddingHorizontal: Spacing[4] },
  resultPass:         { backgroundColor: Colors.success },
  resultFail:         { backgroundColor: Colors.primary },
  resultEmoji:        { fontSize: 48, marginBottom: Spacing[3] },
  resultTitle:        { fontSize: Typography.sizes['2xl'], fontWeight: Typography.weights.bold, color: Colors.white },
  resultScore:        { fontSize: 64, fontWeight: Typography.weights.extrabold, color: Colors.white, lineHeight: 72, marginVertical: Spacing[2] },
  resultMeta:         { fontSize: Typography.sizes.base, color: 'rgba(255,255,255,0.85)' },
  resultActions:      { flexDirection: 'row', gap: Spacing[3], padding: Spacing[4] },
  resultBtn:          { flex: 1, borderWidth: 1.5, borderColor: Colors.border, borderRadius: Radii.lg, paddingVertical: Spacing[4], alignItems: 'center' },
  resultBtnPrimary:   { backgroundColor: Colors.primary, borderColor: Colors.primary },
  resultBtnText:      { fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold, color: Colors.textPrimary },

  // Review
  reviewTitle:        { fontSize: Typography.sizes.lg, fontWeight: Typography.weights.bold, color: Colors.textPrimary, marginBottom: Spacing[4] },
  reviewCard:         { borderRadius: Radii.xl, padding: Spacing[4], marginBottom: Spacing[4], borderLeftWidth: 4, borderLeftColor: Colors.gray300, backgroundColor: Colors.surface },
  reviewCorrect:      { borderLeftColor: Colors.success, backgroundColor: '#f0fdf4' },
  reviewWrong:        { borderLeftColor: Colors.error, backgroundColor: '#fef2f2' },
  reviewQ:            { fontSize: Typography.sizes.base, fontWeight: Typography.weights.semibold, color: Colors.textPrimary, marginBottom: Spacing[3] },
  reviewOpt:          { paddingVertical: Spacing[1], paddingHorizontal: Spacing[2], borderRadius: Radii.md, marginBottom: Spacing[1] },
  reviewOptCorrect:   { backgroundColor: '#dcfce7' },
  reviewOptWrong:     { backgroundColor: '#fee2e2' },
  reviewOptText:      { fontSize: Typography.sizes.sm, color: Colors.textPrimary },
  explanation:        { fontSize: Typography.sizes.sm, color: Colors.textSecondary, marginTop: Spacing[2], fontStyle: 'italic' },
});
