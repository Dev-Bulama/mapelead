@extends('layouts.student')

@section('title', 'Instructor Dashboard')
@section('page_title', 'Instructor Dashboard')

@section('content')
<div class="space-y-6">

    <div class="bg-gradient-to-r from-brand-600 to-purple-600 rounded-2xl p-6 text-white">
        <h2 class="text-2xl font-bold">Welcome back, {{ auth()->user()->first_name }}!</h2>
        <p class="mt-1 text-indigo-100">Here's your teaching overview.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Total Courses</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">{{ $courses->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Total Students</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">{{ $totalStudents }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Total Revenue</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">₦{{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Active Courses</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">{{ $courses->where('status', 'published')->count() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">My Courses</h3>
            <a href="{{ route('instructor.courses.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl transition-colors">
                + New Course
            </a>
        </div>

        @if($courses->isEmpty())
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <p class="mt-4 text-gray-500">You haven't created any courses yet.</p>
            <a href="{{ route('instructor.courses.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl transition-colors">
                Create Your First Course
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Course</th>
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Enrollments</th>
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Status</th>
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Price</th>
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($courses as $course)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-2 font-medium text-gray-900">{{ $course->title }}</td>
                        <td class="py-3 px-2 text-gray-600">{{ $course->enrollments_count ?? 0 }}</td>
                        <td class="py-3 px-2">
                            @if($course->status === 'published')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Published</span>
                            @elseif($course->status === 'rejected')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Rejected</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Draft</span>
                            @endif
                        </td>
                        <td class="py-3 px-2 text-gray-600">
                            @if($course->is_free)
                                <span class="text-green-600 font-medium">Free</span>
                            @else
                                ₦{{ number_format($course->price, 2) }}
                            @endif
                        </td>
                        <td class="py-3 px-2">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('instructor.courses.show', $course->id) }}" class="text-brand-600 hover:text-brand-700 font-medium">View</a>
                                <a href="{{ route('instructor.courses.edit', $course->id) }}" class="text-gray-500 hover:text-gray-700 font-medium">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- My Session Assignments --}}
    @if($sessionAssignments->isNotEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">My Assigned Sessions</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
                $sessionCfg = [
                    'morning'   => ['icon' => '🌅', 'label' => 'Morning',   'bg' => 'from-orange-50 to-amber-50',  'border' => 'border-orange-200', 'badge' => 'bg-orange-100 text-orange-700'],
                    'afternoon' => ['icon' => '☀️', 'label' => 'Afternoon', 'bg' => 'from-yellow-50 to-amber-50', 'border' => 'border-yellow-200', 'badge' => 'bg-yellow-100 text-yellow-700'],
                    'evening'   => ['icon' => '🌙', 'label' => 'Evening',   'bg' => 'from-indigo-50 to-purple-50', 'border' => 'border-indigo-200', 'badge' => 'bg-indigo-100 text-indigo-700'],
                ];
            @endphp
            @foreach($sessionAssignments as $assignment)
            @php $cfg = $sessionCfg[$assignment->session] ?? ['icon' => '⏰', 'label' => ucfirst($assignment->session), 'bg' => 'from-gray-50 to-gray-50', 'border' => 'border-gray-200', 'badge' => 'bg-gray-100 text-gray-700']; @endphp
            <div class="bg-gradient-to-br {{ $cfg['bg'] }} border {{ $cfg['border'] }} rounded-2xl p-5">
                <div class="flex items-start justify-between mb-3">
                    <span class="text-2xl">{{ $cfg['icon'] }}</span>
                    <span class="inline-flex items-center px-2.5 py-1 {{ $cfg['badge'] }} text-xs font-semibold rounded-full">
                        {{ $cfg['label'] }}
                    </span>
                </div>
                <p class="font-semibold text-gray-900 text-sm leading-tight">{{ $assignment->course->title ?? '—' }}</p>
                @if($assignment->course->category)
                <p class="text-xs text-gray-500 mt-1">{{ $assignment->course->category->name }}</p>
                @endif
                <div class="flex items-center gap-2 mt-3 flex-wrap">
                    @if($assignment->session_time)
                    <span class="inline-flex items-center gap-1 text-xs font-mono font-bold text-gray-700 bg-white border border-gray-200 px-2.5 py-1 rounded-lg">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $assignment->formatted_time }}
                    </span>
                    @endif
                    @if($assignment->course)
                    <span class="text-xs text-gray-500">{{ number_format($assignment->course->enrollments_count ?? 0) }} students</span>
                    @endif
                </div>
                @if($assignment->course)
                <a href="{{ route('instructor.courses.show', $assignment->course_id) }}"
                   class="mt-3 inline-flex items-center text-xs font-medium text-brand-600 hover:text-brand-700">
                    View course →
                </a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('instructor.courses.create') }}" class="bg-white rounded-2xl border border-gray-100 p-6 hover:border-brand-200 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 bg-brand-50 rounded-xl flex items-center justify-center group-hover:bg-brand-100 transition-colors mb-3">
                <svg class="w-5 h-5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <p class="font-semibold text-gray-900">Create New Course</p>
            <p class="text-sm text-gray-500 mt-1">Add a new course to your catalogue</p>
        </a>
        <a href="{{ route('instructor.earnings') }}" class="bg-white rounded-2xl border border-gray-100 p-6 hover:border-brand-200 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center group-hover:bg-green-100 transition-colors mb-3">
                <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="font-semibold text-gray-900">View Earnings</p>
            <p class="text-sm text-gray-500 mt-1">Track your revenue and payments</p>
        </a>
        <a href="{{ route('instructor.students') }}" class="bg-white rounded-2xl border border-gray-100 p-6 hover:border-brand-200 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center group-hover:bg-purple-100 transition-colors mb-3">
                <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <p class="font-semibold text-gray-900">My Students</p>
            <p class="text-sm text-gray-500 mt-1">See who's enrolled in your courses</p>
        </a>
    </div>

</div>
@endsection
