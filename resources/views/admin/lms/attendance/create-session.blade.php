@extends('layouts.admin')

@section('title', 'New Attendance Session')

@section('content')
<div class="space-y-5">

    {{-- ═══════════════════════════════════════════════════════════════
         PAGE HEADER + BREADCRUMB
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-gray-400 mb-1.5 flex-wrap">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-brand-600 transition-colors">Dashboard</a>
                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('admin.courses.index') }}" class="hover:text-brand-600 transition-colors">Courses</a>
                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('admin.courses.show', $course->id) }}" class="hover:text-brand-600 transition-colors truncate max-w-[160px]">{{ $course->title }}</a>
                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('admin.courses.attendance.index', $course->id) }}" class="hover:text-brand-600 transition-colors">Attendance</a>
                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-600 font-medium">New Session</span>
            </nav>

            <h2 class="text-2xl font-bold text-gray-900">New Attendance Session</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                For: <span class="font-medium text-gray-700">{{ $course->title }}</span>
            </p>
        </div>

        <a href="{{ route('admin.courses.attendance.index', $course->id) }}"
           class="flex items-center gap-2 text-sm text-gray-600 bg-white border border-gray-200 px-4 py-2 rounded-lg hover:text-gray-900 hover:border-gray-300 transition-colors shrink-0 self-start">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Attendance
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         FORM
    ═══════════════════════════════════════════════════════════════ --}}
    <form action="{{ route('admin.courses.attendance.session.store', $course->id) }}" method="POST"
          class="space-y-5 max-w-2xl">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-100 pb-3">
                Session Details
            </h3>

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Session Title <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="title"
                       name="title"
                       value="{{ old('title') }}"
                       required
                       placeholder="e.g. Week 3 — Introduction to Variables"
                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Session Date --}}
            <div>
                <label for="session_date" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Session Date <span class="text-red-500">*</span>
                </label>
                <input type="date"
                       id="session_date"
                       name="session_date"
                       value="{{ old('session_date', now()->toDateString()) }}"
                       required
                       class="w-full sm:w-56 border border-gray-200 rounded-lg px-4 py-2.5 text-sm bg-white
                              focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                @error('session_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Time Range --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Start Time
                        <span class="text-xs font-normal text-gray-400">(optional)</span>
                    </label>
                    <input type="time"
                           id="start_time"
                           name="start_time"
                           value="{{ old('start_time') }}"
                           class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm bg-white
                                  focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                    @error('start_time')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1.5">
                        End Time
                        <span class="text-xs font-normal text-gray-400">(optional)</span>
                    </label>
                    <input type="time"
                           id="end_time"
                           name="end_time"
                           value="{{ old('end_time') }}"
                           class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm bg-white
                                  focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                    @error('end_time')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Type --}}
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Session Type <span class="text-red-500">*</span>
                </label>
                <select id="type"
                        name="type"
                        required
                        class="w-full sm:w-56 border border-gray-200 rounded-lg px-4 py-2.5 text-sm bg-white
                               focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                    <option value="online"   {{ old('type') === 'online'   ? 'selected' : '' }}>Online</option>
                    <option value="physical" {{ old('type') === 'physical' ? 'selected' : '' }}>Physical</option>
                    <option value="hybrid"   {{ old('type') === 'hybrid'   ? 'selected' : '' }}>Hybrid</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Notes --}}
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Notes
                    <span class="text-xs font-normal text-gray-400">(optional)</span>
                </label>
                <textarea id="notes"
                          name="notes"
                          rows="3"
                          placeholder="Any notes about this session…"
                          class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm
                                 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 resize-none transition">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3 pb-2">
            <a href="{{ route('admin.courses.attendance.index', $course->id) }}"
               class="text-sm text-gray-600 border border-gray-200 hover:border-gray-300 px-4 py-2 rounded-lg transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white
                           text-sm font-medium px-5 py-2.5 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Create Session
            </button>
        </div>
    </form>

</div>
@endsection
