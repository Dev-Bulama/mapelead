@extends('layouts.student')
@section('title', 'My Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-brand-600 to-purple-600 rounded-2xl p-6 text-white">
        <h2 class="text-xl font-bold">Welcome back, {{ auth()->user()->first_name }}! 👋</h2>
        <p class="text-brand-100 mt-1">You're making great progress. Keep going!</p>
        @if($stats['avg_progress'] > 0)
            <div class="mt-4">
                <div class="flex justify-between text-sm mb-1">
                    <span>Overall Progress</span>
                    <span>{{ round($stats['avg_progress']) }}%</span>
                </div>
                <div class="bg-white/20 rounded-full h-2">
                    <div class="bg-white rounded-full h-2 progress-bar" style="width: {{ round($stats['avg_progress']) }}%"></div>
                </div>
            </div>
        @endif
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Total Courses',   'value' => $stats['total_courses'],  'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'color' => 'blue'],
            ['label' => 'Completed',        'value' => $stats['completed'],       'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'green'],
            ['label' => 'In Progress',      'value' => $stats['in_progress'],     'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'yellow'],
            ['label' => 'Certificates',     'value' => $stats['certificates'],    'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0', 'color' => 'purple'],
        ] as $stat)
            <div class="bg-white rounded-2xl p-4 border border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $stat['label'] }}</span>
                    <div class="w-8 h-8 bg-{{ $stat['color'] }}-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Admission Number Banner --}}
    @if(auth()->user()->admission_number)
    <div class="bg-gradient-to-r from-[#14215B] to-blue-800 rounded-2xl p-5 text-white flex items-center justify-between">
        <div>
            <p class="text-blue-200 text-xs font-semibold uppercase tracking-widest">Your Admission Number</p>
            <p class="text-3xl font-bold font-mono mt-1">{{ auth()->user()->admission_number }}</p>
            <p class="text-blue-300 text-xs mt-1">Keep this number for reference</p>
        </div>
        <a href="{{ route('student.admission') }}"
           class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
            View Admission
        </a>
    </div>
    @endif

    {{-- My Courses --}}
    <div class="bg-white rounded-2xl border border-gray-100">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">My Courses</h3>
            <a href="{{ route('student.courses') }}" class="text-sm text-brand-600 hover:underline">View all →</a>
        </div>

        @if($enrollments->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/></svg>
                </div>
                <p class="text-gray-500 mb-4">You haven't enrolled in any courses yet.</p>
                <a href="{{ route('courses.index') }}" class="bg-brand-600 hover:bg-brand-700 text-white font-medium px-5 py-2.5 rounded-xl transition-colors inline-block">
                    Browse Courses
                </a>
            </div>
        @else
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($enrollments->take(6) as $enrollment)
                    <div class="border border-gray-100 rounded-xl overflow-hidden hover:shadow-md transition-shadow">
                        <div class="aspect-video bg-gray-100 relative overflow-hidden">
                            <img src="{{ $enrollment->course->thumbnail_url }}"
                                 alt="{{ $enrollment->course->title }}"
                                 class="w-full h-full object-cover">
                            @if($enrollment->status === 'completed')
                                <div class="absolute top-2 right-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full">Completed</div>
                            @endif
                        </div>
                        <div class="p-3">
                            <p class="text-xs text-brand-600 font-medium mb-1">{{ $enrollment->course->category->name ?? '' }}</p>
                            <h4 class="font-semibold text-gray-900 text-sm line-clamp-2">{{ $enrollment->course->title }}</h4>
                            <p class="text-xs text-gray-500 mt-1">by {{ $enrollment->course->instructor->user->full_name ?? 'Instructor' }}</p>

                            {{-- Progress --}}
                            <div class="mt-3">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Progress</span>
                                    <span>{{ $enrollment->progress_percent }}%</span>
                                </div>
                                <div class="bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-brand-600 rounded-full h-1.5 progress-bar" style="width: {{ $enrollment->progress_percent }}%"></div>
                                </div>
                            </div>

                            <a href="{{ route('student.learn', $enrollment->course->slug) }}"
                               class="mt-3 block w-full text-center bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold py-2 rounded-lg transition-colors">
                                {{ $enrollment->progress_percent > 0 ? 'Continue Learning' : 'Start Learning' }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Recent Notifications --}}
    @if($notifications->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">Recent Notifications</h3>
                <a href="{{ route('student.notifications') }}" class="text-sm text-brand-600 hover:underline">View all →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($notifications as $notification)
                    <div class="p-4 flex items-start gap-3 {{ $notification->is_read ? '' : 'bg-brand-50' }}">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0
                            {{ $notification->type === 'success' ? 'bg-green-100' : ($notification->type === 'warning' ? 'bg-yellow-100' : 'bg-brand-100') }}">
                            <svg class="w-4 h-4 {{ $notification->type === 'success' ? 'text-green-600' : 'text-brand-600' }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $notification->message }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
