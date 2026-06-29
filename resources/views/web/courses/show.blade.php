@extends('layouts.app')

@section('title', ($course->meta_title ?: $course->title) . ' — MapeLearn')
@section('meta_description', $course->meta_description ?: $course->short_description)
@section('meta_keywords', $course->meta_keywords ?? '')
@section('og_image', $course->thumbnail_url)

@section('content')

{{-- ═══════════════════════════════════════════════════════════════
     BREADCRUMB BAR
═══════════════════════════════════════════════════════════════ --}}
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5">
        <nav class="flex items-center gap-1.5 text-xs text-gray-500 flex-wrap">
            <a href="{{ route('home') }}" class="hover:text-brand-600 transition-colors font-medium">Home</a>
            <svg class="w-3 h-3 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('courses.index') }}" class="hover:text-brand-600 transition-colors font-medium">Courses</a>
            @if($course->category)
            <svg class="w-3 h-3 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('courses.category', $course->category->slug) }}" class="hover:text-brand-600 transition-colors font-medium">{{ $course->category->name }}</a>
            @endif
            <svg class="w-3 h-3 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-800 font-semibold truncate max-w-xs">{{ $course->title }}</span>
        </nav>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     COURSE HEADER BANNER (dark, matches hero aesthetic)
═══════════════════════════════════════════════════════════════ --}}
<div class="bg-gradient-to-br from-brand-950 via-brand-900 to-gray-900 py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:w-8/12">
            {{-- Category badge --}}
            @if($course->category)
            <a href="{{ route('courses.category', $course->category->slug) }}"
               class="inline-block bg-brand-600 bg-opacity-70 text-brand-100 text-xs font-semibold px-3 py-1 rounded-full mb-4 hover:bg-opacity-100 transition-colors">
                {{ $course->category->name }}
            </a>
            @endif

            <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight mb-4">
                {{ $course->title }}
            </h1>

            @if($course->short_description)
            <p class="text-brand-200 text-base leading-relaxed mb-5 max-w-2xl">{{ $course->short_description }}</p>
            @endif

            {{-- Meta row --}}
            <div class="flex flex-wrap items-center gap-4 mb-5 text-sm text-brand-300">
                {{-- Rating --}}
                <div class="flex items-center gap-1.5">
                    <span class="text-yellow-400 font-bold">{{ number_format($course->average_rating ?? 0, 1) }}</span>
                    <div class="flex items-center gap-0.5">
                        @for($s = 1; $s <= 5; $s++)
                        <svg class="w-3.5 h-3.5 {{ $s <= round($course->average_rating ?? 0) ? 'text-yellow-400 fill-current' : 'text-gray-600 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <span class="text-brand-400 text-xs">({{ number_format($course->total_reviews ?? 0) }} reviews)</span>
                </div>

                <span class="w-1 h-1 bg-brand-600 rounded-full"></span>
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    {{ number_format($course->total_students ?? 0) }} students enrolled
                </span>

                <span class="w-1 h-1 bg-brand-600 rounded-full"></span>
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Last updated {{ $course->updated_at->format('M Y') }}
                </span>

                <span class="w-1 h-1 bg-brand-600 rounded-full"></span>
                <span class="flex items-center gap-1 capitalize">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                    {{ $course->language ?? 'English' }}
                </span>
            </div>

            {{-- Instructor quick ref --}}
            @if($course->courseInstructors->count())
            <div class="flex flex-wrap items-center gap-4">
                <span class="text-brand-400 text-xs">Instructors:</span>
                @foreach($course->courseInstructors as $ci)
                @if($ci->instructor)
                <div class="flex items-center gap-2">
                    <img src="{{ $ci->instructor->avatar_url }}" alt="{{ $ci->instructor->full_name }}"
                         class="w-9 h-9 rounded-full object-cover border-2 border-brand-500">
                    <div>
                        <a href="#instructor" class="text-white text-sm font-semibold hover:text-brand-300 transition-colors">{{ $ci->instructor->full_name }}</a>
                        <span class="text-brand-400 text-xs ml-1">· {{ $ci->session_label }}</span>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @elseif($course->instructor)
            <div class="flex items-center gap-3">
                <img src="{{ $course->instructor->avatar_url }}" alt="{{ $course->instructor->full_name }}"
                     class="w-9 h-9 rounded-full object-cover border-2 border-brand-500">
                <div>
                    <span class="text-brand-400 text-xs">Created by </span>
                    <a href="#instructor" class="text-white text-sm font-semibold hover:text-brand-300 transition-colors">{{ $course->instructor->full_name }}</a>
                    @if($course->instructor->title)
                    <span class="text-brand-400 text-xs ml-1">· {{ $course->instructor->title }}</span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     MAIN TWO-COLUMN CONTENT
