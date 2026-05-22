@extends('layouts.admin')
@section('title', 'Batches / Cohorts')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Batches & Cohorts</h1>
            <p class="text-gray-500 text-sm mt-1">Manage student cohorts and batch enrollments</p>
        </div>
        <a href="{{ route('admin.batches.create') }}" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-700">+ New Batch</a>
    </div>

    @if(session('success'))<div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>@endif

    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Batch Name</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Course</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Start Date</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">End Date</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Students</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Status</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($batches as $batch)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $batch->name }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $batch->course->title ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($batch->start_date)->format('M d, Y') }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $batch->end_date ? \Carbon\Carbon::parse($batch->end_date)->format('M d, Y') : '—' }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $batch->batchEnrollments->count() }} / {{ $batch->max_students ?? '∞' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $batch->status === 'active' ? 'bg-green-100 text-green-700' : ($batch->status === 'completed' ? 'bg-gray-100 text-gray-500' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($batch->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.batches.enrollments', $batch) }}" class="text-brand-600 hover:underline text-xs">Students</a>
                            <a href="{{ route('admin.batches.edit', $batch) }}" class="text-gray-500 hover:underline text-xs">Edit</a>
                            <form action="{{ route('admin.batches.destroy', $batch) }}" method="POST" onsubmit="return confirm('Delete this batch?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline text-xs">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">No batches yet. <a href="{{ route('admin.batches.create') }}" class="text-brand-600 hover:underline">Create one</a>.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($batches->hasPages())<div class="p-4 border-t">{{ $batches->links() }}</div>@endif
    </div>
</div>
@endsection
