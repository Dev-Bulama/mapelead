@extends('layouts.admin')
@section('title', 'Instructor: ' . $instructor->full_name)

@section('content')
<div class="space-y-6">

    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-2xl text-sm font-medium">
        {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl overflow-hidden bg-indigo-100 shrink-0">
                @if($instructor->avatar_url)
                <img src="{{ $instructor->avatar_url }}" alt="{{ $instructor->full_name }}" class="w-full h-full object-cover">
                @else
                <div class="w-full h-full flex items-center justify-center text-indigo-600 font-bold text-xl">
                    {{ strtoupper(substr($instructor->full_name, 0, 2)) }}
                </div>
                @endif
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $instructor->full_name }}</h1>
                    @if($instructor->is_verified)
                    <span class="px-2.5 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Verified</span>
                    @endif
                    @if($instructor->is_featured)
                    <span class="px-2.5 py-0.5 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">Featured</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-1">{{ $instructor->user?->email }}</p>
                @if($instructor->title || $instructor->expertise)
                <p class="text-sm text-gray-600 mt-0.5">{{ $instructor->title }}{{ $instructor->title && $instructor->expertise ? ' · ' : '' }}{{ $instructor->expertise }}</p>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <form action="{{ route('admin.instructors.toggle-featured', $instructor) }}" method="POST">
                @csrf
                <button type="submit" class="text-sm font-medium {{ $instructor->is_featured ? 'bg-amber-100 text-amber-700 hover:bg-amber-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} px-4 py-2 rounded-xl transition-colors">
                    {{ $instructor->is_featured ? '★ Unfeature' : '☆ Feature' }}
                </button>
            </form>
            <form action="{{ route('admin.instructors.toggle-verified', $instructor) }}" method="POST">
                @csrf
                <button type="submit" class="text-sm font-medium {{ $instructor->is_verified ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} px-4 py-2 rounded-xl transition-colors">
                    {{ $instructor->is_verified ? 'Remove Verification' : 'Verify Instructor' }}
                </button>
            </form>
            <a href="{{ route('admin.instructors.index') }}" class="text-sm text-gray-600 bg-white border border-gray-200 px-4 py-2 rounded-xl hover:text-gray-900 transition-colors">
                ← Back
            </a>
        </div>
    </div>

    {{-- Performance Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Courses',       'value' => $instructor->courses->count(),           'color' => 'text-indigo-600', 'bg' => 'bg-indigo-50'],
            ['label' => 'Total Students', 'value' => number_format($totalStudents),            'color' => 'text-blue-600',   'bg' => 'bg-blue-50'],
            ['label' => 'Revenue',        'value' => '₦' . number_format($totalRevenue),      'color' => 'text-green-600',  'bg' => 'bg-green-50'],
            ['label' => 'Rating',         'value' => $instructor->average_rating > 0 ? number_format($instructor->average_rating, 1) . ' ★' : 'N/A', 'color' => 'text-amber-600', 'bg' => 'bg-amber-50'],
        ] as $stat)
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="w-10 h-10 {{ $stat['bg'] }} rounded-xl flex items-center justify-center mb-3">
                <span class="{{ $stat['color'] }} font-bold text-lg">{{ substr($stat['value'], 0, 1) === '₦' ? '₦' : '#' }}</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stat['value'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Session Assignments --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Session Assignments</h3>
                @php
                    $sessionConfig = [
                        'morning'   => ['label' => 'Morning',   'icon' => '🌅', 'bg' => 'bg-orange-50',  'border' => 'border-orange-200', 'text' => 'text-orange-700'],
                        'afternoon' => ['label' => 'Afternoon', 'icon' => '☀️', 'bg' => 'bg-yellow-50',  'border' => 'border-yellow-200', 'text' => 'text-yellow-700'],
                        'evening'   => ['label' => 'Evening',   'icon' => '🌙', 'bg' => 'bg-indigo-50',  'border' => 'border-indigo-200', 'text' => 'text-indigo-700'],
                    ];
                @endphp
                @if($instructor->sessionAssignments->isEmpty())
                <div class="text-center py-8 text-gray-400">
                    <p class="text-sm">No session assignments yet.</p>
                    <p class="text-xs mt-1">Assign this instructor to a course session from the course edit page.</p>
                </div>
                @else
                <div class="space-y-3">
                    @foreach($instructor->sessionAssignments as $assignment)
                    @php $cfg = $sessionConfig[$assignment->session] ?? ['label' => $assignment->session, 'icon' => '⏰', 'bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-700']; @endphp
                    <div class="flex items-start gap-3 p-3 {{ $cfg['bg'] }} border {{ $cfg['border'] }} rounded-xl">
                        <span class="text-lg shrink-0">{{ $cfg['icon'] }}</span>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold {{ $cfg['text'] }} uppercase tracking-wide">{{ $cfg['label'] }} Session</p>
                            <p class="text-sm font-medium text-gray-900 truncate mt-0.5">{{ $assignment->course->title ?? '—' }}</p>
                            @if($assignment->session_time)
                            <p class="text-xs text-gray-500 mt-0.5">{{ $assignment->formatted_time }}</p>
                            @endif
                        </div>
                        @if($assignment->course)
                        <a href="{{ route('admin.courses.show', $assignment->course_id) }}" class="shrink-0 text-xs text-indigo-600 hover:underline">View</a>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Profile Info --}}
            @if($instructor->description)
            <div class="bg-white rounded-2xl border border-gray-100 p-5 mt-4">
                <h3 class="font-semibold text-gray-900 mb-3">About</h3>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $instructor->description }}</p>
            </div>
            @endif
        </div>

        {{-- Courses --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Assigned Courses</h3>
                </div>
                @if($instructor->courses->isEmpty())
                <div class="p-8 text-center text-gray-400">
                    <p class="text-sm">No courses assigned to this instructor.</p>
                </div>
                @else
                <div class="divide-y divide-gray-50">
                    @foreach($instructor->courses as $course)
                    <div class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition-colors">
                        <div class="w-12 h-8 rounded-lg overflow-hidden bg-gray-100 shrink-0">
                            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $course->title }}</p>
                            <p class="text-xs text-gray-400">{{ $course->category->name ?? '—' }} · {{ ucfirst($course->type) }}</p>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-sm font-semibold text-gray-900">{{ number_format($course->enrollments_count) }}</p>
                            <p class="text-xs text-gray-400">students</p>
                        </div>
                        <div class="shrink-0">
                            @if($course->status === 'published')
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded-full">Published</span>
                            @else
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">{{ ucfirst($course->status) }}</span>
                            @endif
                        </div>
                        <a href="{{ route('admin.courses.show', $course->id) }}" class="text-xs text-indigo-600 font-medium hover:underline shrink-0">View</a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