═══════════════════════════════════════════════════════════════ --}}
<div class="bg-gray-50 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- ─────────────────────────────────────────────────────
                 LEFT COLUMN — 8/12
            ───────────────────────────────────────────────────── --}}
            <div class="lg:w-8/12 space-y-8">

                {{-- WHAT YOU'LL LEARN --}}
                @if(!empty($course->what_you_learn) && count($course->what_you_learn))
                <section class="bg-white rounded-2xl border border-gray-200 p-7">
                    <h2 class="font-display font-bold text-gray-900 text-xl mb-5">What You'll Learn</h2>
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach($course->what_you_learn as $item)
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 bg-brand-100 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3 h-3 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="text-gray-700 text-sm leading-relaxed">{{ $item }}</span>
                        </div>
                        @endforeach
                    </div>
                </section>
                @endif

                {{-- REQUIREMENTS --}}
                @if(!empty($course->requirements) && count($course->requirements))
                <section class="bg-white rounded-2xl border border-gray-200 p-7">
                    <h2 class="font-display font-bold text-gray-900 text-xl mb-5">Requirements</h2>
                    <ul class="space-y-3">
                        @foreach($course->requirements as $req)
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 bg-brand-500 rounded-full mt-2 shrink-0"></div>
                            <span class="text-gray-700 text-sm leading-relaxed">{{ $req }}</span>
                        </li>
                        @endforeach
                    </ul>
                </section>
                @endif

                {{-- COURSE DESCRIPTION --}}
                @if($course->description)
                <section class="bg-white rounded-2xl border border-gray-200 p-7" x-data="{ expanded: false }">
                    <h2 class="font-display font-bold text-gray-900 text-xl mb-5">Course Description</h2>
                    <div class="relative">
                        <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed"
                             :class="expanded ? '' : 'max-h-48 overflow-hidden'"
                             style="line-height: 1.8;">
                            {!! nl2br(e($course->description)) !!}
                        </div>
                        {{-- Gradient fade --}}
                        <div x-show="!expanded"
                             class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-white to-transparent pointer-events-none">
                        </div>
                    </div>
                    <button @click="expanded = !expanded"
                            class="mt-3 flex items-center gap-1.5 text-brand-600 hover:text-brand-700 font-semibold text-sm transition-colors">
                        <span x-text="expanded ? 'Show Less' : 'Show More'"></span>
                        <svg class="w-4 h-4 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </section>
                @endif

                {{-- COURSE CURRICULUM --}}
                <section class="bg-white rounded-2xl border border-gray-200 p-7">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="font-display font-bold text-gray-900 text-xl">Course Curriculum</h2>
                        <div class="text-xs text-gray-500">
                            <span class="font-semibold text-gray-700">{{ $course->total_lessons ?? $course->lessons->count() }}</span> lessons ·
                            <span class="font-semibold text-gray-700">{{ $course->duration_hours ?? 0 }}h</span> total
                        </div>
                    </div>

                    @if($course->modules->count())
                    <div class="space-y-3" x-data="{ openModule: 0 }">
                        @foreach($course->modules as $moduleIndex => $module)
                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                            {{-- Module header --}}
                            <button @click="openModule === {{ $moduleIndex }} ? openModule = null : openModule = {{ $moduleIndex }}"
                                    class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-gray-50 transition-colors"
                                    :class="openModule === {{ $moduleIndex }} ? 'bg-brand-50 border-b border-brand-100' : ''">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-brand-100 flex items-center justify-center shrink-0 text-brand-700 font-bold text-sm">
                                        {{ $moduleIndex + 1 }}
                                    </div>
                                    <div class="min-w-0">
                                        <span class="font-semibold text-gray-900 text-sm block truncate">{{ $module->title }}</span>
                                        <span class="text-xs text-gray-500">{{ $module->lessons->count() }} {{ Str::plural('lesson', $module->lessons->count()) }}</span>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 shrink-0 ml-3 transition-transform"
                                     :class="openModule === {{ $moduleIndex }} ? 'rotate-180 text-brand-600' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            {{-- Lessons list --}}
                            <div x-show="openModule === {{ $moduleIndex }}" x-cloak>
                                @foreach($module->lessons as $lesson)
                                <div class="flex items-center gap-3 px-5 py-3 border-t border-gray-100 hover:bg-gray-50 transition-colors group">
                                    {{-- Lesson type icon --}}
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0
                                        {{ $lesson->type === 'video' ? 'bg-purple-100' : ($lesson->type === 'quiz' ? 'bg-yellow-100' : ($lesson->type === 'assignment' ? 'bg-orange-100' : 'bg-blue-100')) }}">
                                        @if($lesson->type === 'video')
                                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @elseif($lesson->type === 'quiz')
                                        <svg class="w-3.5 h-3.5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        @elseif($lesson->type === 'assignment')
                                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        @else
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        @endif
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <span class="text-sm text-gray-800 truncate block">{{ $lesson->title }}</span>
                                    </div>

                                    <div class="flex items-center gap-2.5 shrink-0">
                                        {{-- Free preview badge --}}
                                        @if($lesson->is_free_preview)
                                        <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">Preview</span>
                                        @elseif(!$isEnrolled)
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        @endif

                                        {{-- Duration --}}
                                        @if($lesson->duration_minutes)
                                        <span class="text-xs text-gray-400">{{ $lesson->duration_minutes < 60 ? $lesson->duration_minutes . 'm' : floor($lesson->duration_minutes/60) . 'h ' . ($lesson->duration_minutes % 60) . 'm' }}</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-400 text-sm">
                        Curriculum details will be available soon.
                    </div>
                    @endif
                </section>

                {{-- INSTRUCTOR BIO --}}
                @if($course->courseInstructors->count())
                <section id="instructor" class="bg-white rounded-2xl border border-gray-200 p-7">
                    <h2 class="font-display font-bold text-gray-900 text-xl mb-6">
                        {{ $course->courseInstructors->count() > 1 ? 'Your Instructors' : 'Your Instructor' }}
                    </h2>
                    <div class="space-y-8">
                    @foreach($course->courseInstructors as $ci)
                    @php $instr = $ci->instructor; @endphp
                    @if(!$instr) @continue @endif
                    <div class="flex flex-col sm:flex-row gap-6 {{ !$loop->last ? 'pb-8 border-b border-gray-100' : '' }}">
                        <div class="shrink-0 relative">
                            <img src="{{ $instr->avatar_url }}" alt="{{ $instr->full_name }}"
                                 class="w-24 h-24 rounded-2xl object-cover border-2 border-brand-100">
                            <span class="absolute -bottom-2 -right-2 text-lg" title="{{ $ci->session_label }}">{{ $ci->session_icon }}</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-start justify-between gap-3 mb-2">
                                <div>
                                    <h3 class="font-display font-bold text-gray-900 text-lg">{{ $instr->full_name }}</h3>
                                    <div class="flex items-center gap-2">
                                        @if($instr->title)
                                        <p class="text-brand-600 text-sm font-medium">{{ $instr->title }}</p>
                                        @endif
                                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">{{ $ci->session_label }} Session{{ $ci->session_time ? ' · ' . $ci->formatted_time : '' }}</span>
                                    </div>
                                </div>
                                @if($instr->is_verified)
                                <span class="inline-flex items-center gap-1 bg-brand-50 text-brand-700 text-xs font-semibold px-3 py-1 rounded-full">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                    Verified Instructor
                                </span>
                                @endif
                            </div>

                            <div class="flex flex-wrap gap-4 mb-4 text-sm text-gray-600">
                                <span class="flex items-center gap-1.5">
                                    <div class="flex items-center gap-0.5">
                                        @for($s = 1; $s <= 5; $s++)
                                        <svg class="w-3 h-3 {{ $s <= round($instr->average_rating ?? 0) ? 'text-yellow-400 fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                    <span class="font-medium">{{ number_format($instr->average_rating ?? 0, 1) }} rating</span>
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    {{ number_format($instr->total_students ?? 0) }} students
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    {{ $instr->total_courses ?? 0 }} {{ Str::plural('course', $instr->total_courses ?? 0) }}
                                </span>
                            </div>

                            @if($instr->description)
                            <div x-data="{ expanded: false }">
                                <p class="text-gray-600 text-sm leading-relaxed"
                                   :class="expanded ? '' : 'line-clamp-4'">
                                    {{ $instr->description }}
                                </p>
                                <button @click="expanded = !expanded"
                                        class="mt-2 text-brand-600 hover:text-brand-700 text-sm font-semibold transition-colors">
                                    <span x-text="expanded ? 'Show Less' : 'Read More'"></span>
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    </div>
                </section>
                @elseif($course->instructor)
                <section id="instructor" class="bg-white rounded-2xl border border-gray-200 p-7">
                    <h2 class="font-display font-bold text-gray-900 text-xl mb-6">Your Instructor</h2>
                    <div class="flex flex-col sm:flex-row gap-6">
                        <div class="shrink-0">
                            <img src="{{ $course->instructor->avatar_url }}" alt="{{ $course->instructor->full_name }}"
                                 class="w-24 h-24 rounded-2xl object-cover border-2 border-brand-100">
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-start justify-between gap-3 mb-2">
                                <div>
                                    <h3 class="font-display font-bold text-gray-900 text-lg">{{ $course->instructor->full_name }}</h3>
                                    @if($course->instructor->title)
                                    <p class="text-brand-600 text-sm font-medium">{{ $course->instructor->title }}</p>
                                    @endif
                                </div>
                                @if($course->instructor->is_verified)
                                <span class="inline-flex items-center gap-1 bg-brand-50 text-brand-700 text-xs font-semibold px-3 py-1 rounded-full">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                    Verified Instructor
                                </span>
                                @endif
                            </div>

                            <div class="flex flex-wrap gap-4 mb-4 text-sm text-gray-600">
                                <span class="flex items-center gap-1.5">
                                    <div class="flex items-center gap-0.5">
                                        @for($s = 1; $s <= 5; $s++)
                                        <svg class="w-3 h-3 {{ $s <= round($course->instructor->average_rating ?? 0) ? 'text-yellow-400 fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                    <span class="font-medium">{{ number_format($course->instructor->average_rating ?? 0, 1) }} rating</span>
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    {{ number_format($course->instructor->total_students ?? 0) }} students
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    {{ $course->instructor->total_courses ?? 0 }} {{ Str::plural('course', $course->instructor->total_courses ?? 0) }}
                                </span>
                            </div>

                            @if($course->instructor->description)
                            <div x-data="{ expanded: false }">
                                <p class="text-gray-600 text-sm leading-relaxed"
                                   :class="expanded ? '' : 'line-clamp-4'">
                                    {{ $course->instructor->description }}
                                </p>
                                <button @click="expanded = !expanded"
                                        class="mt-2 text-brand-600 hover:text-brand-700 text-sm font-semibold transition-colors">
                                    <span x-text="expanded ? 'Show Less' : 'Read More'"></span>
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </section>
                @endif

                {{-- STUDENT REVIEWS --}}
                <section id="reviews" class="bg-white rounded-2xl border border-gray-200 p-7">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="font-display font-bold text-gray-900 text-xl">Student Reviews</h2>
                        <span class="text-sm text-gray-500">{{ number_format($course->total_reviews ?? 0) }} {{ Str::plural('review', $course->total_reviews ?? 0) }}</span>
                    </div>

                    {{-- Rating overview --}}
                    <div class="flex flex-col sm:flex-row gap-6 mb-8 p-5 bg-gray-50 rounded-2xl">
                        <div class="text-center sm:w-32 shrink-0">
                            <div class="font-display text-6xl font-extrabold text-gray-900 leading-none">{{ number_format($course->average_rating ?? 0, 1) }}</div>
                            <div class="flex items-center justify-center gap-0.5 my-2">
                                @for($s = 1; $s <= 5; $s++)
                                <svg class="w-4 h-4 {{ $s <= round($course->average_rating ?? 0) ? 'text-yellow-400 fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <p class="text-xs text-gray-500">Course Rating</p>
                        </div>
                        <div class="flex-1 space-y-2">
                            @foreach([5, 4, 3, 2, 1] as $star)
                            @php
                                $count = $course->reviews->where('rating', $star)->count();
                                $pct = $course->total_reviews > 0 ? round(($count / $course->total_reviews) * 100) : 0;
                            @endphp
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-0.5 shrink-0">
                                    @for($s = 1; $s <= 5; $s++)
                                    <svg class="w-3 h-3 {{ $s <= $star ? 'text-yellow-400 fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                                <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-full bg-yellow-400 rounded-full progress-bar" style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 w-8 text-right">{{ $pct }}%</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Leave review (enrolled users only) --}}
                    @auth
                    @if($isEnrolled)
                    <div class="mb-7 p-5 bg-brand-50 border border-brand-100 rounded-2xl" x-data="{ rating: 0, hover: 0 }">
                        <h3 class="font-semibold text-gray-900 mb-3 text-sm">Leave a Review</h3>
                        <form action="{{ route('courses.review', $course->slug) }}" method="POST" class="space-y-3">
                            @csrf
                            <div class="flex items-center gap-1">
                                @for($s = 1; $s <= 5; $s++)
                                <button type="button"
                                        @click="rating = {{ $s }}"
                                        @mouseenter="hover = {{ $s }}"
                                        @mouseleave="hover = 0">
                                    <svg class="w-7 h-7 transition-colors cursor-pointer"
                                         :class="(hover || rating) >= {{ $s }} ? 'text-yellow-400 fill-current' : 'text-gray-300 fill-current'"
                                         viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                                @endfor
                                <input type="hidden" name="rating" :value="rating">
                            </div>
                            <input type="text" name="title" placeholder="Review headline (optional)" maxlength="120"
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                            <textarea name="body" rows="3" placeholder="Share your experience..." required
                                      class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none"></textarea>
                            <button type="submit" x-bind:disabled="rating === 0"
                                    :class="rating === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-brand-700'"
                                    class="bg-brand-600 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                                Submit Review
                            </button>
                        </form>
                    </div>
                    @endif
                    @endauth

                    {{-- Reviews list --}}
                    @if($course->reviews->count())
                    <div class="space-y-5">
                        @foreach($course->reviews->take(6) as $review)
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center shrink-0 overflow-hidden">
                                @if($review->user->avatar_url)
                                <img src="{{ $review->user->avatar_url }}" alt="{{ $review->user->full_name }}" class="w-full h-full object-cover">
                                @else
                                <span class="text-brand-700 font-bold text-sm">{{ strtoupper(substr($review->user->full_name ?? 'U', 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
                                    <div>
                                        <span class="font-semibold text-gray-900 text-sm">{{ $review->user->full_name }}</span>
                                        <span class="text-gray-400 text-xs ml-2">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex items-center gap-0.5">
                                        @for($s = 1; $s <= 5; $s++)
                                        <svg class="w-3 h-3 {{ $s <= $review->rating ? 'text-yellow-400 fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                </div>
                                @if($review->title)
                                <p class="font-semibold text-gray-800 text-sm mb-1">{{ $review->title }}</p>
                                @endif
                                <p class="text-gray-600 text-sm leading-relaxed">{{ $review->body }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-400 text-sm">
                        <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        No reviews yet. Be the first to review!
                    </div>
                    @endif
                </section>
            </div>

            {{-- ─────────────────────────────────────────────────────
                 RIGHT COLUMN — 4/12 (sticky)
            ───────────────────────────────────────────────────── --}}
            <div class="lg:w-4/12">
                <div class="sticky top-24 space-y-5">

                    {{-- ENROLLMENT CARD --}}
                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-xl shadow-gray-200/60">

                        {{-- Thumbnail / Video Preview --}}
                        <div class="relative aspect-video bg-gray-900 overflow-hidden group">
                            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                                 onerror="this.onerror=null;this.src='{{ \App\Models\Course::placeholderDataUri() }}'"
                                 class="w-full h-full object-cover opacity-90 group-hover:opacity-70 transition-opacity">
                            @if($course->promo_video)
                            <button onclick="document.getElementById('promo-modal').classList.remove('hidden')"
                                    class="absolute inset-0 flex items-center justify-center">
                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform">
                                    <svg class="w-7 h-7 text-brand-600 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </button>
                            <div class="absolute bottom-3 left-0 right-0 text-center">
                                <span class="bg-black bg-opacity-70 text-white text-xs font-semibold px-3 py-1 rounded-full">Preview this course</span>
                            </div>
                            @endif
                        </div>

                        <div class="p-6">
                            {{-- Price --}}
                            <div class="flex items-end gap-3 mb-5">
                                @if($course->is_free)
                                <span class="font-display text-3xl font-extrabold text-green-600">Free</span>
                                @elseif($course->discount_price && $course->discount_price < $course->price)
                                @php $discPct = round((($course->price - $course->discount_price) / $course->price) * 100); @endphp
                                <div>
                                    <span class="font-display text-3xl font-extrabold text-gray-900">{{ $course->currency ?? 'NGN' }} {{ number_format($course->discount_price) }}</span>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-gray-400 line-through text-sm">{{ $course->currency ?? 'NGN' }} {{ number_format($course->price) }}</span>
                                        <span class="text-red-500 text-sm font-bold">{{ $discPct }}% off</span>
                                    </div>
                                </div>
                                @else
                                <span class="font-display text-3xl font-extrabold text-gray-900">{{ $course->currency ?? 'NGN' }} {{ number_format($course->price) }}</span>
                                @endif
                            </div>

                            {{-- CTA Button --}}
                            @if($isEnrolled)
                            <a href="{{ route('student.learn', $course->slug) }}"
                               class="block w-full bg-green-600 hover:bg-green-700 text-white font-bold text-center py-4 rounded-2xl transition-colors text-base mb-3">
                                <span class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Continue Learning
                                </span>
                            </a>
                            <p class="text-center text-xs text-gray-500">You are enrolled in this course</p>
                            @else
                            @if($course->is_free)
                            @auth
                            <a href="{{ route('enroll.checkout', $course->slug) }}"
                               class="block w-full bg-green-600 hover:bg-green-700 text-white font-bold text-center py-4 rounded-2xl transition-colors text-base mb-3">
                                Enroll for Free
                            </a>
                            @else
                            <a href="{{ route('auth.login') }}?redirect={{ urlencode(route('enroll.checkout', $course->slug)) }}"
                               class="block w-full bg-green-600 hover:bg-green-700 text-white font-bold text-center py-4 rounded-2xl transition-colors text-base mb-3">
                                Enroll for Free — Login to Continue
                            </a>
                            @endauth
                            @else
                            @auth
                            <a href="{{ route('enroll.checkout', $course->slug) }}"
                               class="block w-full bg-brand-600 hover:bg-brand-700 text-white font-bold text-center py-4 rounded-2xl transition-all hover:-translate-y-0.5 hover:shadow-lg hover:shadow-brand-500/30 text-base mb-3">
                                Enroll Now
                            </a>
                            @else
                            <a href="{{ route('auth.login') }}?redirect={{ urlencode(route('enroll.checkout', $course->slug)) }}"
                               class="block w-full bg-brand-600 hover:bg-brand-700 text-white font-bold text-center py-4 rounded-2xl transition-all hover:-translate-y-0.5 hover:shadow-lg hover:shadow-brand-500/30 text-base mb-3">
                                Enroll Now — Login to Continue
                            </a>
                            @endauth
                            @endif
                            <p class="text-center text-xs text-gray-500 mb-3">30-day money-back guarantee</p>
                            @endif

                            {{-- Course includes --}}
                            <div class="border-t border-gray-100 pt-5 mt-4">
                                <h4 class="font-semibold text-gray-900 text-sm mb-4">This course includes:</h4>
                                <ul class="space-y-3 text-sm text-gray-600">
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>{{ $course->duration_hours ?? 0 }} hours of content</span>
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>{{ $course->total_lessons ?? 0 }} {{ Str::plural('lesson', $course->total_lessons ?? 0) }}</span>
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        <span class="capitalize">{{ ucfirst($course->type ?? 'online') }} learning</span>
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                        <span class="capitalize">{{ ucfirst($course->level ?? 'All') }} level</span>
                                    </li>
                                    @if($course->certificate_enabled)
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                        <span class="font-medium text-green-700">Certificate of completion</span>
                                    </li>
                                    @endif
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2v-8a2 2 0 00-2-2H8a2 2 0 00-2 2v8a2 2 0 002 2zM12 11V3m0 0l-3 3m3-3l3 3"/></svg>
                                        <span>Lifetime access</span>
                                    </li>
                                </ul>
                            </div>

                            {{-- Brochure Download --}}
                            @if($course->brochure_url)
                            <div class="border-t border-gray-100 pt-5 mt-5">
                                <a href="{{ $course->brochure_url }}" target="_blank" rel="noopener"
                                   download
                                   class="flex items-center justify-center gap-2 w-full border-2 border-brand-600 text-brand-700 hover:bg-brand-600 hover:text-white font-semibold py-3 rounded-2xl transition-all duration-200 text-sm group">
                                    <svg class="w-4 h-4 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download Brochure
                                </a>
                                <p class="text-center text-xs text-gray-400 mt-1.5">PDF · Course outline & details</p>
                            </div>
                            @endif

                            {{-- Share --}}
                            <div class="border-t border-gray-100 pt-5 mt-5">
                                <h4 class="font-semibold text-gray-900 text-sm mb-3">Share this course</h4>
                                <div class="flex gap-2">
                                    @php $shareUrl = urlencode(route('courses.show', $course->slug)); $shareTitle = urlencode($course->title); @endphp
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener"
                                       class="flex-1 flex items-center justify-center gap-1.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                                        Facebook
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener"
                                       class="flex-1 flex items-center justify-center gap-1.5 py-2 bg-sky-500 hover:bg-sky-600 text-white text-xs font-semibold rounded-xl transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
                                        Twitter
                                    </a>
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" rel="noopener"
                                       class="flex-1 flex items-center justify-center gap-1.5 py-2 bg-blue-800 hover:bg-blue-900 text-white text-xs font-semibold rounded-xl transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z M4 6a2 2 0 100-4 2 2 0 000 4z"/></svg>
                                        LinkedIn
                                    </a>
                                    <button onclick="navigator.clipboard.writeText('{{ route('courses.show', $course->slug) }}').then(() => { this.textContent = '✓'; setTimeout(() => this.textContent = '🔗', 1500); })"
                                            class="w-10 flex items-center justify-center py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-xl transition-colors"
                                            title="Copy link">
                                        🔗
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Who is this for --}}
                    @if(!empty($course->who_is_this_for) && count($course->who_is_this_for))
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h4 class="font-display font-bold text-gray-900 mb-4 text-base">Who is this course for?</h4>
                        <ul class="space-y-2.5">
                            @foreach($course->who_is_this_for as $who)
                            <li class="flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-brand-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span class="text-gray-600 text-sm leading-relaxed">{{ $who }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Tags --}}
                    @if($course->tags && $course->tags->count())
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h4 class="font-display font-bold text-gray-900 mb-4 text-base">Tags</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($course->tags as $tag)
                            <span class="inline-block bg-gray-100 hover:bg-brand-100 hover:text-brand-700 text-gray-600 text-xs font-medium px-3 py-1.5 rounded-full transition-colors cursor-pointer">
                                {{ $tag->name }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     RELATED COURSES
═══════════════════════════════════════════════════════════════ --}}
@if(isset($relatedCourses) && $relatedCourses->count())
<section class="py-14 bg-white border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-8">
            <div>
                <span class="text-brand-600 font-semibold text-sm uppercase tracking-widest">Related</span>
                <h2 class="font-display text-2xl font-bold text-gray-900 mt-1">Students Also Enrolled In</h2>
            </div>
            @if($course->category)
            <a href="{{ route('courses.category', $course->category->slug) }}" class="text-brand-600 hover:text-brand-700 text-sm font-semibold flex items-center gap-1 transition-colors">
                More {{ $course->category->name }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
            @endif
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($relatedCourses->take(4) as $related)
            <article class="card-hover bg-white rounded-2xl border border-gray-200 overflow-hidden group">
                <div class="relative aspect-video overflow-hidden bg-gray-100">
                    <a href="{{ route('courses.show', $related->slug) }}">
                        <img src="{{ $related->thumbnail_url }}" alt="{{ $related->title }}"
                             onerror="this.onerror=null;this.src='{{ \App\Models\Course::placeholderDataUri() }}'"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </a>
                    @if($related->category)
                    <span class="absolute top-2 right-2 bg-black bg-opacity-60 text-white text-xs px-2 py-0.5 rounded-full">{{ $related->category->name }}</span>
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 text-sm leading-snug mb-2 group-hover:text-brand-700 transition-colors line-clamp-2">
                        <a href="{{ route('courses.show', $related->slug) }}">{{ $related->title }}</a>
                    </h3>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1">
                            <div class="flex items-center gap-0.5">
                                @for($s = 1; $s <= 5; $s++)
                                <svg class="w-3 h-3 {{ $s <= round($related->average_rating ?? 0) ? 'text-yellow-400 fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                        </div>
                        <div class="text-sm font-bold text-gray-900">
                            @if($related->is_free)
                            <span class="text-green-600">Free</span>
                            @else
                            {{ $related->currency ?? 'NGN' }} {{ number_format($related->effective_price) }}
                            @endif
                        </div>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Promo video modal --}}
@if($course->promo_video)
<div id="promo-modal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-80 flex items-center justify-center p-4"
     onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="relative w-full max-w-3xl bg-black rounded-2xl overflow-hidden">
        <button onclick="document.getElementById('promo-modal').classList.add('hidden')"
                class="absolute top-3 right-3 z-10 w-9 h-9 bg-black bg-opacity-60 hover:bg-opacity-90 text-white rounded-full flex items-center justify-center transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="aspect-video">
            @if(str_contains($course->promo_video, 'youtube') || str_contains($course->promo_video, 'youtu.be'))
            @php
                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $course->promo_video, $ytm);
            @endphp
            <iframe src="https://www.youtube.com/embed/{{ $ytm[1] ?? '' }}?autoplay=1" class="w-full h-full" frameborder="0" allowfullscreen allow="autoplay"></iframe>
            @elseif(str_contains($course->promo_video, 'vimeo'))
            @php preg_match('/vimeo\.com\/(\d+)/', $course->promo_video, $vim); @endphp
            <iframe src="https://player.vimeo.com/video/{{ $vim[1] ?? '' }}?autoplay=1" class="w-full h-full" frameborder="0" allowfullscreen allow="autoplay"></iframe>
            @else
            <video src="{{ $course->promo_video }}" class="w-full h-full" controls autoplay></video>
            @endif
        </div>
    </div>
</div>
@endif

@endsection
