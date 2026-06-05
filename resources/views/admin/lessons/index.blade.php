@extends('layouts.admin')
@section('title', 'Lessons — ' . $module->title)
@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('admin.courses.modules.index', $module->course_id) }}" class="hover:text-brand-600">Modules</a>
                <span>/</span>
                <span class="text-gray-700 font-medium">{{ Str::limit($module->title, 40) }}</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Lessons</h1>
        </div>
        <a href="{{ route('admin.modules.lessons.create', $module) }}"
            class="inline-flex items-center gap-2 bg-brand-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-brand-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Lesson
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl border overflow-hidden">
        @if($lessons->isEmpty())
        <div class="p-12 text-center text-gray-400">
            <p class="text-sm font-medium">No lessons yet</p>
        </div>
        @else
        <table class="w-full text-sm">
            <thead class="text-xs text-gray-500 uppercase tracking-wide border-b bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left w-10">#</th>
                    <th class="px-4 py-3 text-left">Lesson</th>
                    <th class="px-4 py-3 text-center">Type</th>
                    <th class="px-4 py-3 text-center">Duration</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($lessons as $lesson)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-400 font-mono text-xs">{{ $lesson->sort_order }}</td>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-900">{{ $lesson->title }}</p>
                        @if($lesson->is_free_preview)
                        <span class="text-xs text-green-600 font-medium">Free Preview</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @php $typeColors = ['video'=>'blue','text'=>'gray','quiz'=>'purple','assignment'=>'orange','live'=>'red']; @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            bg-{{ $typeColors[$lesson->type] ?? 'gray' }}-100 text-{{ $typeColors[$lesson->type] ?? 'gray' }}-700">
                            {{ ucfirst($lesson->type) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-xs text-gray-500">
                        {{ $lesson->duration_minutes ? $lesson->duration_minutes . ' min' : '—' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($lesson->is_published)
                        <span class="inline-flex items-center px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">Published</span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Draft</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.lessons.edit', $lesson) }}"
                                class="text-xs text-gray-600 hover:underline">Edit</a>
                            <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST"
                                onsubmit="return confirm('Delete this lesson?')">
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
