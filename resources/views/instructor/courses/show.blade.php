@extends('layouts.student')

@section('title', 'Course: ' . $course->title)
@section('page_title', 'Course: ' . $course->title)

@section('content')
@php
    $modules = $course->modules()->orderBy('sort_order')->with('lessons')->get();
    $totalLessons = $modules->sum(fn($m) => $m->lessons->count());
    $totalDuration = $modules->sum(fn($m) => $m->lessons->sum('duration_minutes'));
    $enrollmentCount = $course->enrollments()->count();
@endphp

<div class="space-y-6"
     x-data="{
        addModuleOpen: false,
        editModuleOpen: false,
        editModule: {},
        addLessonOpen: false,
        addLessonModuleId: null,
        addLessonModuleTitle: '',
        editLessonOpen: false,
        editLesson: {},
        editLessonModuleId: null,
     }">

    {{-- Header --}}
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('instructor.courses.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Courses
        </a>
        <span class="text-gray-300">/</span>
        <span class="text-sm text-gray-700 font-medium truncate max-w-xs">{{ $course->title }}</span>
        <div class="ml-auto flex items-center gap-2">
            @if($course->status === 'published')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Published</span>
            @elseif($course->status === 'rejected')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Rejected</span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Draft</span>
            @endif
            <a href="{{ route('instructor.courses.edit', $course->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Course
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left sidebar --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                @if($course->thumbnail)
                    <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full aspect-video object-cover">
                @else
                    <div class="w-full aspect-video bg-gradient-to-br from-brand-50 to-brand-100 flex items-center justify-center">
                        <svg class="w-16 h-16 text-brand-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.277A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />
                        </svg>
                    </div>
                @endif
                <div class="p-5 space-y-3 text-sm">
                    @if($course->category)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Category</span>
                        <span class="font-medium text-gray-900">{{ $course->category->name }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500">Type</span>
                        <span class="font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $course->type ?? 'Online') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Level</span>
                        <span class="font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $course->level ?? 'All Levels') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Price</span>
                        <span class="font-medium text-gray-900">
                            @if($course->is_free)
                                <span class="text-green-600">Free</span>
                            @else
                                ₦{{ number_format($course->price, 2) }}
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Students</span>
                        <span class="font-medium text-gray-900">{{ $enrollmentCount }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created</span>
                        <span class="font-medium text-gray-900">{{ $course->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Course Stats</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Modules</span>
                        <span class="font-medium text-gray-900">{{ $modules->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Lessons</span>
                        <span class="font-medium text-gray-900">{{ $totalLessons }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Duration</span>
                        <span class="font-medium text-gray-900">
                            @if($totalDuration >= 60)
                                {{ floor($totalDuration / 60) }}h {{ $totalDuration % 60 }}m
                            @else
                                {{ $totalDuration }}m
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Curriculum management --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900">Curriculum</h3>
                    <button @click="addModuleOpen = true"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-medium rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Module
                    </button>
                </div>

                @if($modules->isEmpty())
                <div class="text-center py-10">
                    <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="mt-3 text-sm text-gray-500">No modules yet. Click <strong>Add Module</strong> to get started.</p>
                </div>
                @else
                <div class="space-y-3">
                    @foreach($modules as $index => $module)
                    <div class="border border-gray-100 rounded-xl overflow-hidden" x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }">
                        {{-- Module header --}}
                        <div class="flex items-center px-4 py-3 bg-gray-50">
                            <button type="button" @click="open = !open"
                                class="flex items-center gap-3 flex-1 text-left min-w-0">
                                <span class="flex-shrink-0 w-6 h-6 bg-brand-100 text-brand-600 rounded-full flex items-center justify-center text-xs font-bold">{{ $index + 1 }}</span>
                                <span class="font-medium text-gray-900 text-sm truncate">{{ $module->title }}</span>
                                <span class="text-xs text-gray-400 flex-shrink-0">{{ $module->lessons->count() }} lesson{{ $module->lessons->count() !== 1 ? 's' : '' }}</span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform flex-shrink-0 ml-auto" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="flex items-center gap-1 ml-3 flex-shrink-0">
                                <button type="button"
                                    @click="editModule = { id: {{ $module->id }}, title: '{{ addslashes($module->title) }}', description: '{{ addslashes($module->description ?? '') }}', sort_order: {{ $module->sort_order }}, is_free_preview: {{ $module->is_free_preview ? 'true' : 'false' }} }; editModuleOpen = true"
                                    class="p-1.5 text-gray-400 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <form method="POST" action="{{ route('instructor.courses.modules.destroy', [$course->id, $module->id]) }}"
                                      onsubmit="return confirm('Delete this module and all its lessons?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Lessons list --}}
                        <div x-show="open" x-collapse class="divide-y divide-gray-50">
                            @forelse($module->lessons->sortBy('sort_order') as $lesson)
                            <div class="flex items-center gap-3 px-4 py-2.5">
                                <div class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center
                                    {{ $lesson->type === 'video' ? 'bg-blue-50' : ($lesson->type === 'quiz' ? 'bg-orange-50' : 'bg-gray-50') }}">
                                    @if($lesson->type === 'video')
                                        <svg class="w-3.5 h-3.5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                        </svg>
                                    @elseif($lesson->type === 'quiz')
                                        <svg class="w-3.5 h-3.5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-800 truncate">{{ $lesson->title }}</p>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    @if($lesson->is_free_preview)
                                        <span class="text-xs bg-green-50 text-green-600 border border-green-100 px-1.5 py-0.5 rounded">Free</span>
                                    @endif
                                    @if($lesson->duration_minutes)
                                        <span class="text-xs text-gray-400">{{ $lesson->duration_minutes }}m</span>
                                    @endif
                                    <span class="text-xs text-gray-400 capitalize">{{ $lesson->type }}</span>
                                    <button type="button"
                                        @click="editLesson = {
                                            id: {{ $lesson->id }},
                                            title: '{{ addslashes($lesson->title) }}',
                                            type: '{{ $lesson->type }}',
                                            video_url: '{{ addslashes($lesson->video_url ?? '') }}',
                                            video_provider: '{{ $lesson->video_provider ?? '' }}',
                                            duration_minutes: '{{ $lesson->duration_minutes ?? '' }}',
                                            content: '{{ addslashes(\Illuminate\Support\Str::limit($lesson->content ?? '', 500)) }}',
                                            sort_order: {{ $lesson->sort_order }},
                                            is_free_preview: {{ $lesson->is_free_preview ? 'true' : 'false' }},
                                            is_published: {{ $lesson->is_published ? 'true' : 'false' }}
                                        }; editLessonModuleId = {{ $module->id }}; editLessonOpen = true"
                                        class="p-1 text-gray-400 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('instructor.courses.lessons.destroy', [$course->id, $lesson->id]) }}"
                                          onsubmit="return confirm('Delete this lesson?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @empty
                            <div class="px-4 py-3 text-sm text-gray-400 italic">No lessons yet.</div>
                            @endforelse

                            {{-- Add lesson row --}}
                            <div class="px-4 py-2.5">
                                <button type="button"
                                    @click="addLessonModuleId = {{ $module->id }}; addLessonModuleTitle = '{{ addslashes($module->title) }}'; addLessonOpen = true"
                                    class="inline-flex items-center gap-1.5 text-xs text-brand-600 hover:text-brand-700 font-medium">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Lesson
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            @if($course->description)
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Description</h3>
                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $course->description }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ══ ADD MODULE MODAL ═══════════════════════════════════════════════════ --}}
    <div x-show="addModuleOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background:rgba(0,0,0,0.4);">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6"
             @click.outside="addModuleOpen = false">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Add Module</h3>
                <button @click="addModuleOpen = false" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('instructor.courses.modules.store', $course->id) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Module Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                        placeholder="e.g. Introduction to the Course">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"
                        placeholder="Optional overview of this module..."></textarea>
                </div>
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="hidden" name="is_free_preview" value="0">
                    <input type="checkbox" name="is_free_preview" value="1" class="rounded border-gray-300 text-brand-600">
                    <span class="text-sm text-gray-700">Mark as free preview</span>
                </label>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="addModuleOpen = false"
                        class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl transition-colors">Add Module</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══ EDIT MODULE MODAL ══════════════════════════════════════════════════ --}}
    <div x-show="editModuleOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background:rgba(0,0,0,0.4);">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6"
             @click.outside="editModuleOpen = false">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Edit Module</h3>
                <button @click="editModuleOpen = false" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <template x-if="editModule.id">
                <form method="POST" :action="`{{ url('instructor/courses/' . $course->id . '/modules') }}/${editModule.id}`" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Module Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" :value="editModule.title" required
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2" :value="editModule.description"
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" :value="editModule.sort_order" min="0"
                            class="w-28 px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="hidden" name="is_free_preview" value="0">
                        <input type="checkbox" name="is_free_preview" value="1" :checked="editModule.is_free_preview" class="rounded border-gray-300 text-brand-600">
                        <span class="text-sm text-gray-700">Free preview</span>
                    </label>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="editModuleOpen = false"
                            class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl transition-colors">Save</button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    {{-- ══ ADD LESSON MODAL ═══════════════════════════════════════════════════ --}}
    <div x-show="addLessonOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
         style="background:rgba(0,0,0,0.4);">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 my-4"
             @click.outside="addLessonOpen = false">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Add Lesson</h3>
                    <p class="text-xs text-gray-400 mt-0.5" x-text="'Module: ' + addLessonModuleTitle"></p>
                </div>
                <button @click="addLessonOpen = false" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <template x-if="addLessonModuleId">
                <form method="POST" :action="`{{ url('instructor/courses/' . $course->id . '/modules') }}/${addLessonModuleId}/lessons`" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lesson Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                            placeholder="e.g. Welcome & Course Overview">
                    </div>
                    <div x-data="{ lessonType: 'video' }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                        <select name="type" x-model="lessonType"
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                            <option value="video">Video</option>
                            <option value="text">Text / Article</option>
                            <option value="quiz">Quiz</option>
                            <option value="assignment">Assignment</option>
                            <option value="live">Live Session</option>
                        </select>

                        <div x-show="lessonType === 'video'" class="mt-3 space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Video URL</label>
                                <input type="url" name="video_url"
                                    class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                                    placeholder="https://www.youtube.com/watch?v=...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Provider</label>
                                <select name="video_provider"
                                    class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                    <option value="">Select provider</option>
                                    <option value="youtube">YouTube</option>
                                    <option value="vimeo">Vimeo</option>
                                    <option value="bunny">Bunny.net</option>
                                    <option value="wistia">Wistia</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div x-show="lessonType !== 'video'" class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                            <textarea name="content" rows="4"
                                class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"
                                placeholder="Lesson content, instructions, or description..."></textarea>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" min="1"
                            class="w-28 px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                            placeholder="15">
                    </div>
                    <div class="flex gap-5">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="hidden" name="is_free_preview" value="0">
                            <input type="checkbox" name="is_free_preview" value="1" class="rounded border-gray-300 text-brand-600">
                            <span class="text-sm text-gray-700">Free preview</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="hidden" name="is_published" value="0">
                            <input type="checkbox" name="is_published" value="1" checked class="rounded border-gray-300 text-brand-600">
                            <span class="text-sm text-gray-700">Published</span>
                        </label>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="addLessonOpen = false"
                            class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl transition-colors">Add Lesson</button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    {{-- ══ EDIT LESSON MODAL ══════════════════════════════════════════════════ --}}
    <div x-show="editLessonOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
         style="background:rgba(0,0,0,0.4);">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 my-4"
             @click.outside="editLessonOpen = false">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Edit Lesson</h3>
                <button @click="editLessonOpen = false" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <template x-if="editLesson.id">
                <form method="POST" :action="`{{ url('instructor/courses/' . $course->id . '/lessons') }}/${editLesson.id}`" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lesson Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" :value="editLesson.title" required
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" :value="editLesson.type"
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                            <option value="video" :selected="editLesson.type === 'video'">Video</option>
                            <option value="text" :selected="editLesson.type === 'text'">Text / Article</option>
                            <option value="quiz" :selected="editLesson.type === 'quiz'">Quiz</option>
                            <option value="assignment" :selected="editLesson.type === 'assignment'">Assignment</option>
                            <option value="live" :selected="editLesson.type === 'live'">Live Session</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Video URL</label>
                        <input type="url" name="video_url" :value="editLesson.video_url"
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Video Provider</label>
                        <select name="video_provider"
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                            <option value="">None</option>
                            <option value="youtube" :selected="editLesson.video_provider === 'youtube'">YouTube</option>
                            <option value="vimeo" :selected="editLesson.video_provider === 'vimeo'">Vimeo</option>
                            <option value="bunny" :selected="editLesson.video_provider === 'bunny'">Bunny.net</option>
                            <option value="wistia" :selected="editLesson.video_provider === 'wistia'">Wistia</option>
                            <option value="other" :selected="editLesson.video_provider === 'other'">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                        <textarea name="content" rows="3" x-model="editLesson.content"
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Duration (min)</label>
                            <input type="number" name="duration_minutes" :value="editLesson.duration_minutes" min="1"
                                class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                            <input type="number" name="sort_order" :value="editLesson.sort_order" min="0"
                                class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>
                    <div class="flex gap-5">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="hidden" name="is_free_preview" value="0">
                            <input type="checkbox" name="is_free_preview" value="1" :checked="editLesson.is_free_preview" class="rounded border-gray-300 text-brand-600">
                            <span class="text-sm text-gray-700">Free preview</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="hidden" name="is_published" value="0">
                            <input type="checkbox" name="is_published" value="1" :checked="editLesson.is_published" class="rounded border-gray-300 text-brand-600">
                            <span class="text-sm text-gray-700">Published</span>
                        </label>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="editLessonOpen = false"
                            class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl transition-colors">Save Lesson</button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection
