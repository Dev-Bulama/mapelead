@extends('layouts.student')
@section('title', 'My Attendance')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Attendance</h1>
        <p class="text-gray-500 text-sm mt-1">Track your attendance across all enrolled courses</p>
    </div>

    @if($enrollments->isEmpty())
    <div class="bg-white rounded-xl border p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <p class="text-gray-500">You are not enrolled in any active courses.</p>
    </div>
    @else
    <div class="grid md:grid-cols-2 gap-4">
        @foreach($enrollments as $enrollment)
        @php $pct = $enrollment->attendance_percentage; @endphp
        <div class="bg-white rounded-xl border p-5 hover:shadow-md transition">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1 min-w-0 pr-4">
                    <h3 class="font-semibold text-gray-900 truncate">{{ $enrollment->course->title }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $enrollment->course->category->name ?? '' }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="text-2xl font-bold {{ $pct >= 75 ? 'text-green-600' : ($pct >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ number_format($pct, 0) }}%
                    </div>
                    <p class="text-xs text-gray-400">attendance</p>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="w-full bg-gray-100 rounded-full h-2 mb-4">
                <div class="h-2 rounded-full {{ $pct >= 75 ? 'bg-green-500' : ($pct >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ min(100, $pct) }}%"></div>
            </div>

            @if($pct < 75)
            <div class="bg-red-50 border border-red-100 rounded-lg px-3 py-2 mb-3">
                <p class="text-xs text-red-700">
                    <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 110 18A9 9 0 0112 3z"/></svg>
                    Below minimum requirement (75%). Attend more sessions.
                </p>
            </div>
            @endif

            <a href="{{ route('student.attendance.show', $enrollment->course) }}" class="block text-center text-brand-600 hover:bg-brand-50 py-2 rounded-lg text-sm font-medium transition border border-brand-200">
                View Details
            </a>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
