@extends('layouts.admin')
@section('title', 'Edit Batch')

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.batches.index') }}" class="text-brand-600 hover:underline text-sm">← Back to Batches</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit: {{ $batch->name }}</h1>
    </div>
    <div class="bg-white rounded-xl border p-6">
        <form action="{{ route('admin.batches.update', $batch) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Batch Name</label>
                <input type="text" name="name" value="{{ old('name', $batch->name) }}" required
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $batch->start_date) }}" required
                        class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $batch->end_date) }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Students</label>
                    <input type="number" name="max_students" value="{{ old('max_students', $batch->max_students) }}" min="1"
                        class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm">
                        @foreach(['active','inactive','completed'] as $s)
                        <option value="{{ $s }}" {{ $batch->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-brand-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-brand-700">Save Changes</button>
                <a href="{{ route('admin.batches.index') }}" class="border px-6 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
