@extends('layouts.admin')
@section('title', 'Create Assignment')

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.courses.assignments.index', $course) }}" class="text-brand-600 hover:underline text-sm">← Back to Assignments</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Create Assignment</h1>
        <p class="text-gray-500 text-sm">{{ $course->title }}</p>
    </div>
    <div class="bg-white rounded-xl border p-6">
        <form action="{{ route('admin.courses.assignments.store', $course) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm">{{ old('description') }}</textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Instructions (HTML allowed)</label>
                <textarea name="instructions" rows="5" class="w-full border rounded-lg px-3 py-2 text-sm font-mono text-xs">{{ old('instructions') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Score</label>
                    <input type="number" name="max_score" value="{{ old('max_score', 100) }}" min="1"
                        class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pass Score</label>
                    <input type="number" name="pass_score" value="{{ old('pass_score', 50) }}" min="0"
                        class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Attempts</label>
                    <input type="number" name="max_attempts" value="{{ old('max_attempts', 1) }}" min="1"
                        class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Allowed File Types</label>
                    <input type="text" name="allowed_file_types" value="{{ old('allowed_file_types') }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="pdf,doc,docx,zip">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max File Size (MB)</label>
                    <input type="number" name="max_file_size_mb" value="{{ old('max_file_size_mb', 10) }}" min="1"
                        class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div class="mb-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_required_for_certificate" value="1"
                        {{ old('is_required_for_certificate') ? 'checked' : '' }}
                        class="w-4 h-4 rounded text-brand-600">
                    <span class="text-sm font-medium text-gray-700">Required for Certificate</span>
                </label>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-brand-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-brand-700">Create Assignment</button>
                <a href="{{ route('admin.courses.assignments.index', $course) }}" class="border px-6 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
