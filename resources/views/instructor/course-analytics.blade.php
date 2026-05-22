@extends('layouts.student')

@section('title', 'Course Analytics')
@section('page_title', 'Analytics: ' . $course->title)

@section('content')
@php
    $enrollments = $course->enrollments()->with('user')->get();
    $completed = $enrollments->where('status', 'completed')->count();
    $inProgress = $enrollments->where('status', 'active')->count();
    $total = $enrollments->count();
    $completionRate = $total > 0 ? round(($completed / $total) * 100) : 0;

    $ranges = [
        '0–25%'   => $enrollments->filter(fn($e) => ($e->progress ?? 0) <= 25)->count(),
        '26–50%'  => $enrollments->filter(fn($e) => ($e->progress ?? 0) > 25 && ($e->progress ?? 0) <= 50)->count(),
        '51–75%'  => $enrollments->filter(fn($e) => ($e->progress ?? 0) > 50 && ($e->progress ?? 0) <= 75)->count(),
        '76–99%'  => $enrollments->filter(fn($e) => ($e->progress ?? 0) > 75 && ($e->progress ?? 0) < 100)->count(),
        '100%'    => $enrollments->filter(fn($e) => ($e->progress ?? 0) >= 100)->count(),
    ];
    $maxRange = max(array_values($ranges)) ?: 1;
@endphp

<div class="space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.courses.show', $course->id) }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Course
        </a>
    </div>

    <div class="bg-gradient-to-r from-brand-600 to-purple-600 rounded-2xl p-6 text-white">
        <p class="text-sm text-indigo-200 mb-1">Analytics for</p>
        <h2 class="text-xl font-bold">{{ $course->title }}</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Total Enrolled</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">{{ $total }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Completed</p>
            <p class="mt-1 text-3xl font-bold text-green-600">{{ $completed }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <p class="text-sm text-gray-500">In Progress</p>
            <p class="mt-1 text-3xl font-bold text-blue-600">{{ $inProgress }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Completion Rate</p>
            <p class="mt-1 text-3xl font-bold text-brand-600">{{ $completionRate }}%</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-5">Progress Distribution</h3>
        <div class="space-y-4">
            @foreach($ranges as $label => $count)
            @php $pct = $maxRange > 0 ? round(($count / $maxRange) * 100) : 0; @endphp
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500 w-16 flex-shrink-0">{{ $label }}</span>
                <div class="flex-1 bg-gray-100 rounded-full h-3">
                    <div class="h-3 rounded-full bg-brand-500 transition-all" style="width: {{ $pct }}%"></div>
                </div>
                <span class="text-sm font-medium text-gray-700 w-8 text-right flex-shrink-0">{{ $count }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Student Breakdown</h3>

        @if($enrollments->isEmpty())
        <div class="text-center py-10">
            <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <p class="mt-3 text-sm text-gray-500">No students enrolled yet.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Student</th>
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Enrolled</th>
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Progress</th>
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Status</th>
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Last Active</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($enrollments as $enrollment)
                    @php
                        $user = $enrollment->user;
                        $progress = $enrollment->progress ?? 0;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-2">
                            <div class="flex items-center gap-2">
                                <div class="flex-shrink-0 w-7 h-7 rounded-full bg-brand-100 flex items-center justify-center">
                                    <span class="text-xs font-bold text-brand-600">
                                        {{ $user ? strtoupper(substr($user->first_name ?? $user->name ?? 'U', 0, 1)) : 'U' }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $user ? ($user->name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))) : 'Unknown' }}</p>
                                    <p class="text-xs text-gray-400">{{ $user?->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-2 text-gray-500 whitespace-nowrap">
                            {{ $enrollment->created_at->format('M d, Y') }}
                        </td>
                        <td class="py-3 px-2">
                            <div class="flex items-center gap-2">
                                <div class="w-20 bg-gray-100 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full {{ $progress >= 100 ? 'bg-green-500' : 'bg-brand-500' }}" style="width: {{ min($progress, 100) }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 w-8">{{ $progress }}%</span>
                            </div>
                        </td>
                        <td class="py-3 px-2">
                            @if($enrollment->status === 'completed')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Completed</span>
                            @elseif($enrollment->status === 'active')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Active</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 capitalize">{{ $enrollment->status ?? 'Enrolled' }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-2 text-gray-400 text-xs whitespace-nowrap">
                            @if($user && $user->last_login_at)
                                {{ \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() }}
                            @else
                                Never
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection
