@extends('layouts.admin')
@section('title', 'Attendance Report')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.courses.attendance.index', $course) }}" class="text-brand-600 hover:underline text-sm">← Back to Attendance</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">Attendance Report</h1>
            <p class="text-gray-500 text-sm">{{ $course->title }} — {{ $sessionsCount }} total sessions</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Student</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Admission No.</th>
                    <th class="text-center px-4 py-3 text-gray-600 font-medium">Attendance %</th>
                    <th class="text-center px-4 py-3 text-gray-600 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($enrollments as $enrollment)
                @php $pct = $enrollment->attendance_percentage ?? 0; @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <img src="{{ $enrollment->user->avatar_url }}" class="w-7 h-7 rounded-full object-cover">
                            <span class="font-medium">{{ $enrollment->user->full_name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $enrollment->user->admission_number ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2 justify-center">
                            <div class="w-24 bg-gray-200 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full {{ $pct >= 75 ? 'bg-green-500' : ($pct >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ min(100,$pct) }}%"></div>
                            </div>
                            <span class="font-bold text-sm {{ $pct >= 75 ? 'text-green-700' : ($pct >= 50 ? 'text-yellow-700' : 'text-red-700') }}">{{ number_format($pct, 1) }}%</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($pct >= 75)
                            <span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">Good</span>
                        @elseif($pct >= 50)
                            <span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700">At Risk</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700">Critical</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-12 text-center text-gray-400">No enrolled students found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
