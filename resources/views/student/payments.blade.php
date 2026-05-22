@extends('layouts.student')
@section('title', 'Payment History')
@section('page_title', 'Payments')

@section('content')
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
@endsection
