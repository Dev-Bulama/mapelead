@extends('layouts.student')
@section('title', 'My Courses')
@section('page_title', 'My Courses')

@section('content')
<div class="space-y-6">
    @if($enrollments->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
            <div class="w-20 h-20 bg-brand-50 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <svg class="w-10 h-10 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900">No courses yet</h3>
            <p class="text-gray-500 mt-2 mb-6">Start your learning journey by enrolling in a course.</p>
            <a href="{{ route('courses.index') }}" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-3 rounded-xl transition-colors inline-block">
                Browse Courses
            </a>
        </div>
    @else
        {{-- Filter tabs --}}
        <div x-data="{ tab: 'all' }" class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 p-1 inline-flex gap-1">
                @foreach(['all' => 'All Courses', 'active' => 'In Progress', 'completed' => 'Completed'] as $key => $label)
                    <button @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}' ? 'bg-brand-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
                            class="px-4 py-2 rounded-xl text-sm font-medium transition-colors">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($enrollments as $enrollment)
                    <div x-show="tab === 'all' || tab === '{{ $enrollment->status }}'"
                         class="bg-white rounded-2xl border border-gray-100 overflow-hidden card-hover">
                        <div class="aspect-video relative bg-gray-100">
                            <img src="{{ $enrollment->course->thumbnail_url }}" alt="{{ $enrollment->course->title }}" class="w-full h-full object-cover">
                            @if($enrollment->status === 'completed')
                                <div class="absolute inset-0 bg-green-900/60 flex items-center justify-center">
                                    <div class="text-center text-white">
                                        <svg class="w-10 h-10 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span class="text-sm font-bold">Completed!</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <span class="text-xs text-brand-600 font-medium">{{ $enrollment->course->category->name ?? '' }}</span>
                            <h3 class="font-bold text-gray-900 mt-1 line-clamp-2">{{ $enrollment->course->title }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ $enrollment->course->instructor?->user?->full_name ?? 'Instructor' }}</p>

                            <div class="mt-3">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Progress</span><span class="font-semibold">{{ $enrollment->progress_percent }}%</span>
                                </div>
                                <div class="bg-gray-200 rounded-full h-2">
                                    <div class="bg-brand-600 h-2 rounded-full" style="width:{{ $enrollment->progress_percent }}%"></div>
                                </div>
                            </div>

                            <div class="flex gap-2 mt-3">
                                <a href="{{ route('student.learn', $enrollment->course->slug) }}"
                                   class="flex-1 text-center bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold py-2 rounded-lg transition-colors">
                                    {{ $enrollment->progress_percent > 0 ? 'Continue' : 'Start' }} Learning
                                </a>
                                <a href="{{ route('courses.show', $enrollment->course->slug) }}"
                                   class="px-3 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
