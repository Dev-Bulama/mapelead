@extends('layouts.admin')
@section('title', 'Installment Plans')

@section('content')
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Installment Plans</h1>
            <p class="text-gray-500 text-sm mt-1">Track all part-payment plans — who has paid, what's owed, and overdue alerts.</p>
        </div>
        <a href="{{ route('admin.installments.create') }}" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-700 transition">
            + New Plan
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    {{-- Stats (from full DB, not paginated page) --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl border p-4">
            <p class="text-xs text-gray-500">Total Plans</p>
            <p class="text-2xl font-bold text-gray-900">{{ $globalStats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <p class="text-xs text-gray-500">Active</p>
            <p class="text-2xl font-bold text-blue-600">{{ $globalStats['active'] }}</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <p class="text-xs text-gray-500">Overdue</p>
            <p class="text-2xl font-bold text-red-600">{{ $globalStats['overdue'] }}</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <p class="text-xs text-gray-500">Completed</p>
            <p class="text-2xl font-bold text-green-600">{{ $globalStats['completed'] }}</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <p class="text-xs text-gray-500">Total Collected</p>
            <p class="text-xl font-bold text-green-700">₦{{ number_format($globalStats['revenue'], 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <p class="text-xs text-gray-500">Outstanding</p>
            <p class="text-xl font-bold text-red-600">₦{{ number_format($globalStats['outstanding'], 0) }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Student</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Course</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Plan</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Progress</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Paid</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Balance</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Access</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($installmentPlans as $plan)
                    @php
                        $paidCount  = $plan->paid_count ?? 0;
                        $totalCount = $plan->total_count ?? $plan->installment_count;
                        $paidPct    = $plan->total_amount > 0
                            ? round(($plan->amount_paid / $plan->total_amount) * 100)
                            : 0;
                        $statusColors = [
                            'active'    => 'bg-blue-100 text-blue-700',
                            'completed' => 'bg-green-100 text-green-700',
                            'overdue'   => 'bg-red-100 text-red-700',
                            'defaulted' => 'bg-gray-100 text-gray-500',
                            'cancelled' => 'bg-gray-100 text-gray-500',
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50 {{ $plan->status === 'overdue' ? 'bg-red-50/40' : '' }}">

                        {{-- Student --}}
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">
                                {{ $plan->enrollment->user->full_name ?? 'N/A' }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $plan->enrollment->user->email ?? '' }}
                            </div>
                            @if($plan->enrollment->user->admission_number ?? null)
                            <div class="text-xs font-mono text-gray-400 mt-0.5">
                                {{ $plan->enrollment->user->admission_number }}
                            </div>
                            @endif
                        </td>

                        {{-- Course --}}
                        <td class="px-4 py-3 text-gray-700 max-w-[140px]">
                            <p class="truncate">{{ $plan->enrollment->course->title ?? 'N/A' }}</p>
                        </td>

                        {{-- Plan summary: total + down payment --}}
                        <td class="px-4 py-3">
                            <div class="text-gray-900 font-medium">₦{{ number_format($plan->total_amount, 0) }}</div>
                            <div class="text-xs text-gray-400">Down: ₦{{ number_format($plan->down_payment, 0) }}</div>
                            <div class="text-xs text-gray-400">{{ $plan->installment_count }} installments</div>
                        </td>

                        {{-- Progress: bar + count --}}
                        <td class="px-4 py-3 min-w-[120px]">
                            <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                <span class="font-medium">{{ $paidCount }} / {{ $totalCount }} paid</span>
                                <span>{{ $paidPct }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full {{ $plan->status === 'overdue' ? 'bg-red-500' : ($plan->status === 'completed' ? 'bg-green-500' : 'bg-blue-500') }}"
                                     style="width: {{ $paidPct }}%"></div>
                            </div>
                        </td>

                        {{-- Paid --}}
                        <td class="px-4 py-3 text-green-700 font-semibold">
                            ₦{{ number_format($plan->amount_paid, 0) }}
                        </td>

                        {{-- Balance --}}
                        <td class="px-4 py-3 {{ $plan->outstanding_balance > 0 ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                            {{ $plan->outstanding_balance > 0 ? '₦' . number_format($plan->outstanding_balance, 0) : '—' }}
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusColors[$plan->status] ?? 'bg-gray-100 text-gray-500' }}">
                                {{ ucfirst($plan->status) }}
                            </span>
                        </td>

                        {{-- Access locked? --}}
                        <td class="px-4 py-3">
                            @if($plan->enrollment && $plan->enrollment->access_locked)
                                <span class="flex items-center gap-1 text-xs font-medium text-red-700">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Locked
                                </span>
                            @else
                                <span class="flex items-center gap-1 text-xs text-green-700">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                    </svg>
                                    Active
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3">
                            <div class="flex gap-2 items-center">
                                <a href="{{ route('admin.installments.show', $plan) }}"
                                   class="text-brand-600 hover:underline text-xs font-medium">View</a>

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
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-gray-400">No installment plans found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($installmentPlans->hasPages())
        <div class="p-4 border-t">{{ $installmentPlans->links() }}</div>
        @endif
    </div>
</div>
@endsection
