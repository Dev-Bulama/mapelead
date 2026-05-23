@extends('layouts.admin')
@section('title', 'Edit Enrollment #' . $enrollment->id)

@section('content')
<div class="space-y-6 max-w-2xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.enrollments.index') }}" class="hover:text-brand-600">Admissions</a>
        <span>/</span>
        <a href="{{ route('admin.enrollments.show', $enrollment) }}" class="hover:text-brand-600">
            #{{ $enrollment->id }} — {{ $enrollment->user->full_name ?? '' }}
        </a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Edit</span>
    </div>

    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
        <ul class="space-y-1">@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <div class="flex items-center gap-3">
                <img src="{{ $enrollment->user->avatar_url }}" alt="" class="w-10 h-10 rounded-full object-cover bg-gray-200">
                <div>
                    <h2 class="font-bold text-gray-900">{{ $enrollment->user->full_name }}</h2>
                    <p class="text-sm text-gray-500">{{ $enrollment->course->title ?? '—' }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.enrollments.update', $enrollment) }}" method="POST" class="p-6 space-y-5">
            @csrf @method('PUT')

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Enrollment Status</label>
                    <select name="status" class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-brand-500">
                        @foreach(['pending' => 'Pending', 'active' => 'Active', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
                        <option value="{{ $val }}" {{ old('status', $enrollment->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Payment Status</label>
                    <select name="payment_status" class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-brand-500">
                        @foreach(['unpaid' => 'Unpaid', 'paid' => 'Paid', 'partial' => 'Partial', 'refunded' => 'Refunded'] as $val => $label)
                        <option value="{{ $val }}" {{ old('payment_status', $enrollment->payment_status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Amount Paid (₦)</label>
                    <input type="number" name="amount_paid" step="0.01" min="0"
                           value="{{ old('amount_paid', $enrollment->amount_paid) }}"
                           class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Reassign Batch</label>
                    <select name="batch_id" form="batch-form"
                            class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-brand-500">
                        <option value="">No batch</option>
                        @foreach($availableBatches as $batch)
                        <option value="{{ $batch->id }}"
                            {{ $enrollment->batch?->batch_id == $batch->id ? 'selected' : '' }}>
                            {{ $batch->name }}
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Submit via the "Reassign Batch" button below to change batch separately.</p>
                </div>
            </div>

            <div class="pt-2 flex items-center gap-3">
                <button type="submit"
                        class="bg-brand-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-brand-700 transition-colors">
                    Save Changes
                </button>
                <a href="{{ route('admin.enrollments.show', $enrollment) }}"
                   class="text-gray-500 hover:text-gray-700 text-sm font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    {{-- Batch reassign form (separate POST) --}}
    <form id="batch-form" action="{{ route('admin.enrollments.reassign-batch', $enrollment) }}" method="POST" class="hidden">
        @csrf
    </form>
    <div class="bg-white rounded-xl border p-5">
        <h3 class="font-semibold text-gray-900 mb-3 text-sm">Reassign Batch</h3>
        <form action="{{ route('admin.enrollments.reassign-batch', $enrollment) }}" method="POST" class="flex gap-3">
            @csrf
            <select name="batch_id" class="flex-1 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                <option value="">No batch</option>
                @foreach($availableBatches as $batch)
                <option value="{{ $batch->id }}"
                    {{ $enrollment->batch?->batch_id == $batch->id ? 'selected' : '' }}>
                    {{ $batch->name }}
                    @if($batch->max_students) ({{ max(0, $batch->max_students - $batch->current_students) }} seats left) @endif
                </option>
                @endforeach
            </select>
            <button type="submit"
                    class="bg-brand-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-brand-700 transition-colors whitespace-nowrap">
                Reassign Batch
            </button>
        </form>
    </div>

    {{-- Danger Zone --}}
    <div class="bg-white rounded-xl border border-red-200 p-5">
        <h3 class="font-semibold text-red-700 mb-3 text-sm">Danger Zone</h3>
        <form action="{{ route('admin.enrollments.destroy', $enrollment) }}" method="POST"
              onsubmit="return confirm('Permanently delete this enrollment? This cannot be undone.')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="px-4 py-2 border border-red-300 text-red-700 rounded-lg text-sm font-medium hover:bg-red-50 transition-colors">
                Delete Enrollment
            </button>
        </form>
    </div>

</div>
@endsection
