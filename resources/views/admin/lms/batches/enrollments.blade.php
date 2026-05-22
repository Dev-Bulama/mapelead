@extends('layouts.admin')
@section('title', 'Batch Enrollments')

@section('content')
<div class="p-6 max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.batches.index') }}" class="text-brand-600 hover:underline text-sm">← Back to Batches</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $batch->name }} — Students</h1>
        <p class="text-gray-500 text-sm">{{ $batch->course->title ?? '' }}</p>
    </div>

    @if(session('success'))<div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">{{ session('error') }}</div>@endif

    {{-- Add Student Form --}}
    <div class="bg-white rounded-xl border p-5 mb-6">
        <h3 class="font-semibold text-gray-900 mb-3">Add Student to Batch</h3>
        <form action="{{ route('admin.batches.students.add', $batch) }}" method="POST" class="flex gap-3">
            @csrf
            <input type="number" name="enrollment_id" placeholder="Enrollment ID" required
                class="flex-1 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
            <button type="submit" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-700">Add</button>
        </form>
    </div>

    {{-- Students List --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Enrolled Students ({{ $batch->batchEnrollments->count() }})</h3>
        </div>
        <div class="divide-y">
            @forelse($batch->batchEnrollments as $be)
            <div class="flex items-center justify-between px-5 py-3">
                <div class="flex items-center gap-3">
                    <img src="{{ $be->enrollment->user->avatar_url ?? '' }}" class="w-8 h-8 rounded-full object-cover bg-gray-200">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $be->enrollment->user->full_name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $be->enrollment->user->admission_number ?? $be->enrollment->user->email ?? '' }}</p>
                    </div>
                </div>
                <form action="{{ route('admin.batches.students.remove', $be) }}" method="POST" onsubmit="return confirm('Remove from batch?')">
                    @csrf @method('DELETE')
                    <button class="text-red-500 hover:underline text-xs">Remove</button>
                </form>
            </div>
            @empty
            <div class="px-5 py-12 text-center text-gray-400">No students in this batch yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
