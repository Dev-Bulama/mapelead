@extends('layouts.admin')
@section('title', 'Payments')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Payments</h1>
        <p class="text-sm text-gray-500 mt-0.5">Track all transactions and revenue.</p>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm p-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm p-4">{{ session('error') }}</div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border p-5">
            <p class="text-xs text-gray-500 mb-1">Total Revenue</p>
            <p class="text-2xl font-bold text-gray-900">&#8358;{{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl border p-5">
            <p class="text-xs text-gray-500 mb-1">Today's Revenue</p>
            <p class="text-2xl font-bold" style="color:#14215B">&#8358;{{ number_format($todayRevenue, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl border p-5">
            <p class="text-xs text-gray-500 mb-1">Total Transactions</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalCount) }}</p>
        </div>
        <div class="bg-white rounded-xl border p-5">
            <p class="text-xs text-gray-500 mb-1">Pending</p>
            <p class="text-2xl font-bold text-yellow-600">{{ number_format($pendingCount) }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.payments.index') }}" class="bg-white rounded-xl border p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by reference, name or email..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:border-transparent"
                    style="--tw-ring-color: #14215B;">
            </div>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
                <option value="">All Statuses</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="success" @selected(request('status') === 'success')>Success</option>
                <option value="failed" @selected(request('status') === 'failed')>Failed</option>
                <option value="refunded" @selected(request('status') === 'refunded')>Refunded</option>
            </select>
            <select name="gateway" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
                <option value="">All Gateways</option>
                <option value="paystack" @selected(request('gateway') === 'paystack')>Paystack</option>
                <option value="manual" @selected(request('gateway') === 'manual')>Manual</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium text-white hover:opacity-90 transition" style="background-color:#14215B;">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'gateway']))
            <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                Clear
            </a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Student</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Course</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Gateway</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm font-mono text-gray-700">{{ $payment->reference }}</td>
                        <td class="px-4 py-3">
                            @if($payment->user)
                            <div class="text-sm font-medium text-gray-900">{{ $payment->user->first_name }} {{ $payment->user->last_name }}</div>
                            <div class="text-xs text-gray-500">{{ $payment->user->email }}</div>
                            @else
                            <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            {{ $payment->enrollment?->course?->title ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                            &#8358;{{ number_format($payment->amount, 2) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 capitalize">
                            {{ $payment->gateway ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusMap = [
                                    'success'  => 'bg-green-100 text-green-700',
                                    'pending'  => 'bg-yellow-100 text-yellow-700',
                                    'failed'   => 'bg-red-100 text-red-700',
                                    'refunded' => 'bg-gray-100 text-gray-600',
                                ];
                                $cls = $statusMap[$payment->status] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $cls }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $payment->created_at->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.payments.show', $payment) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-medium text-white hover:opacity-90 transition"
                               style="background-color:#14215B;">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-sm text-gray-500">No payments found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $payments->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
