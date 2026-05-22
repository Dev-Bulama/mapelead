@extends('layouts.student')
@section('title', 'Quiz Result — ' . $attempt->quiz->title)
@section('page_title', 'Quiz Result')

@section('content')
@php
    $quiz        = $attempt->quiz;
    $questions   = $quiz->questions;
    $answers     = $attempt->answers->keyBy('question_id');
    $passed      = $attempt->passed;
    $scorePercent = (float) $attempt->score_percent;

    // SVG circle progress
    $radius      = 54;
    $circumference = 2 * M_PI * $radius;
    $dashOffset  = $circumference - ($scorePercent / 100) * $circumference;

    // Time taken
    $timeTaken = null;
    if ($attempt->time_taken_seconds) {
        $mins = intdiv($attempt->time_taken_seconds, 60);
        $secs = $attempt->time_taken_seconds % 60;
        $timeTaken = $mins . 'm ' . $secs . 's';
    } elseif ($attempt->started_at && $attempt->completed_at) {
        $seconds = $attempt->started_at->diffInSeconds($attempt->completed_at);
        $mins = intdiv($seconds, 60);
        $secs = $seconds % 60;
        $timeTaken = $mins . 'm ' . $secs . 's';
    }

    // Remaining attempts
    $totalAttempts = \App\Models\QuizAttempt::where('user_id', $attempt->user_id)
        ->where('quiz_id', $quiz->id)
        ->count();
    $attemptsRemaining = $quiz->max_attempts - $totalAttempts;

    $enrollment = \App\Models\Enrollment::where('user_id', $attempt->user_id)
        ->where('course_id', $quiz->course_id)
        ->first();
@endphp

