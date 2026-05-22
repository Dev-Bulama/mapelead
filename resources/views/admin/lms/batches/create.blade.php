@extends('layouts.admin')
@section('title', 'Create Batch')

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.batches.index') }}" class="text-brand-600 hover:underline text-sm">← Back to Batches</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Create New Batch</h1>
    </div>
    <div class="bg-white rounded-xl border p-6">
        <form action="{{ route('admin.batches.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Batch Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Cohort 7 — Jan 2026"
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Course <span class="text-red-500">*</span></label>
                <select name="course_id" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                    <option value="">Select a course...</option>
                    @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Students</label>
                    <input type="number" name="max_students" value="{{ old('max_students') }}" min="1"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="Leave blank for unlimited">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                        <option value="active" {{ old('status','active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-brand-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-brand-700">Create Batch</button>
                <a href="{{ route('admin.batches.index') }}" class="border px-6 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
