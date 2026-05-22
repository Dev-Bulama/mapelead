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

<div class="space-y-6">

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
                        <span class="text-gray-500">Enrolled</span>
                        <span class="font-medium text-gray-900">{{ $enrollmentCount }} students</span>
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
                        <span class="text-gray-500">Total Modules</span>
                        <span class="font-medium text-gray-900">{{ $modules->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Lessons</span>
                        <span class="font-medium text-gray-900">{{ $totalLessons }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Duration</span>
                        <span class="font-medium text-gray-900">
                            @if($totalDuration >= 60)
                                {{ floor($totalDuration / 60) }}h {{ $totalDuration % 60 }}m
                            @else
                                {{ $totalDuration }}m
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Students</span>
                        <span class="font-medium text-gray-900">{{ $enrollmentCount }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900">Curriculum</h3>
                    <p class="text-xs text-gray-400 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5">Manage curriculum in Admin Panel</p>
                </div>

                @if($modules->isEmpty())
                <div class="text-center py-10">
                    <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="mt-3 text-sm text-gray-500">No curriculum added yet.</p>
                </div>
                @else
                <div class="space-y-3" x-data="{ open: {} }">
                    @foreach($modules as $index => $module)
                    <div class="border border-gray-100 rounded-xl overflow-hidden">
                        <button type="button"
                            @click="open[{{ $index }}] = !open[{{ $index }}]"
                            class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 transition-colors text-left">
                            <div class="flex items-center gap-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-brand-100 text-brand-600 rounded-full flex items-center justify-center text-xs font-bold">{{ $index + 1 }}</span>
                                <span class="font-medium text-gray-900 text-sm">{{ $module->title }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-500">{{ $module->lessons->count() }} lesson{{ $module->lessons->count() !== 1 ? 's' : '' }}</span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open[{{ $index }}] ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>

                        <div x-show="open[{{ $index }}]" x-collapse class="divide-y divide-gray-50">
                            @forelse($module->lessons->sortBy('sort_order') as $lesson)
                            <div class="flex items-center gap-3 px-4 py-3">
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
                                    @if($lesson->is_free)
                                        <span class="text-xs bg-green-50 text-green-600 border border-green-100 px-1.5 py-0.5 rounded">Free</span>
                                    @endif
                                    @if($lesson->duration_minutes)
                                        <span class="text-xs text-gray-400">{{ $lesson->duration_minutes }}m</span>
                                    @endif
                                    <span class="text-xs text-gray-400 capitalize">{{ $lesson->type }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="px-4 py-3 text-sm text-gray-400 italic">No lessons in this module.</div>
                            @endforelse
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

</div>
@endsection
