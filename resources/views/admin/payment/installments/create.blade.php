@extends('layouts.admin')
@section('title', 'Create Installment Plan')

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.installments.index') }}" class="text-brand-600 hover:underline text-sm">← Back to Plans</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Create Installment Plan</h1>
    </div>

    <div class="bg-white rounded-xl border p-6">
        <form action="{{ route('admin.installments.store') }}" method="POST" x-data="{ total: 0, down: 0 }">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Enrollment ID <span class="text-red-500">*</span></label>
                <input type="number" name="enrollment_id" value="{{ old('enrollment_id') }}" required
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500"
                    placeholder="Enter enrollment ID">
                @error('enrollment_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Course Amount (₦) <span class="text-red-500">*</span></label>
                    <input type="number" name="total_amount" step="0.01" min="1" value="{{ old('total_amount') }}" required
                        x-model="total"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Down Payment (₦) <span class="text-red-500">*</span></label>
                    <input type="number" name="down_payment" step="0.01" min="0" value="{{ old('down_payment') }}" required
                        x-model="down"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <div class="mb-4 p-3 bg-gray-50 rounded-lg text-sm" x-show="total > 0">
                <span class="text-gray-600">Remaining after down payment: </span>
                <span class="font-bold text-gray-900">₦<span x-text="(total - down).toLocaleString()"></span></span>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Number of Installments <span class="text-red-500">*</span></label>
                    <select name="installment_count" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ old('installment_count', 3) == $i ? 'selected' : '' }}>{{ $i }} {{ $i === 1 ? 'installment' : 'installments' }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grace Period (days)</label>
                    <input type="number" name="grace_period_days" min="0" max="30" value="{{ old('grace_period_days', 3) }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">First Payment Due Date <span class="text-red-500">*</span></label>
                <input type="date" name="first_due_date" value="{{ old('first_due_date', now()->addMonth()->format('Y-m-d')) }}" required
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-brand-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-brand-700">Create Plan</button>
                <a href="{{ route('admin.installments.index') }}" class="border px-6 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
