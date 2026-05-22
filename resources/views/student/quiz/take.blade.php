<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $quiz->title }} — Quiz</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                brand: { 50:'#eef0f8',100:'#d4d9ef',200:'#a9b3df',300:'#7f8ecf',400:'#5468bf',500:'#2a42af',600:'#14215B',700:'#0f1a48',800:'#0b1335',900:'#070d22',950:'#040812' }
            }, fontFamily: { sans: ['Inter','sans-serif'] } } }
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="font-sans bg-gray-50 antialiased min-h-screen">

@php
    $questions = $quiz->questions;
    $totalQuestions = $questions->count();
    $initialTime = $quiz->time_limit_minutes ? $quiz->time_limit_minutes * 60 : 0;
@endphp

<div
    x-data="{
        currentQuestion: 0,
        answers: {},
        timeLeft: {{ $initialTime }},
        timerRunning: true,
        submitting: false,

        formatTime(seconds) {
            if (seconds <= 0) return '00:00';
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
        },

        init() {
            if (this.timeLeft > 0) {
                const interval = setInterval(() => {
                    if (!this.timerRunning) { clearInterval(interval); return; }
                    if (this.timeLeft <= 0) {
                        clearInterval(interval);
                        this.autoSubmit();
                        return;
                    }
                    this.timeLeft--;
                }, 1000);
            }
        },

        autoSubmit() {
            this.submitting = true;
            this.$nextTick(() => {
                document.getElementById('quiz-form').submit();
            });
        },

        isAnswered(questionId) {
            const val = this.answers[questionId];
            if (val === undefined || val === null) return false;
            if (Array.isArray(val)) return val.length > 0;
            return String(val).trim() !== '';
        },

        answeredCount() {
            let count = 0;
            @foreach($questions as $q)
                if (this.isAnswered({{ $q->id }})) count++;
            @endforeach
            return count;
        },

        confirmSubmit() {
            const answered = this.answeredCount();
            const total = {{ $totalQuestions }};
            const unanswered = total - answered;
            let msg = 'Are you sure you want to submit this quiz?';
            if (unanswered > 0) {
                msg = 'You have ' + unanswered + ' unanswered question(s). Are you sure you want to submit?';
            }
            if (confirm(msg)) {
                this.submitting = true;
                this.timerRunning = false;
                document.getElementById('quiz-form').submit();
            }
        }
    }"
    x-init="init()"
    class="flex flex-col min-h-screen"
