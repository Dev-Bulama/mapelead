@extends('layouts.student')

@section('title', 'My Courses')
@section('page_title', 'My Courses')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $courses->total() }} course{{ $courses->total() !== 1 ? 's' : '' }} found</p>
        <a href="{{ route('instructor.courses.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create New Course
        </a>
    </div>

    @if($courses->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
        <svg class="mx-auto h-14 w-14 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <h3 class="mt-4 text-lg font-semibold text-gray-900">No courses yet</h3>
        <p class="mt-1 text-gray-500">Get started by creating your first course.</p>
        <a href="{{ route('instructor.courses.create') }}" class="mt-5 inline-flex items-center px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl transition-colors">
            Create Your First Course
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($courses as $course)
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden flex flex-col">
            <div class="aspect-video bg-gray-100 relative overflow-hidden">
                @if($course->thumbnail)
                    <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-brand-50 to-brand-100">
                        <svg class="w-12 h-12 text-brand-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.277A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />
                        </svg>
                    </div>
                @endif
                <div class="absolute top-2 right-2">
                    @if($course->status === 'published')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Published</span>
                    @elseif($course->status === 'rejected')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Rejected</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Draft</span>
                    @endif
                </div>
            </div>

            <div class="p-5 flex flex-col flex-1">
                <div class="flex items-center gap-2 mb-2">
                    @if($course->category)
                        <span class="text-xs font-medium text-brand-600 bg-brand-50 px-2 py-0.5 rounded-full">{{ $course->category->name }}</span>
                    @endif
                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full capitalize">{{ str_replace('_', ' ', $course->type ?? 'online') }}</span>
                </div>

                <h3 class="font-semibold text-gray-900 leading-snug line-clamp-2 flex-1">{{ $course->title }}</h3>

                <div class="mt-3 flex items-center justify-between text-sm text-gray-500">
                    <span class="capitalize">{{ str_replace('_', ' ', $course->level ?? 'all levels') }}</span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ $course->enrollments_count ?? 0 }} enrolled
                    </span>
                </div>

                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                    <span class="font-bold text-gray-900">
                        @if($course->is_free)
                            <span class="text-green-600">Free</span>
                        @else
                            ₦{{ number_format($course->price, 2) }}
                        @endif
                    </span>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('instructor.courses.edit', $course->id) }}" class="text-sm px-3 py-1.5 border border-gray-200 hover:border-gray-300 text-gray-600 hover:text-gray-800 rounded-lg transition-colors font-medium">Edit</a>
                        <a href="{{ route('instructor.courses.show', $course->id) }}" class="text-sm px-3 py-1.5 bg-brand-600 hover:bg-brand-700 text-white rounded-lg transition-colors font-medium">View</a>
                    </div>
                </div>

                <form action="{{ route('instructor.courses.destroy', $course->id) }}" method="POST" class="mt-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" x-on:click.prevent="if(confirm('Delete this course? This action cannot be undone.')) $el.closest('form').submit()" class="w-full text-xs text-red-500 hover:text-red-700 py-1 transition-colors">
                        Delete course
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div>
        {{ $courses->links() }}
    </div>
    @endif

</div>
@endsection
