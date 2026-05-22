@extends('layouts.admin')

@section('title', 'Attendance — ' . $course->title)

@section('content')
<div class="space-y-5">

    {{-- ═══════════════════════════════════════════════════════════════
         PAGE HEADER + BREADCRUMB
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-gray-400 mb-1.5 flex-wrap">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-brand-600 transition-colors">Dashboard</a>
                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('admin.courses.index') }}" class="hover:text-brand-600 transition-colors">Courses</a>
                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('admin.courses.show', $course->id) }}" class="hover:text-brand-600 transition-colors truncate max-w-[160px]">{{ $course->title }}</a>
                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-600 font-medium">Attendance</span>
            </nav>

            <h2 class="text-2xl font-bold text-gray-900">
                Attendance &mdash; <span class="text-brand-600">{{ $course->title }}</span>
            </h2>
            <p class="text-sm text-gray-500 mt-0.5">
                Track and manage attendance sessions for enrolled students.
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2 flex-wrap shrink-0">
            {{-- Settings --}}
            <a href="{{ route('admin.courses.attendance.settings', $course->id) }}"
               class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 hover:border-gray-300 hover:text-gray-800 px-3.5 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Settings
            </a>

            {{-- Attendance Report --}}
            <a href="{{ route('admin.courses.attendance.report', $course->id) }}"
               class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 hover:border-gray-300 hover:text-gray-800 px-3.5 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Attendance Report
            </a>

            {{-- New Session --}}
            <a href="{{ route('admin.courses.attendance.session.create', $course->id) }}"
               class="inline-flex items-center gap-1.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Session
            </a>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         SESSIONS TABLE
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        @if($sessions->isEmpty())
            <div class="px-5 py-16 text-center">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-brand-50 flex items-center justify-center">
                        <svg class="w-8 h-8 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-base font-semibold text-gray-700">No sessions yet</p>
                        <p class="text-sm text-gray-400 mt-1">Create your first attendance session to begin tracking.</p>
                    </div>
                    <a href="{{ route('admin.courses.attendance.session.create', $course->id) }}"
                       class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create First Session
                    </a>
                </div>
            </div>

        @else
            <div class="overflow-x-auto">
                <table class="admin-table w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase tracking-wide">
                        <tr>
                            <th class="px-5 py-3.5 text-left font-medium">Date</th>
                            <th class="px-4 py-3.5 text-left font-medium">Title</th>
                            <th class="px-4 py-3.5 text-center font-medium">Type</th>
                            <th class="px-4 py-3.5 text-left font-medium">Created</th>
                            <th class="px-5 py-3.5 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($sessions as $session)
                            <tr class="hover:bg-gray-50 transition-colors">

                                {{-- Date --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-10 h-10 rounded-lg bg-brand-50 flex flex-col items-center justify-center shrink-0 border border-brand-100">
                                            <span class="text-[10px] font-bold text-brand-500 uppercase leading-none">
                                                {{ $session->session_date->format('M') }}
                                            </span>
                                            <span class="text-base font-extrabold text-brand-700 leading-tight">
                                                {{ $session->session_date->format('d') }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800 text-xs">{{ $session->session_date->format('l') }}</p>
                                            <p class="text-xs text-gray-400">{{ $session->session_date->format('Y') }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Title --}}
                                <td class="px-4 py-3.5">
                                    <p class="font-medium text-gray-800">{{ $session->title }}</p>
                                    @if($session->start_time || $session->end_time)
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            @if($session->start_time){{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}@endif
                                            @if($session->start_time && $session->end_time) &ndash; @endif
                                            @if($session->end_time){{ \Carbon\Carbon::parse($session->end_time)->format('g:i A') }}@endif
                                        </p>
                                    @endif
                                </td>

                                {{-- Type Badge --}}
                                <td class="px-4 py-3.5 text-center">
                                    @php
                                        $typeBadge = match($session->type) {
                                            'online'   => 'bg-blue-100 text-blue-700',
                                            'physical' => 'bg-green-100 text-green-700',
                                            'hybrid'   => 'bg-purple-100 text-purple-700',
                                            default    => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeBadge }}">
                                        {{ ucfirst($session->type) }}
                                    </span>
                                </td>

                                {{-- Created At --}}
                                <td class="px-4 py-3.5">
                                    <p class="text-xs text-gray-500">{{ $session->created_at->format('M j, Y') }}</p>
                                    <p class="text-xs text-gray-400">{{ $session->created_at->diffForHumans() }}</p>
                                </td>

                                {{-- Actions --}}
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">

                                        {{-- Mark Attendance --}}
                                        <a href="{{ route('admin.attendance.mark', $session->id) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-brand-600 bg-brand-50 hover:bg-brand-100 rounded-lg transition-colors border border-brand-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                            </svg>
                                            Mark Attendance
                                        </a>

                                        {{-- Delete --}}
                                        <form method="POST"
                                              action="{{ route('admin.attendance.session.destroy', $session->id) }}"
                                              onsubmit="return confirm('Delete session \'{{ addslashes($session->title) }}\'? All attendance records for this session will be lost.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Delete session">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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

            {{-- Pagination --}}
            @if($sessions->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $sessions->links() }}
                </div>
            @else
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50">
                    <p class="text-xs text-gray-500">{{ $sessions->total() }} {{ Str::plural('session', $sessions->total()) }} total</p>
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
