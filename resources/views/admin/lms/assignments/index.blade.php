@extends('layouts.admin')
@section('title', 'Assignments')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Assignments — {{ $course->title }}</h1>
            <p class="text-gray-500 text-sm">Manage course assignments and student submissions</p>
        </div>
        <a href="{{ route('admin.courses.assignments.create', $course) }}" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-700">+ Add Assignment</a>
    </div>

    @if(session('success'))<div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>@endif

    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Title</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Max Score</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Pass Score</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Due Date</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Submissions</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($assignments as $assignment)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $assignment->title }}</div>
                        <div class="text-xs text-gray-400">{{ Str::limit($assignment->description, 60) }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $assignment->max_score }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $assignment->pass_score }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $assignment->due_date?->format('M d, Y') ?? 'No deadline' }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.assignments.submissions', $assignment) }}" class="text-brand-600 hover:underline text-xs">
                            {{ $assignment->submissions->count() }} submissions
                        </a>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.assignments.edit', $assignment) }}" class="text-gray-500 hover:underline text-xs">Edit</a>
                            <form action="{{ route('admin.assignments.destroy', $assignment) }}" method="POST" onsubmit="return confirm('Delete this assignment?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline text-xs">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">No assignments yet. <a href="{{ route('admin.courses.assignments.create', $course) }}" class="text-brand-600 hover:underline">Add one</a>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