<div class="space-y-6 max-w-4xl mx-auto">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('student.dashboard') }}" class="hover:text-brand-600 transition-colors">Dashboard</a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('student.courses') }}" class="hover:text-brand-600 transition-colors">My Courses</a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('student.quiz.show', $quiz) }}" class="hover:text-brand-600 transition-colors truncate max-w-[180px]">{{ $quiz->title }}</a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-900 font-medium">Result</span>
    </nav>

    {{-- ═══════════════════════════════════════
         SCORE CARD
    ═══════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

        {{-- Pass / Fail banner --}}
        @if($passed)
            <div class="bg-green-500 px-6 py-4 flex items-center gap-3">
                <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-white text-lg leading-none">Congratulations! You Passed</p>
                    <p class="text-green-100 text-sm mt-0.5">You scored above the required {{ $quiz->pass_score }}% pass mark.</p>
                </div>
            </div>
        @else
            <div class="bg-red-500 px-6 py-4 flex items-center gap-3">
                <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-white text-lg leading-none">Not Passed</p>
                    <p class="text-red-100 text-sm mt-0.5">You needed {{ $quiz->pass_score }}% to pass. Keep practicing!</p>
                </div>
            </div>
        @endif

        {{-- Score circle + stats --}}
        <div class="p-6 flex flex-col sm:flex-row items-center gap-8">

            {{-- SVG Score Circle --}}
            <div class="shrink-0 flex flex-col items-center gap-2">
                <div class="relative w-36 h-36">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 128 128">
                        {{-- Track --}}
                        <circle
                            cx="64" cy="64" r="{{ $radius }}"
                            fill="none"
                            stroke="#e5e7eb"
                            stroke-width="10"
                        />
                        {{-- Progress --}}
                        <circle
                            cx="64" cy="64" r="{{ $radius }}"
                            fill="none"
                            stroke="{{ $passed ? '#22c55e' : '#ef4444' }}"
                            stroke-width="10"
                            stroke-linecap="round"
                            stroke-dasharray="{{ number_format($circumference, 2) }}"
                            stroke-dashoffset="{{ number_format($dashOffset, 2) }}"
                            style="transition: stroke-dashoffset 1s ease-in-out;"
                        />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-3xl font-bold {{ $passed ? 'text-green-600' : 'text-red-500' }}">
                            {{ number_format($scorePercent, 0) }}%
                        </span>
                        <span class="text-xs text-gray-400 font-medium">Score</span>
                    </div>
                </div>
                <span class="text-sm text-gray-500">Pass mark: <strong>{{ $quiz->pass_score }}%</strong></span>
            </div>

            {{-- Stats grid --}}
            <div class="flex-1 grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full">

                {{-- Score --}}
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Score</p>
                    <p class="text-2xl font-bold {{ $passed ? 'text-green-600' : 'text-red-500' }}">
                        {{ number_format($scorePercent, 1) }}%
                    </p>
                </div>

                {{-- Points --}}
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Points</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $attempt->earned_points }}<span class="text-sm text-gray-400 font-normal"> / {{ $attempt->total_points }}</span>
                    </p>
                </div>

                {{-- Time taken --}}
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Time Taken</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $timeTaken ?? '—' }}
                    </p>
                </div>

                {{-- Completed --}}
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Completed</p>
                    <p class="text-lg font-bold text-gray-900">
                        {{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y') : '—' }}
                    </p>
                    @if($attempt->completed_at)
                        <p class="text-xs text-gray-400">{{ $attempt->completed_at->format('g:i A') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         QUESTION BREAKDOWN
    ═══════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Question Breakdown</h3>
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1.5 text-green-700">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-400 inline-block"></span>
                    Correct
                </span>
                <span class="flex items-center gap-1.5 text-red-600">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-400 inline-block"></span>
                    Incorrect
                </span>
            </div>
        </div>

        <div class="divide-y divide-gray-50">
            @foreach($questions as $index => $question)
                @php
                    $answer = $answers->get($question->id);
                    $isCorrect = $answer && $answer->is_correct;
                    $pointsEarned = $answer ? $answer->points_earned : 0;

                    // Build user answer display string
                    $userAnswerText = '—';
                    if ($answer) {
                        if ($question->type === 'short_answer') {
                            $userAnswerText = $answer->text_answer ?? '—';
                        } elseif (!empty($answer->selected_option_ids)) {
                            $selectedOptions = $question->options
                                ->whereIn('id', $answer->selected_option_ids)
                                ->pluck('option_text')
                                ->join(', ');
                            $userAnswerText = $selectedOptions ?: '—';
                        }
                    }

                    // Build correct answer display string
                    if ($question->type === 'short_answer') {
                        $correctAnswerText = '(Open answer)';
                    } else {
                        $correctAnswerText = $question->options
                            ->where('is_correct', true)
                            ->pluck('option_text')
                            ->join(', ');
                        $correctAnswerText = $correctAnswerText ?: '—';
                    }
                @endphp

                <div class="p-5 {{ $isCorrect ? 'bg-green-50/40' : 'bg-red-50/40' }}">
                    <div class="flex items-start gap-4">

                        {{-- Status icon --}}
                        <div class="shrink-0 mt-0.5">
                            @if($isCorrect)
                                <div class="w-7 h-7 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            @else
                                <div class="w-7 h-7 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Question body --}}
                        <div class="flex-1 min-w-0 space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <span class="text-xs font-semibold {{ $isCorrect ? 'text-green-700' : 'text-red-600' }} uppercase tracking-wide">
                                        Q{{ $index + 1 }}
                                    </span>
                                    <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $question->question }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-sm font-bold {{ $isCorrect ? 'text-green-700' : 'text-red-500' }}">
                                        +{{ $pointsEarned }}
                                    </span>
                                    <span class="text-xs text-gray-400"> / {{ $question->points }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                {{-- Your answer --}}
                                <div class="rounded-xl p-3 {{ $isCorrect ? 'bg-green-100/60' : 'bg-red-100/60' }}">
                                    <p class="text-xs font-medium {{ $isCorrect ? 'text-green-700' : 'text-red-600' }} mb-1">Your Answer</p>
                                    <p class="text-sm text-gray-800 font-medium">{{ $userAnswerText }}</p>
                                </div>

                                {{-- Correct answer --}}
                                <div class="bg-green-100/60 rounded-xl p-3">
                                    <p class="text-xs font-medium text-green-700 mb-1">Correct Answer</p>
                                    <p class="text-sm text-gray-800 font-medium">{{ $correctAnswerText }}</p>
                                </div>
                            </div>

                            {{-- Explanation --}}
                            @if($question->explanation)
                                <div class="flex items-start gap-2 text-xs text-gray-500 bg-blue-50 rounded-lg px-3 py-2">
                                    <svg class="w-3.5 h-3.5 text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $question->explanation }}</span>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         ACTION BUTTONS
    ═══════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-gray-100 px-6 py-5 flex flex-col sm:flex-row items-center gap-3 justify-between">

        <div class="flex items-center gap-2 text-sm text-gray-500">
            @if($attemptsRemaining > 0)
                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span><strong class="text-gray-800">{{ $attemptsRemaining }}</strong> attempt{{ $attemptsRemaining !== 1 ? 's' : '' }} remaining</span>
            @else
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                <span class="text-gray-400">No attempts remaining</span>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-3 justify-end">

            {{-- Take Again --}}
            @if($attemptsRemaining > 0)
                <form action="{{ route('student.quiz.start', $quiz) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Take Again
                    </button>
                </form>
            @endif

            {{-- Back to Course --}}
            @if($enrollment)
                <a href="{{ route('student.learn', $quiz->course->slug ?? $quiz->course_id) }}"
                   class="flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Back to Course
                </a>
            @endif

            {{-- View All Quizzes / Quiz Overview --}}
            <a href="{{ route('student.quiz.show', $quiz) }}"
               class="flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                View All Quizzes
            </a>

        </div>
    </div>

</div>
@endsection
