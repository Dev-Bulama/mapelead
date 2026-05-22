@extends('layouts.admin')

@section('title', 'Create Quiz')

@section('content')
<div class="space-y-5" x-data>

    {{-- ═══════════════════════════════════════════════════════════════
         PAGE HEADER + BREADCRUMB
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="flex items-start justify-between gap-3">
        <div>
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-1.5 text-xs text-gray-400 mb-1.5">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-brand-600 transition-colors">Dashboard</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('admin.courses.index') }}" class="hover:text-brand-600 transition-colors">Courses</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('admin.courses.show', $course->id) }}" class="hover:text-brand-600 transition-colors truncate max-w-[160px]">
                    {{ $course->title }}
                </a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('admin.courses.quizzes.index', $course->id) }}" class="hover:text-brand-600 transition-colors">Quizzes</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-600 font-medium">Create</span>
            </nav>

            <h2 class="text-2xl font-bold text-gray-900">Create Quiz</h2>
            <p class="text-sm text-gray-500 mt-0.5">for <span class="font-medium text-gray-700">{{ $course->title }}</span></p>
        </div>

        <a href="{{ route('admin.courses.quizzes.index', $course->id) }}"
           class="flex items-center gap-2 text-sm text-gray-600 bg-white border border-gray-200 px-4 py-2 rounded-lg hover:text-gray-900 hover:border-gray-300 transition-colors shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Quizzes
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         FORM
    ═══════════════════════════════════════════════════════════════ --}}
    <form action="{{ route('admin.courses.quizzes.store', $course->id) }}" method="POST" class="space-y-5">
        @csrf

        {{-- Basic Details --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
            <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide border-b border-gray-100 pb-3">
                Quiz Details
            </h3>

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="title"
                       name="title"
                       value="{{ old('title') }}"
                       required
                       placeholder="e.g. Module 1 Assessment"
                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                <textarea id="description"
                          name="description"
                          rows="3"
                          placeholder="Brief description of what this quiz covers…"
                          class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm
                                 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 resize-none transition">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Type --}}
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Quiz Type <span class="text-red-500">*</span>
                </label>
                <select id="type"
                        name="type"
                        required
                        class="w-full sm:w-64 border border-gray-200 rounded-lg px-4 py-2.5 text-sm bg-white
                               focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                    <option value="practice" {{ old('type', 'practice') === 'practice' ? 'selected' : '' }}>
                        Practice — unscored, for learning
                    </option>
                    <option value="graded" {{ old('type') === 'graded' ? 'selected' : '' }}>
                        Graded — counted toward completion
                    </option>
                </select>
                @error('type')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Scoring & Limits --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
            <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide border-b border-gray-100 pb-3">
                Scoring &amp; Limits
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

                {{-- Pass Score --}}
                <div>
                    <label for="pass_score" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Pass Score (%) <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           id="pass_score"
                           name="pass_score"
                           value="{{ old('pass_score', 70) }}"
                           min="0"
                           max="100"
                           required
                           class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                    @error('pass_score')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Max Attempts --}}
                <div>
                    <label for="max_attempts" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Max Attempts <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           id="max_attempts"
                           name="max_attempts"
                           value="{{ old('max_attempts', 3) }}"
                           min="1"
                           required
                           class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                    @error('max_attempts')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Time Limit --}}
                <div>
                    <label for="time_limit_minutes" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Time Limit (minutes)
                    </label>
                    <input type="number"
                           id="time_limit_minutes"
                           name="time_limit_minutes"
                           value="{{ old('time_limit_minutes') }}"
                           min="1"
                           placeholder="Leave blank for no limit"
                           class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                    @error('time_limit_minutes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Options / Toggles --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
            <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide border-b border-gray-100 pb-3">
                Options
            </h3>

            {{-- Shuffle Questions --}}
            <label class="flex items-center justify-between gap-4 py-1 cursor-pointer group">
                <div>
                    <p class="text-sm font-medium text-gray-800 group-hover:text-brand-600 transition-colors">Shuffle Questions</p>
                    <p class="text-xs text-gray-400 mt-0.5">Randomise the order of questions for each attempt</p>
                </div>
                <div class="relative shrink-0">
                    <input type="hidden" name="shuffle_questions" value="0">
                    <input type="checkbox"
                           id="shuffle_questions"
                           name="shuffle_questions"
                           value="1"
                           {{ old('shuffle_questions') ? 'checked' : '' }}
                           class="sr-only peer">
                    <label for="shuffle_questions"
                           class="block w-10 h-6 rounded-full bg-gray-200 peer-checked:bg-brand-600 cursor-pointer transition-colors
                                  after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                  after:w-5 after:h-5 after:bg-white after:rounded-full after:shadow
                                  after:transition-transform peer-checked:after:translate-x-4"></label>
                </div>
            </label>

            {{-- Show Results --}}
            <label class="flex items-center justify-between gap-4 py-1 cursor-pointer group border-t border-gray-50 pt-4">
                <div>
                    <p class="text-sm font-medium text-gray-800 group-hover:text-brand-600 transition-colors">Show Results</p>
                    <p class="text-xs text-gray-400 mt-0.5">Display score and correct answers to students after submission</p>
                </div>
                <div class="relative shrink-0">
                    <input type="hidden" name="show_results" value="0">
                    <input type="checkbox"
                           id="show_results"
                           name="show_results"
                           value="1"
                           {{ old('show_results', true) ? 'checked' : '' }}
                           class="sr-only peer">
                    <label for="show_results"
                           class="block w-10 h-6 rounded-full bg-gray-200 peer-checked:bg-brand-600 cursor-pointer transition-colors
                                  after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                  after:w-5 after:h-5 after:bg-white after:rounded-full after:shadow
                                  after:transition-transform peer-checked:after:translate-x-4"></label>
                </div>
            </label>

            {{-- Required for Certificate --}}
            <label class="flex items-center justify-between gap-4 py-1 cursor-pointer group border-t border-gray-50 pt-4">
                <div>
                    <p class="text-sm font-medium text-gray-800 group-hover:text-brand-600 transition-colors">Required for Certificate</p>
                    <p class="text-xs text-gray-400 mt-0.5">Students must pass this quiz to receive a course certificate</p>
                </div>
                <div class="relative shrink-0">
                    <input type="hidden" name="is_required_for_certificate" value="0">
                    <input type="checkbox"
                           id="is_required_for_certificate"
                           name="is_required_for_certificate"
                           value="1"
                           {{ old('is_required_for_certificate') ? 'checked' : '' }}
                           class="sr-only peer">
                    <label for="is_required_for_certificate"
                           class="block w-10 h-6 rounded-full bg-gray-200 peer-checked:bg-brand-600 cursor-pointer transition-colors
                                  after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                  after:w-5 after:h-5 after:bg-white after:rounded-full after:shadow
                                  after:transition-transform peer-checked:after:translate-x-4"></label>
                </div>
            </label>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3 pb-2">
            <a href="{{ route('admin.courses.quizzes.index', $course->id) }}"
               class="text-sm text-gray-600 border border-gray-200 hover:border-gray-300 px-4 py-2 rounded-lg transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white
                           text-sm font-medium px-5 py-2 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Create Quiz
            </button>
        </div>
    </form>

</div>
@endsection
