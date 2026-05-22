@extends('layouts.admin')
@section('title', 'Edit Lead')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Update Lead</h2>
        <a href="{{ route('admin.leads.show', $lead->id) }}" class="border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2.5 rounded-xl hover:text-gray-900 transition-colors">← Back</a>
    </div>

    <form action="{{ route('admin.leads.update', $lead->id) }}" method="POST" class="space-y-6">
        @csrf @method('PUT')

        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
            <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide border-b border-gray-100 pb-3">Lead Details</h3>
            <p class="text-sm text-gray-500">Lead: <span class="font-medium text-gray-900">{{ $lead->first_name }} {{ $lead->last_name }} ({{ $lead->email }})</span></p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        @foreach(['new' => 'New', 'contacted' => 'Contacted', 'qualified' => 'Qualified', 'enrolled' => 'Enrolled', 'lost' => 'Lost'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status', $lead->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Assign To</label>
                    <input type="number" name="assigned_to" value="{{ old('assigned_to', $lead->assigned_to) }}" placeholder="User ID"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Notes</label>
                <textarea name="notes" rows="5"
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                          placeholder="Follow-up notes, conversation summary...">{{ old('notes', $lead->notes) }}</textarea>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">Save Changes</button>
            <a href="{{ route('admin.leads.show', $lead->id) }}" class="text-gray-600 font-medium px-4 py-2.5 rounded-xl hover:bg-gray-100 transition-colors">Cancel</a>
        </div>
    </form>

</div>
@endsection