>

    {{-- ═══════════════════════════════════════
         HEADER BAR
    ═══════════════════════════════════════ --}}
    <header class="bg-white border-b border-gray-200 px-4 py-3 sticky top-0 z-20 shadow-sm">
        <div class="max-w-5xl mx-auto flex items-center justify-between gap-4">

            {{-- Quiz title --}}
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h1 class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $quiz->title }}</h1>
            </div>

            {{-- Timer (center) --}}
            @if($quiz->time_limit_minutes)
                <div class="flex items-center gap-2 shrink-0"
                     :class="timeLeft < 60 ? 'text-red-600' : 'text-gray-700'">
                    <svg class="w-5 h-5" :class="timeLeft < 60 ? 'text-red-500 animate-pulse' : 'text-gray-400'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-mono font-bold text-lg tabular-nums" x-text="formatTime(timeLeft)"></span>
                </div>
            @else
                <div class="flex items-center gap-2 text-gray-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">No time limit</span>
                </div>
            @endif

            {{-- Question counter --}}
            <div class="text-sm font-medium text-gray-600 shrink-0">
                Question <span class="text-brand-600 font-bold" x-text="currentQuestion + 1"></span>
                of <span class="font-bold text-gray-900">{{ $totalQuestions }}</span>
            </div>
        </div>
    </header>

    {{-- Auto-submit overlay --}}
    <div x-show="submitting" x-cloak
         class="fixed inset-0 bg-white/80 backdrop-blur-sm z-50 flex flex-col items-center justify-center gap-4">
        <div class="w-10 h-10 border-4 border-brand-600 border-t-transparent rounded-full animate-spin"></div>
        <p class="text-gray-700 font-medium">Submitting your quiz...</p>
    </div>

    {{-- ═══════════════════════════════════════
         MAIN CONTENT
    ═══════════════════════════════════════ --}}
    <main class="flex-1 py-6 px-4">
        <div class="max-w-3xl mx-auto space-y-6">

            {{-- Progress bar --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-4">
                <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                    <span x-text="answeredCount() + ' of {{ $totalQuestions }} answered'"></span>
                    <span x-text="Math.round(answeredCount() / {{ $totalQuestions }} * 100) + '%'"></span>
                </div>
                <div class="bg-gray-100 rounded-full h-2">
                    <div class="bg-brand-600 rounded-full h-2 transition-all duration-300"
                         :style="'width: ' + Math.round(answeredCount() / {{ $totalQuestions }} * 100) + '%'"></div>
                </div>
            </div>

            {{-- Question navigation pills --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-3">Questions</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($questions as $index => $q)
                        <button
                            type="button"
                            @click="currentQuestion = {{ $index }}"
                            :class="{
                                'bg-brand-600 text-white border-brand-600': currentQuestion === {{ $index }} && !isAnswered({{ $q->id }}),
                                'bg-brand-600 text-white border-brand-600 ring-2 ring-brand-300': currentQuestion === {{ $index }} && isAnswered({{ $q->id }}),
                                'bg-green-100 text-green-700 border-green-200': currentQuestion !== {{ $index }} && isAnswered({{ $q->id }}),
                                'bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200': currentQuestion !== {{ $index }} && !isAnswered({{ $q->id }})
                            }"
                            class="w-9 h-9 rounded-lg border text-sm font-semibold transition-all duration-150 flex items-center justify-center"
                        >
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>
                <div class="flex items-center gap-4 mt-3 pt-3 border-t border-gray-100 text-xs text-gray-500">
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded bg-green-100 border border-green-200 inline-block"></span>
                        Answered
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded bg-brand-600 inline-block"></span>
                        Current
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded bg-gray-100 border border-gray-200 inline-block"></span>
                        Unanswered
                    </span>
                </div>
            </div>

            {{-- Form wrapping all questions --}}
            <form id="quiz-form" action="{{ route('student.quiz.submit', $attempt) }}" method="POST">
                @csrf

                {{-- Question cards --}}
                @foreach($questions as $index => $question)
                    <div x-show="currentQuestion === {{ $index }}" x-cloak
                         class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

                        {{-- Question header --}}
                        <div class="px-6 py-5 border-b border-gray-100">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-2.5 py-0.5 rounded-full">
                                            Question {{ $index + 1 }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            @if($question->type === 'multiple_choice') Multiple choice
                                            @elseif($question->type === 'true_false') True / False
                                            @elseif($question->type === 'short_answer') Short answer
                                            @else Single choice
                                            @endif
                                        </span>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-900 leading-snug">{{ $question->question }}</p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <span class="text-xs text-gray-400">Points</span>
                                    <p class="font-bold text-brand-600 text-lg">{{ $question->points }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Answer area --}}
                        <div class="px-6 py-5 space-y-3">

                            @if($question->type === 'single_choice' || $question->type === 'true_false')
                                {{-- Radio options --}}
                                @foreach($question->options as $option)
                                    <label
                                        class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all duration-150"
                                        :class="answers[{{ $question->id }}] == '{{ $option->id }}'
                                            ? 'border-brand-600 bg-brand-50'
                                            : 'border-gray-200 hover:border-brand-300 hover:bg-gray-50'"
                                    >
                                        <input
                                            type="radio"
                                            name="answers[{{ $question->id }}]"
                                            value="{{ $option->id }}"
                                            x-model="answers[{{ $question->id }}]"
                                            class="w-4 h-4 text-brand-600 border-gray-300 focus:ring-brand-500"
                                        >
                                        <span class="text-sm text-gray-800 font-medium">{{ $option->option_text }}</span>
                                    </label>
                                @endforeach

                            @elseif($question->type === 'multiple_choice')
                                {{-- Checkbox options --}}
                                <p class="text-xs text-gray-400 -mt-1 mb-1">Select all that apply</p>
                                @foreach($question->options as $option)
                                    <label
                                        class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all duration-150"
                                        :class="(answers[{{ $question->id }}] || []).includes('{{ $option->id }}')
                                            ? 'border-brand-600 bg-brand-50'
                                            : 'border-gray-200 hover:border-brand-300 hover:bg-gray-50'"
                                    >
                                        <input
                                            type="checkbox"
                                            name="answers[{{ $question->id }}][]"
                                            value="{{ $option->id }}"
                                            @change="
                                                if (!answers[{{ $question->id }}]) answers[{{ $question->id }}] = [];
                                                const idx = answers[{{ $question->id }}].indexOf('{{ $option->id }}');
                                                if ($event.target.checked) {
                                                    if (idx === -1) answers[{{ $question->id }}].push('{{ $option->id }}');
                                                } else {
                                                    if (idx > -1) answers[{{ $question->id }}].splice(idx, 1);
                                                }
                                            "
                                            :checked="(answers[{{ $question->id }}] || []).includes('{{ $option->id }}')"
                                            class="w-4 h-4 text-brand-600 border-gray-300 rounded focus:ring-brand-500"
                                        >
                                        <span class="text-sm text-gray-800 font-medium">{{ $option->option_text }}</span>
                                    </label>
                                @endforeach

                            @elseif($question->type === 'short_answer')
                                {{-- Textarea --}}
                                <textarea
                                    name="answers[{{ $question->id }}]"
                                    rows="5"
                                    placeholder="Type your answer here..."
                                    x-model="answers[{{ $question->id }}]"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-brand-600 focus:ring-0 text-sm text-gray-800 placeholder-gray-400 resize-none transition-colors"
                                ></textarea>
                            @endif

                        </div>
                    </div>
                @endforeach

                {{-- Navigation buttons --}}
                <div class="bg-white rounded-2xl border border-gray-100 px-6 py-4 flex items-center justify-between gap-3">

                    {{-- Previous --}}
                    <button
                        type="button"
                        @click="if (currentQuestion > 0) currentQuestion--"
                        :disabled="currentQuestion === 0"
                        :class="currentQuestion === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-100'"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Previous
                    </button>

                    {{-- Center: answered count --}}
                    <span class="text-xs text-gray-400 hidden sm:block" x-text="answeredCount() + ' / {{ $totalQuestions }} answered'"></span>

                    {{-- Next / Submit --}}
                    <template x-if="currentQuestion < {{ $totalQuestions - 1 }}">
                        <button
                            type="button"
                            @click="currentQuestion++"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium transition-colors"
                        >
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </template>

                    <template x-if="currentQuestion === {{ $totalQuestions - 1 }}">
                        <button
                            type="button"
                            @click="confirmSubmit()"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-semibold transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Submit Quiz
                        </button>
                    </template>
                </div>

            </form>

        </div>
    </main>

    {{-- Footer hint --}}
    <footer class="text-center py-4 text-xs text-gray-400 border-t border-gray-200 bg-white">
        Your answers are saved locally. Click "Submit Quiz" when you are ready to finish.
    </footer>

</div>
</body>
</html>
