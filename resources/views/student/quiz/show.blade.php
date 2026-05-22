@extends('layouts.student')
@section('title', $quiz->title)
@section('page_title', 'Quiz')

@section('content')
<div class="space-y-6">

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
        <span class="text-gray-900 font-medium truncate max-w-xs">{{ $quiz->title }}</span>
    </nav>

    {{-- Flash error --}}
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-cloak x-init="setTimeout(() => show = false, 5000)"
             class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button @click="show = false" class="text-red-400 hover:text-red-600 ml-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    {{-- Passed Banner --}}
    @if($attempts->isNotEmpty() && $attempts->first()->passed)
        <div class="bg-green-50 border border-green-200 rounded-2xl px-5 py-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-green-800">You Passed!</p>
                <p class="text-sm text-green-600">Congratulations! You have successfully passed this quiz.</p>
            </div>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-2xl font-bold text-gray-900">{{ $quiz->title }}</h2>
                @php
                    $quizType = $quiz->type ?? ($quiz->is_required ? 'graded' : 'practice');
                    $isGraded = strtolower($quizType) === 'graded';
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                    {{ $isGraded ? 'bg-brand-50 text-brand-700 border border-brand-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                    {{ $isGraded ? 'Graded' : 'Practice' }}
                </span>
            </div>
            @if($quiz->course)
                <p class="text-sm text-gray-500 mt-1">{{ $quiz->course->title }}</p>
            @endif
        </div>
    </div>

    {{-- Two-column layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Info + Attempts (2/3 width) --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Info Card --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Quiz Information</h3>
                </div>

                @if($quiz->description)
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $quiz->description }}</p>
                    </div>
                @endif

                <div class="p-5 grid grid-cols-2 sm:grid-cols-3 gap-4">

                    {{-- Pass Score --}}
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Pass Score</span>
                        <div class="flex items-center gap-1.5">
                            <div class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="font-semibold text-gray-900">{{ $quiz->pass_score }}%</span>
                        </div>
                    </div>

                    {{-- Time Limit --}}
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Time Limit</span>
                        <div class="flex items-center gap-1.5">
                            <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="font-semibold text-gray-900">
                                {{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . ' min' : 'No limit' }}
                            </span>
                        </div>
                    </div>

                    {{-- Max Attempts --}}
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Max Attempts</span>
                        <div class="flex items-center gap-1.5">
                            <div class="w-7 h-7 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </div>
                            <span class="font-semibold text-gray-900">{{ $quiz->max_attempts }}</span>
                        </div>
                    </div>

                    {{-- Question Count --}}
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Questions</span>
                        <div class="flex items-center gap-1.5">
                            <div class="w-7 h-7 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="font-semibold text-gray-900">{{ $quiz->questions->count() }}</span>
                        </div>
                    </div>

                    {{-- Quiz Type --}}
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Quiz Type</span>
                        <div class="flex items-center gap-1.5">
                            <div class="w-7 h-7 {{ $isGraded ? 'bg-brand-50' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 {{ $isGraded ? 'text-brand-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <span class="font-semibold text-gray-900">{{ $isGraded ? 'Graded' : 'Practice' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Attempts History --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Attempt History</h3>
                    <span class="text-xs text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full font-medium">
                        {{ $attempts->count() }} / {{ $quiz->max_attempts }}
                    </span>
                </div>

                @if($attempts->isEmpty())
                    <div class="px-5 py-10 text-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">No attempts yet. Start the quiz to begin.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Attempt #</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Score</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($attempts as $attempt)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-5 py-3.5 font-medium text-gray-900">#{{ $attempt->attempt_number }}</td>
                                        <td class="px-5 py-3.5">
                                            <span class="font-semibold {{ $attempt->passed ? 'text-green-700' : 'text-red-600' }}">
                                                {{ number_format($attempt->score_percent, 1) }}%
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            @if($attempt->passed)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Passed
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-600">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Failed
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-500">
                                            {{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y') : $attempt->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-5 py-3.5">
                                            @if($attempt->completed_at)
                                                <a href="{{ route('student.quiz.result', $attempt) }}"
                                                   class="text-brand-600 hover:text-brand-700 text-xs font-medium hover:underline">
                                                    View Result
                                                </a>
                                            @else
                                                <span class="text-yellow-600 text-xs font-medium">In Progress</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right: Action Card (1/3 width) --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden sticky top-6">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Ready to Begin?</h3>
                </div>
                <div class="p-5 space-y-5">

                    {{-- Score requirement reminder --}}
                    <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Required to pass</span>
                            <span class="font-semibold text-gray-900">{{ $quiz->pass_score }}%</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Total questions</span>
                            <span class="font-semibold text-gray-900">{{ $quiz->questions->count() }}</span>
                        </div>
                        @if($quiz->time_limit_minutes)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Time limit</span>
                                <span class="font-semibold text-gray-900">{{ $quiz->time_limit_minutes }} minutes</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Attempts used</span>
                            <span class="font-semibold text-gray-900">{{ $attempts->count() }} of {{ $quiz->max_attempts }}</span>
                        </div>
                    </div>

                    @php
                        $attemptsRemaining = $quiz->max_attempts - $attempts->count();
                        $maxReached = $attemptsRemaining <= 0;
                    @endphp

                    {{-- Remaining attempts indicator --}}
                    @if(!$maxReached)
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>
                                <strong class="text-gray-900">{{ $attemptsRemaining }}</strong>
                                attempt{{ $attemptsRemaining !== 1 ? 's' : '' }} remaining
                            </span>
                        </div>
                    @endif

                    {{-- Start / Disabled button --}}
                    @if($maxReached)
                        <div class="space-y-3">
                            <button disabled
                                    class="w-full bg-gray-100 text-gray-400 font-semibold py-3 px-4 rounded-xl cursor-not-allowed text-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                Start Quiz
                            </button>
                            <p class="text-xs text-center text-red-500 font-medium">Maximum attempts reached</p>
                        </div>
                    @else
                        <form action="{{ route('student.quiz.start', $quiz) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 px-4 rounded-xl transition-colors text-sm flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $attempts->isEmpty() ? 'Start Quiz' : 'Retake Quiz' }}
                            </button>
                        </form>
                    @endif

                    {{-- Back to course --}}
                    @if($enrollment)
                        <a href="{{ route('student.learn', $enrollment->course->slug) }}"
                           class="block w-full text-center border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-4 rounded-xl transition-colors text-sm">
                            Back to Course
                        </a>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
