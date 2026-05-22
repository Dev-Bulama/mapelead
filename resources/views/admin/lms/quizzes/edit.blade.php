@extends('layouts.admin')

@section('title', 'Edit Quiz — ' . $quiz->title)

@section('content')
<div class="space-y-5"
     x-data="{
         questionModal: false,
         options: [
             { option_text: '', is_correct: false },
             { option_text: '', is_correct: false }
         ],
         addOption() {
             this.options.push({ option_text: '', is_correct: false });
         },
         removeOption(index) {
             if (this.options.length > 2) {
                 this.options.splice(index, 1);
             }
         },
         resetModal() {
             this.options = [
                 { option_text: '', is_correct: false },
                 { option_text: '', is_correct: false }
             ];
             this.$nextTick(() => {
                 this.$refs.questionForm && this.$refs.questionForm.reset();
             });
         }
     }">

    {{-- ═══════════════════════════════════════════════════════════════
         PAGE HEADER + BREADCRUMB
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-gray-400 mb-1.5">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-brand-600 transition-colors">Dashboard</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('admin.courses.index') }}" class="hover:text-brand-600 transition-colors">Courses</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('admin.courses.quizzes.index', $quiz->course_id) }}" class="hover:text-brand-600 transition-colors">Quizzes</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-600 font-medium truncate max-w-[160px]">{{ $quiz->title }}</span>
            </nav>
            <h2 class="text-2xl font-bold text-gray-900">Edit Quiz</h2>
            <p class="text-sm text-gray-500 mt-0.5 truncate max-w-xl">{{ $quiz->title }}</p>
        </div>

        <a href="{{ route('admin.courses.quizzes.index', $quiz->course_id) }}"
           class="flex items-center gap-2 text-sm text-gray-600 bg-white border border-gray-200 px-4 py-2 rounded-lg hover:text-gray-900 hover:border-gray-300 transition-colors shrink-0 self-start">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Quizzes
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         TWO-PANEL LAYOUT
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

        {{-- ── LEFT PANEL: Quiz Settings ──────────────────────────── --}}
        <div class="xl:col-span-2 space-y-5">

            <form action="{{ route('admin.quizzes.update', $quiz->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Basic Details --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-4">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-100 pb-3">
                        Quiz Settings
                    </h3>

                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="title" name="title"
                               value="{{ old('title', $quiz->title) }}" required
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 resize-none transition">{{ old('description', $quiz->description) }}</textarea>
                        @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Type --}}
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Quiz Type <span class="text-red-500">*</span>
                        </label>
                        <select id="type" name="type" required
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                            <option value="practice" {{ old('type', $quiz->type) === 'practice' ? 'selected' : '' }}>Practice — unscored, for learning</option>
                            <option value="graded"   {{ old('type', $quiz->type) === 'graded'   ? 'selected' : '' }}>Graded — counted toward completion</option>
                        </select>
                        @error('type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Scoring & Limits --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-4">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-100 pb-3">
                        Scoring &amp; Limits
                    </h3>

                    {{-- Pass Score --}}
                    <div>
                        <label for="pass_score" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Pass Score (%) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="pass_score" name="pass_score"
                               value="{{ old('pass_score', $quiz->pass_score) }}" min="0" max="100" required
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                        @error('pass_score')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Max Attempts --}}
                    <div>
                        <label for="max_attempts" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Max Attempts <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="max_attempts" name="max_attempts"
                               value="{{ old('max_attempts', $quiz->max_attempts) }}" min="1" required
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                        @error('max_attempts')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Time Limit --}}
                    <div>
                        <label for="time_limit_minutes" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Time Limit (minutes)
                        </label>
                        <input type="number" id="time_limit_minutes" name="time_limit_minutes"
                               value="{{ old('time_limit_minutes', $quiz->time_limit_minutes) }}" min="1"
                               placeholder="Leave blank for no limit"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                        @error('time_limit_minutes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Toggle Options --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-0 divide-y divide-gray-50">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide pb-3">Options</h3>

                    {{-- Shuffle --}}
                    <div class="flex items-center justify-between py-3.5 cursor-pointer group">
                        <div>
                            <p class="text-sm font-medium text-gray-800">Shuffle Questions</p>
                            <p class="text-xs text-gray-400 mt-0.5">Randomise order per attempt</p>
                        </div>
                        <div class="relative shrink-0">
                            <input type="hidden" name="shuffle_questions" value="0">
                            <input type="checkbox" id="shuffle_questions" name="shuffle_questions" value="1"
                                   {{ old('shuffle_questions', $quiz->shuffle_questions) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <label for="shuffle_questions"
                                   class="block w-10 h-6 rounded-full bg-gray-200 peer-checked:bg-brand-600 cursor-pointer transition-colors
                                          after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                          after:w-5 after:h-5 after:bg-white after:rounded-full after:shadow
                                          after:transition-transform peer-checked:after:translate-x-4"></label>
                        </div>
                    </div>

                    {{-- Show Results --}}
                    <div class="flex items-center justify-between py-3.5 cursor-pointer group">
                        <div>
                            <p class="text-sm font-medium text-gray-800">Show Results</p>
                            <p class="text-xs text-gray-400 mt-0.5">Display score after submission</p>
                        </div>
                        <div class="relative shrink-0">
                            <input type="hidden" name="show_results" value="0">
                            <input type="checkbox" id="show_results" name="show_results" value="1"
                                   {{ old('show_results', $quiz->show_results) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <label for="show_results"
                                   class="block w-10 h-6 rounded-full bg-gray-200 peer-checked:bg-brand-600 cursor-pointer transition-colors
                                          after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                          after:w-5 after:h-5 after:bg-white after:rounded-full after:shadow
                                          after:transition-transform peer-checked:after:translate-x-4"></label>
                        </div>
                    </div>

                    {{-- Certificate Required --}}
                    <div class="flex items-center justify-between py-3.5 cursor-pointer group">
                        <div>
                            <p class="text-sm font-medium text-gray-800">Required for Certificate</p>
                            <p class="text-xs text-gray-400 mt-0.5">Must pass to earn certificate</p>
                        </div>
                        <div class="relative shrink-0">
                            <input type="hidden" name="is_required_for_certificate" value="0">
                            <input type="checkbox" id="is_required_for_certificate" name="is_required_for_certificate" value="1"
                                   {{ old('is_required_for_certificate', $quiz->is_required_for_certificate) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <label for="is_required_for_certificate"
                                   class="block w-10 h-6 rounded-full bg-gray-200 peer-checked:bg-brand-600 cursor-pointer transition-colors
                                          after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                          after:w-5 after:h-5 after:bg-white after:rounded-full after:shadow
                                          after:transition-transform peer-checked:after:translate-x-4"></label>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-end gap-3">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- ── RIGHT PANEL: Questions Builder ─────────────────────── --}}
        <div class="xl:col-span-3 space-y-4">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

                {{-- Panel Header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-sm">Questions</h3>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $quiz->questions->count() }} {{ Str::plural('question', $quiz->questions->count()) }}</p>
                    </div>
                    <button @click="questionModal = true; resetModal()"
                            type="button"
                            class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-medium px-3.5 py-2 rounded-lg transition-colors shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Question
                    </button>
                </div>

                {{-- Questions List --}}
                @if($quiz->questions->isEmpty())
                    <div class="px-5 py-14 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-brand-50 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">No questions yet</p>
                        <p class="text-xs text-gray-400 mt-1 mb-4">Click "Add Question" to build your quiz.</p>
                        <button @click="questionModal = true; resetModal()" type="button"
                                class="inline-flex items-center gap-1.5 text-xs font-medium text-brand-600 border border-brand-200 px-3 py-1.5 rounded-lg hover:bg-brand-50 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add First Question
                        </button>
                    </div>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach($quiz->questions as $i => $question)
                            <li class="px-5 py-4 flex items-start gap-4 hover:bg-gray-50 transition-colors group">
                                {{-- Number --}}
                                <span class="flex-shrink-0 w-7 h-7 rounded-full bg-brand-50 text-brand-700 text-xs font-bold flex items-center justify-center mt-0.5">
                                    {{ $i + 1 }}
                                </span>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 line-clamp-2">{{ $question->question_text }}</p>
                                    <div class="flex items-center gap-3 mt-1.5">
                                        {{-- Type badge --}}
                                        @php
                                            $qTypeBadge = match($question->type) {
                                                'multiple_choice' => 'bg-blue-100 text-blue-700',
                                                'true_false'      => 'bg-teal-100 text-teal-700',
                                                'short_answer'    => 'bg-orange-100 text-orange-700',
                                                default           => 'bg-gray-100 text-gray-600',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $qTypeBadge }}">
                                            {{ ucwords(str_replace('_', ' ', $question->type)) }}
                                        </span>
                                        <span class="text-xs text-gray-400">{{ $question->points }} {{ Str::plural('pt', $question->points) }}</span>
                                        @if($question->options->count())
                                            <span class="text-xs text-gray-400">{{ $question->options->count() }} options</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Delete --}}
                                <form method="POST"
                                      action="{{ route('admin.quizzes.questions.destroy', [$quiz->id, $question->id]) }}"
                                      onsubmit="return confirm('Delete this question? This cannot be undone.')"
                                      class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete question">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         ADD QUESTION MODAL
    ═══════════════════════════════════════════════════════════════ --}}
    <div x-show="questionModal"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-start justify-center px-4 py-8 bg-black/60 overflow-y-auto"
         @keydown.escape.window="questionModal = false">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-auto"
             @click.outside="questionModal = false">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h4 class="text-base font-semibold text-gray-900">Add Question</h4>
                <button @click="questionModal = false" type="button"
                        class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Form --}}
            <form x-ref="questionForm"
                  method="POST"
                  action="{{ route('admin.quizzes.questions.store', $quiz->id) }}"
                  class="px-6 py-5 space-y-5">
                @csrf

                {{-- Question Text --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Question Text <span class="text-red-500">*</span>
                    </label>
                    <textarea name="question_text" rows="3" required
                              placeholder="Enter the question…"
                              class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 resize-none transition"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Type --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type" required
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="true_false">True / False</option>
                            <option value="short_answer">Short Answer</option>
                        </select>
                    </div>

                    {{-- Points --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Points <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="points" value="1" min="1" required
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                    </div>
                </div>

                {{-- Explanation (nullable) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Explanation <span class="text-xs text-gray-400 font-normal">(shown after submission)</span>
                    </label>
                    <textarea name="explanation" rows="2"
                              placeholder="Optional explanation for the correct answer…"
                              class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 resize-none transition"></textarea>
                </div>

                {{-- Options Builder --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-700">
                            Answer Options
                        </label>
                        <button type="button"
                                @click="addOption()"
                                class="inline-flex items-center gap-1 text-xs font-medium text-brand-600 hover:text-brand-700 border border-brand-200 hover:border-brand-400 px-2.5 py-1 rounded-lg transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Option
                        </button>
                    </div>

                    <div class="space-y-2">
                        <template x-for="(option, idx) in options" :key="idx">
                            <div class="flex items-center gap-2">
                                {{-- Correct Checkbox --}}
                                <input type="checkbox"
                                       :name="'options[' + idx + '][is_correct]'"
                                       value="1"
                                       x-model="option.is_correct"
                                       class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 shrink-0"
                                       :title="'Mark as correct answer'">

                                {{-- Option Text --}}
                                <input type="text"
                                       :name="'options[' + idx + '][option_text]'"
                                       x-model="option.option_text"
                                       :placeholder="'Option ' + (idx + 1)"
                                       required
                                       class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">

                                {{-- Remove --}}
                                <button type="button"
                                        @click="removeOption(idx)"
                                        :disabled="options.length <= 2"
                                        class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-30 disabled:cursor-not-allowed shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    <p class="text-xs text-gray-400">Check the box next to the correct answer(s).</p>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="questionModal = false"
                            class="text-sm text-gray-600 border border-gray-200 hover:border-gray-300 px-4 py-2 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Question
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
