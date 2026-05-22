@extends('layouts.student')
@section('title', 'Attendance — ' . $course->title)

@section('content')
<div class="p-6 max-w-3xl mx-auto">
    <nav class="text-xs text-gray-400 mb-4 flex items-center gap-1">
        <a href="{{ route('student.attendance') }}" class="hover:text-brand-600">Attendance</a>
        <span>/</span>
        <span class="text-gray-600">{{ $course->title }}</span>
    </nav>

    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $course->title }}</h1>
            <p class="text-gray-500 text-sm">Attendance Record</p>
        </div>
        <div class="text-center">
            <div class="text-4xl font-bold {{ $percentage >= 75 ? 'text-green-600' : ($percentage >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                {{ number_format($percentage, 1) }}%
            </div>
            <p class="text-xs text-gray-400">overall attendance</p>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-3 gap-3 mb-6">
        @php
            $total = $sessions->count();
            $present = $records->where('status', 'present')->count();
            $late = $records->where('status', 'late')->count();
            $absent = $records->where('status', 'absent')->count();
            $excused = $records->where('status', 'excused')->count();
        @endphp
        <div class="bg-green-50 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-green-700">{{ $present + $late }}</p>
            <p class="text-xs text-green-600">Present / Late</p>
        </div>
        <div class="bg-red-50 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-red-700">{{ $absent }}</p>
            <p class="text-xs text-red-600">Absent</p>
        </div>
        <div class="bg-gray-50 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-gray-700">{{ $total }}</p>
            <p class="text-xs text-gray-500">Total Sessions</p>
        </div>
    </div>

    @if($percentage < 75)
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-start gap-3">
        <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 110 18A9 9 0 0112 3z"/>
        </svg>
        <div>
            <p class="text-sm font-medium text-red-800">Attendance Below Minimum</p>
            <p class="text-xs text-red-700 mt-0.5">Your attendance ({{ number_format($percentage, 1) }}%) is below the required 75% minimum. This may affect your certificate eligibility.</p>
        </div>
    </div>
    @endif

    {{-- Sessions Table --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b">
            <h2 class="font-semibold text-gray-900">Session History</h2>
        </div>
        <div class="divide-y">
            @forelse($sessions as $session)
            @php
                $record = $records->get($session->id);
                $status = $record?->status;
                $statusConfig = [
                    'present' => ['label'=>'Present','class'=>'bg-green-100 text-green-700'],
                    'late'    => ['label'=>'Late','class'=>'bg-yellow-100 text-yellow-700'],
                    'absent'  => ['label'=>'Absent','class'=>'bg-red-100 text-red-700'],
                    'excused' => ['label'=>'Excused','class'=>'bg-blue-100 text-blue-700'],
                    null      => ['label'=>'Not Marked','class'=>'bg-gray-100 text-gray-500'],
                ];
                $config = $statusConfig[$status] ?? $statusConfig[null];
            @endphp
            <div class="flex items-center justify-between px-5 py-3">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $session->title }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs text-gray-400">{{ $session->session_date->format('M d, Y') }}</span>
                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                        <span class="text-xs text-gray-400 capitalize">{{ $session->type }}</span>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $config['class'] }}">{{ $config['label'] }}</span>
            </div>
            @empty
            <div class="px-5 py-12 text-center text-gray-400">No attendance sessions yet for this course.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
