@extends('layouts.admin')
@section('title', 'Modules — ' . $course->title)
@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('admin.courses.index') }}" class="hover:text-brand-600">Courses</a>
                <span>/</span>
                <span class="text-gray-700 font-medium">{{ Str::limit($course->title, 40) }}</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Course Modules</h1>
        </div>
        <a href="{{ route('admin.courses.modules.create', $course) }}"
            class="inline-flex items-center gap-2 bg-brand-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-brand-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Module
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl border overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <p class="text-sm text-gray-600">{{ $modules->count() }} module(s) · {{ $modules->sum('lessons_count') }} lessons total</p>
        </div>
        @if($modules->isEmpty())
        <div class="p-12 text-center text-gray-400">
            <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <p class="text-sm font-medium">No modules yet</p>
            <p class="text-xs mt-1">Add the first module to structure this course.</p>
        </div>
        @else
        <table class="w-full text-sm">
            <thead class="text-xs text-gray-500 uppercase tracking-wide border-b bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left w-10">#</th>
                    <th class="px-4 py-3 text-left">Module Title</th>
                    <th class="px-4 py-3 text-center">Lessons</th>
                    <th class="px-4 py-3 text-center">Free Preview</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($modules as $module)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-400 font-mono text-xs">{{ $module->sort_order }}</td>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-900">{{ $module->title }}</p>
                        @if($module->description)
                        <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($module->description, 80) }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.modules.lessons.index', $module) }}"
                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-semibold hover:bg-blue-100 transition-colors">
                            {{ $module->lessons_count }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($module->is_free_preview)
                        <span class="inline-flex items-center px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">Yes</span>
                        @else
                        <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.modules.lessons.index', $module) }}"
                                class="text-xs text-blue-600 hover:underline">Lessons</a>
                            <a href="{{ route('admin.modules.edit', $module) }}"
                                class="text-xs text-gray-600 hover:underline">Edit</a>
                            <form action="{{ route('admin.modules.destroy', $module) }}" method="POST"
                                onsubmit="return confirm('Delete this module and all its lessons?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-500 hover:underline">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
