@extends('layouts.admin')

@section('title', 'Quizzes — ' . $course->title)

@section('content')
<div class="space-y-5">

    {{-- ═══════════════════════════════════════════════════════════════
         PAGE HEADER + BREADCRUMB
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
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
                <span class="text-gray-600 font-medium">Quizzes</span>
            </nav>

            <h2 class="text-2xl font-bold text-gray-900">Quizzes for: <span class="text-brand-600">{{ $course->title }}</span></h2>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $quizzes->count() }} {{ Str::plural('quiz', $quizzes->count()) }} attached to this course
            </p>
        </div>

        <a href="{{ route('admin.courses.quizzes.create', $course->id) }}"
           class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white
                  text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm shrink-0 self-start">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Quiz
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         QUIZZES TABLE
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        @if($quizzes->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="admin-table w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase tracking-wide">
                        <tr>
                            <th class="px-5 py-3.5 text-left font-medium">Title</th>
                            <th class="px-4 py-3.5 text-center font-medium">Type</th>
                            <th class="px-4 py-3.5 text-center font-medium">Pass Score</th>
                            <th class="px-4 py-3.5 text-center font-medium">Questions</th>
                            <th class="px-4 py-3.5 text-center font-medium">Max Attempts</th>
                            <th class="px-5 py-3.5 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($quizzes as $quiz)
                            <tr class="hover:bg-gray-50 transition-colors">

                                {{-- Title --}}
                                <td class="px-5 py-3.5">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $quiz->title }}</p>
                                        @if($quiz->description)
                                            <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $quiz->description }}</p>
                                        @endif
                                    </div>
                                </td>

                                {{-- Type badge --}}
                                <td class="px-4 py-3.5 text-center">
                                    @php
                                        $typeBadge = $quiz->type === 'graded'
                                            ? 'bg-purple-100 text-purple-700'
                                            : 'bg-blue-100 text-blue-700';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeBadge }}">
                                        {{ ucfirst($quiz->type) }}
                                    </span>
                                </td>

                                {{-- Pass Score --}}
                                <td class="px-4 py-3.5 text-center">
                                    <span class="font-semibold text-gray-800">{{ $quiz->pass_score }}%</span>
                                </td>

                                {{-- Questions count --}}
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 text-xs font-semibold text-gray-700">
                                        {{ $quiz->questions_count ?? $quiz->questions->count() }}
                                    </span>
                                </td>

                                {{-- Max Attempts --}}
                                <td class="px-4 py-3.5 text-center">
                                    <span class="text-gray-700">{{ $quiz->max_attempts }}</span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1">

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.quizzes.edit', $quiz->id) }}"
                                           class="p-1.5 text-gray-400 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-colors"
                                           title="Edit quiz">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        {{-- Delete --}}
                                        <form method="POST"
                                              action="{{ route('admin.courses.quizzes.destroy', [$course->id, $quiz->id]) }}"
                                              onsubmit="return confirm('Delete \'{{ addslashes($quiz->title) }}\'? This will also remove all questions and student attempts. This cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Delete quiz">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @else
            {{-- Empty state --}}
            <div class="px-5 py-16 text-center">
                <div class="flex flex-col items-center gap-4 text-gray-400">
                    <div class="w-16 h-16 rounded-2xl bg-brand-50 flex items-center justify-center">
                        <svg class="w-8 h-8 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-base font-semibold text-gray-700">No quizzes yet</p>
                        <p class="text-sm text-gray-400 mt-1">Add your first quiz to start testing students on this course.</p>
                    </div>
                    <a href="{{ route('admin.courses.quizzes.create', $course->id) }}"
                       class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white
                              text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add First Quiz
                    </a>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
