@extends('layouts.student')

@section('title', 'My Earnings')
@section('page_title', 'My Earnings')

@section('content')
@php
    $pageTotal = $payments->sum('amount');
    $pageCount = $payments->count();
@endphp

<div class="space-y-6">

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Total on This Page</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">₦{{ number_format($pageTotal, 2) }}</p>
            <p class="mt-1 text-xs text-gray-400">Showing current page results only</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Payments on This Page</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">{{ $pageCount }}</p>
            <p class="mt-1 text-xs text-gray-400">of {{ $payments->total() }} total payments</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Payment History</h3>

        @if($payments->isEmpty())
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-4 text-base font-semibold text-gray-900">No earnings yet</h3>
            <p class="mt-1 text-sm text-gray-500">Publish your course to start earning.</p>
            <a href="{{ route('instructor.courses.index') }}" class="mt-4 inline-flex items-center px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl transition-colors">
                Manage Courses
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Date</th>
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Course</th>
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Student</th>
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Amount</th>
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Gateway</th>
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Status</th>
                        <th class="text-left py-3 px-2 font-medium text-gray-500">Reference</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($payments as $payment)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-2 text-gray-600 whitespace-nowrap">
                            {{ $payment->created_at->format('M d, Y') }}
                        </td>
                        <td class="py-3 px-2 text-gray-800 max-w-[180px]">
                            <span class="truncate block">{{ optional(optional($payment->enrollment)->course)->title ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-2 text-gray-600">
                            {{ optional(optional($payment->enrollment)->user)->name ?? 'Student' }}
                        </td>
                        <td class="py-3 px-2 font-semibold text-gray-900 whitespace-nowrap">
                            ₦{{ number_format($payment->amount, 2) }}
                        </td>
                        <td class="py-3 px-2 text-gray-500 capitalize">
                            {{ $payment->gateway ?? '-' }}
                        </td>
                        <td class="py-3 px-2">
                            @if($payment->status === 'successful' || $payment->status === 'success')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Successful</span>
                            @elseif($payment->status === 'pending')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Pending</span>
                            @elseif($payment->status === 'failed')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Failed</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 capitalize">{{ $payment->status }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-2 text-gray-400 text-xs font-mono">
                            {{ Str::limit($payment->reference ?? '-', 16) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $payments->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
