@extends('layouts.admin')
@section('title', 'Installment Plans')

@section('content')
<div class="p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Installment Plans</h1>
            <p class="text-gray-500 text-sm mt-1">Manage student payment installments</p>
        </div>
        <a href="{{ route('admin.installments.create') }}" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-700 transition">
            + New Plan
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @php
            $total = $installmentPlans->total();
            $active = $installmentPlans->where('status', 'active')->count();
            $overdue = $installmentPlans->where('status', 'overdue')->count();
            $completed = $installmentPlans->where('status', 'completed')->count();
        @endphp
        <div class="bg-white rounded-xl border p-4"><p class="text-xs text-gray-500">Total Plans</p><p class="text-2xl font-bold text-gray-900">{{ $total }}</p></div>
        <div class="bg-white rounded-xl border p-4"><p class="text-xs text-gray-500">Active</p><p class="text-2xl font-bold text-brand-600">{{ $active }}</p></div>
        <div class="bg-white rounded-xl border p-4"><p class="text-xs text-gray-500">Overdue</p><p class="text-2xl font-bold text-red-600">{{ $overdue }}</p></div>
        <div class="bg-white rounded-xl border p-4"><p class="text-xs text-gray-500">Completed</p><p class="text-2xl font-bold text-green-600">{{ $completed }}</p></div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Student</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Course</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Total</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Paid</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Balance</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($installmentPlans as $plan)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $plan->enrollment->user->full_name ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-400">{{ $plan->enrollment->user->admission_number ?? '' }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $plan->enrollment->course->title ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-gray-900 font-medium">₦{{ number_format($plan->total_amount, 0) }}</td>
                    <td class="px-4 py-3 text-green-600">₦{{ number_format($plan->amount_paid, 0) }}</td>
                    <td class="px-4 py-3 text-red-600 font-medium">₦{{ number_format($plan->outstanding_balance, 0) }}</td>
                    <td class="px-4 py-3">
                        @php
                            $statusColors = ['active'=>'blue','completed'=>'green','overdue'=>'red','defaulted'=>'gray','cancelled'=>'gray'];
                            $color = $statusColors[$plan->status] ?? 'gray';
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-700">{{ ucfirst($plan->status) }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.installments.show', $plan) }}" class="text-brand-600 hover:underline text-xs">View</a>
                            @if($plan->enrollment && $plan->enrollment->access_locked)
                                <form action="{{ route('admin.installments.unlock', $plan) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="text-green-600 hover:underline text-xs">Unlock</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">No installment plans found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($installmentPlans->hasPages())
        <div class="p-4 border-t">{{ $installmentPlans->links() }}</div>
        @endif
    </div>
</div>
@endsection
