@extends('layouts.admin')

@section('title', 'Mark Attendance — ' . $session->title)

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
                <a href="{{ route('admin.courses.attendance.index', $session->course_id) }}" class="hover:text-brand-600 transition-colors">Attendance</a>
                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-600 font-medium">Mark Attendance</span>
            </nav>

            <h2 class="text-2xl font-bold text-gray-900">Mark Attendance</h2>
            <p class="text-sm text-gray-600 mt-0.5 font-medium">
                {{ $session->title }}
                <span class="text-gray-400 font-normal mx-1.5">&mdash;</span>
                {{ $session->session_date->format('M d, Y') }}
                @if($session->start_time)
                    <span class="text-gray-400 font-normal ml-1.5">
                        {{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}
                        @if($session->end_time)
                            &ndash; {{ \Carbon\Carbon::parse($session->end_time)->format('g:i A') }}
                        @endif
                    </span>
                @endif
            </p>
        </div>

        <a href="{{ route('admin.courses.attendance.index', $session->course_id) }}"
           class="flex items-center gap-2 text-sm text-gray-600 bg-white border border-gray-200 px-4 py-2 rounded-lg hover:text-gray-900 hover:border-gray-300 transition-colors shrink-0 self-start">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Sessions
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         QUICK-MARK TOOLBAR
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex flex-wrap items-center gap-3">
        <p class="text-sm font-medium text-gray-700 mr-2">Quick mark:</p>

        <button type="button"
                onclick="markAll('present')"
                class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border
                       text-green-700 bg-green-50 border-green-200 hover:bg-green-100 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Mark All Present
        </button>

        <button type="button"
                onclick="markAll('absent')"
                class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border
                       text-red-700 bg-red-50 border-red-200 hover:bg-red-100 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Mark All Absent
        </button>

        <button type="button"
                onclick="markAll('late')"
                class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border
                       text-yellow-700 bg-yellow-50 border-yellow-200 hover:bg-yellow-100 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Mark All Late
        </button>

        <button type="button"
                onclick="markAll('excused')"
                class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border
                       text-blue-700 bg-blue-50 border-blue-200 hover:bg-blue-100 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Mark All Excused
        </button>

        <span class="ml-auto text-xs text-gray-400">{{ $enrollments->count() }} {{ Str::plural('student', $enrollments->count()) }}</span>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         ATTENDANCE FORM
    ═══════════════════════════════════════════════════════════════ --}}
    <form method="POST" action="{{ route('admin.attendance.save', $session->id) }}" id="attendance-form">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

            @if($enrollments->isEmpty())
                <div class="px-5 py-14 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">No students enrolled</p>
                    <p class="text-xs text-gray-400 mt-1">Enrol students in this course to mark attendance.</p>
                </div>

            @else
                <div class="overflow-x-auto">
                    <table class="admin-table w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase tracking-wide">
                            <tr>
                                <th class="px-5 py-3.5 text-left font-medium">Student</th>
                                <th class="px-4 py-3.5 text-left font-medium">Admission No.</th>
                                <th class="px-5 py-3.5 text-left font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($enrollments as $enrollment)
                                @php
                                    $user = $enrollment->user;
                                    $existing = $records[$user->id] ?? null;
                                    $currentStatus = $existing?->status ?? null;
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">

                                    {{-- Student --}}
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            @if($user->avatar)
                                                <img src="{{ asset('storage/' . $user->avatar) }}"
                                                     class="w-8 h-8 rounded-full object-cover shrink-0" alt="">
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-brand-50 flex items-center justify-center shrink-0 ring-1 ring-brand-100">
                                                    <span class="text-xs font-bold text-brand-600">
                                                        {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <p class="font-medium text-gray-800 truncate">{{ $user->first_name }} {{ $user->last_name }}</p>
                                                <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Admission Number --}}
                                    <td class="px-4 py-3.5">
                                        <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">
                                            {{ $user->admission_number ?? $user->student_id ?? '—' }}
                                        </span>
                                    </td>

                                    {{-- Status Radio Group --}}
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2 flex-wrap">

                                            {{-- Present --}}
                                            <label class="attendance-label inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border cursor-pointer transition-all
                                                          has-[:checked]:bg-green-100 has-[:checked]:border-green-400 has-[:checked]:text-green-800
                                                          text-gray-600 border-gray-200 hover:border-green-300 hover:bg-green-50">
                                                <input type="radio"
                                                       name="records[{{ $user->id }}]"
                                                       value="present"
                                                       {{ $currentStatus === 'present' ? 'checked' : '' }}
                                                       class="sr-only attendance-radio" data-status="present">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span class="text-xs font-medium">Present</span>
                                            </label>

                                            {{-- Absent --}}
                                            <label class="attendance-label inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border cursor-pointer transition-all
                                                          has-[:checked]:bg-red-100 has-[:checked]:border-red-400 has-[:checked]:text-red-800
                                                          text-gray-600 border-gray-200 hover:border-red-300 hover:bg-red-50">
                                                <input type="radio"
                                                       name="records[{{ $user->id }}]"
                                                       value="absent"
                                                       {{ $currentStatus === 'absent' ? 'checked' : '' }}
                                                       class="sr-only attendance-radio" data-status="absent">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                <span class="text-xs font-medium">Absent</span>
                                            </label>

                                            {{-- Late --}}
                                            <label class="attendance-label inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border cursor-pointer transition-all
                                                          has-[:checked]:bg-yellow-100 has-[:checked]:border-yellow-400 has-[:checked]:text-yellow-800
                                                          text-gray-600 border-gray-200 hover:border-yellow-300 hover:bg-yellow-50">
                                                <input type="radio"
                                                       name="records[{{ $user->id }}]"
                                                       value="late"
                                                       {{ $currentStatus === 'late' ? 'checked' : '' }}
                                                       class="sr-only attendance-radio" data-status="late">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span class="text-xs font-medium">Late</span>
                                            </label>

                                            {{-- Excused --}}
                                            <label class="attendance-label inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border cursor-pointer transition-all
                                                          has-[:checked]:bg-blue-100 has-[:checked]:border-blue-400 has-[:checked]:text-blue-800
                                                          text-gray-600 border-gray-200 hover:border-blue-300 hover:bg-blue-50">
                                                <input type="radio"
                                                       name="records[{{ $user->id }}]"
                                                       value="excused"
                                                       {{ $currentStatus === 'excused' ? 'checked' : '' }}
                                                       class="sr-only attendance-radio" data-status="excused">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span class="text-xs font-medium">Excused</span>
                                            </label>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Submit Footer --}}
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between gap-4">
                    <p class="text-xs text-gray-400">
                        Last saved:
                        @if($records->isNotEmpty())
                            {{ $records->sortByDesc('updated_at')->first()?->updated_at?->diffForHumans() ?? 'Never' }}
                        @else
                            Never
                        @endif
                    </p>

                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white
                                   text-sm font-medium px-6 py-2.5 rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Attendance
                    </button>
                </div>
            @endif
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    /**
     * Mark all students with a given status.
     * Finds all radio inputs with data-status matching the given value and checks them.
     */
    function markAll(status) {
        document.querySelectorAll('#attendance-form input[type="radio"]').forEach(function(radio) {
            if (radio.dataset.status === status) {
                radio.checked = true;
                // Trigger change event so any listeners pick it up
                radio.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    }
</script>
@endpush
