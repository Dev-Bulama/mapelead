@extends('layouts.student')
@section('title', 'Payment History')
@section('page_title', 'Payments')

@section('content')
<div class="space-y-6">

{{-- Installment Plans Section --}}
@if(isset($installmentPlans) && $installmentPlans->isNotEmpty())
<div class="space-y-4">
    <h2 class="text-base font-bold text-gray-900">Active Installment Plans</h2>
    @foreach($installmentPlans as $plan)
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        {{-- Plan Header --}}
        <div class="p-5 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="font-bold text-gray-900">{{ $plan->course->title ?? 'Course' }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $plan->installment_count }} installment plan</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $plan->status === 'overdue' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                {{ ucfirst($plan->status) }}
            </span>
        </div>

        {{-- Payment Summary --}}
        <div class="grid grid-cols-3 divide-x divide-gray-100 border-b border-gray-100">
            <div class="p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Total</p>
                <p class="font-bold text-gray-900 text-sm">₦{{ number_format($plan->total_amount, 0) }}</p>
            </div>
            <div class="p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Paid</p>
                <p class="font-bold text-green-600 text-sm">₦{{ number_format($plan->amount_paid, 0) }}</p>
            </div>
            <div class="p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Remaining</p>
                <p class="font-bold text-red-600 text-sm">₦{{ number_format($plan->outstanding_balance, 0) }}</p>
            </div>
        </div>

        {{-- Schedule --}}
        <div class="p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Payment Schedule</p>
            <div class="space-y-2">
                @foreach($plan->schedule as $item)
                <div class="flex items-center justify-between p-3 rounded-xl
                    {{ $item->status === 'paid' ? 'bg-green-50' : ($item->status === 'overdue' ? 'bg-red-50' : 'bg-gray-50') }}">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0
                            {{ $item->status === 'paid' ? 'bg-green-500' : ($item->status === 'overdue' ? 'bg-red-500' : 'bg-gray-300') }}">
                            @if($item->status === 'paid')
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            @elseif($item->status === 'overdue')
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/></svg>
                            @else
                                <span class="text-white text-xs font-bold">{{ $item->installment_number }}</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-medium {{ $item->status === 'paid' ? 'text-green-700' : ($item->status === 'overdue' ? 'text-red-700' : 'text-gray-700') }}">
                                Installment #{{ $item->installment_number }}
                                @if($item->status === 'overdue') <span class="text-xs font-normal">(Overdue)</span> @endif
                            </p>
                            <p class="text-xs text-gray-400">Due: {{ $item->due_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-sm {{ $item->status === 'paid' ? 'text-green-700' : ($item->status === 'overdue' ? 'text-red-700' : 'text-gray-700') }}">
                            ₦{{ number_format($item->amount, 0) }}
                        </p>
                        @if($item->status === 'paid' && $item->paid_at)
                            <p class="text-xs text-gray-400">Paid {{ $item->paid_at->format('M d') }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            @if($plan->enrollment && $plan->enrollment->access_locked)
            <div class="mt-3 p-3 bg-red-50 rounded-xl border border-red-200">
                <p class="text-sm font-medium text-red-800">Your course access is locked</p>
                <p class="text-xs text-red-600 mt-0.5">{{ $plan->enrollment->access_locked_reason }}</p>
                <p class="text-xs text-gray-500 mt-2">Contact support to make a payment and restore access.</p>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Transaction History --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100">
        <h2 class="font-bold text-gray-900">Payment History</h2>
    </div>
    @if($payments->isEmpty())
        <div class="p-12 text-center text-gray-400">No payment records found.</div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="text-left px-5 py-3">Reference</th>
                        <th class="text-left px-5 py-3">Course</th>
                        <th class="text-left px-5 py-3">Amount</th>
                        <th class="text-left px-5 py-3">Method</th>
                        <th class="text-left px-5 py-3">Status</th>
                        <th class="text-left px-5 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $payment->reference }}</td>
                            <td class="px-5 py-3 text-gray-900 font-medium">{{ $payment->enrollment->course->title ?? '—' }}</td>
                            <td class="px-5 py-3 font-semibold text-gray-900">₦{{ number_format($payment->amount) }}</td>
                            <td class="px-5 py-3 text-gray-500 capitalize">{{ $payment->gateway }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $payment->status === 'success' ? 'bg-green-100 text-green-700' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-400">{{ $payment->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $payments->links() }}</div>
    @endif
</div>

</div>{{-- end space-y-6 --}}
@endsection
