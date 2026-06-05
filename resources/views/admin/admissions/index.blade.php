@extends('layouts.admin')
@section('title', 'Admission Management')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Admission Management</h2>
            <p class="text-sm text-gray-500 mt-0.5">View, approve, reject, and manage all student enrollments.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm">{{ session('error') }}</div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        @foreach([
            ['Total', $stats['total'], 'bg-blue-50', 'text-blue-700', 'border-blue-200'],
            ['Active', $stats['active'], 'bg-green-50', 'text-green-700', 'border-green-200'],
            ['Pending', $stats['pending'], 'bg-yellow-50', 'text-yellow-700', 'border-yellow-200'],
            ['Locked', $stats['locked'], 'bg-red-50', 'text-red-700', 'border-red-200'],
            ['Completed', $stats['completed'], 'bg-purple-50', 'text-purple-700', 'border-purple-200'],
        ] as [$label, $count, $bg, $text, $border])
        <div class="{{ $bg }} border {{ $border }} rounded-xl p-4 text-center">
            <p class="text-2xl font-bold {{ $text }}">{{ $count }}</p>
            <p class="text-xs text-gray-500 mt-0.5 font-medium">{{ $label }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.enrollments.index') }}"
          class="bg-white rounded-xl border p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search name, email, admission no…"
               class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 lg:col-span-2">

        <select name="course_id" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
            <option value="">All Courses</option>
            @foreach($courses as $c)
            <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
            @endforeach
        </select>

        <select name="payment_status" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
            <option value="">All Payment Status</option>
            @foreach(['unpaid' => 'Unpaid', 'paid' => 'Paid', 'partial' => 'Partial', 'refunded' => 'Refunded'] as $val => $label)
            <option value="{{ $val }}" {{ request('payment_status') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <select name="status" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
            <option value="">All Statuses</option>
            @foreach(['pending' => 'Pending', 'active' => 'Active', 'completed' => 'Completed', 'cancelled' => 'Cancelled/Rejected', 'locked' => 'Access Locked'] as $val => $label)
            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <div class="flex gap-2 lg:col-span-5">
            <button type="submit" class="bg-brand-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-brand-700 transition-colors">
                Filter
            </button>
            <a href="{{ route('admin.enrollments.index') }}" class="border border-gray-300 text-gray-600 px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">
                Reset
            </a>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">
                Enrollments
                <span class="text-gray-400 font-normal text-sm ml-2">{{ $enrollments->total() }} total</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left">Student</th>
                        <th class="px-4 py-3 text-left">Admission No.</th>
                        <th class="px-4 py-3 text-left">Course</th>
                        <th class="px-4 py-3 text-left">Batch</th>
                        <th class="px-4 py-3 text-left">Payment</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Enrolled</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($enrollments as $enrollment)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <img src="{{ $enrollment->user->avatar_url ?? '' }}" alt=""
                                     class="w-8 h-8 rounded-full object-cover bg-gray-200 shrink-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $enrollment->user->full_name ?? '—' }}</p>
                                    <p class="text-xs text-gray-400">{{ $enrollment->user->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($enrollment->user->admission_number)
                            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded text-gray-700">
                                {{ $enrollment->user->admission_number }}
                            </span>
                            @else
                            <span class="text-xs text-gray-400 italic">Not assigned</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-gray-800 max-w-[160px] truncate">{{ $enrollment->course->title ?? '—' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs text-gray-500">
                                {{ $enrollment->batch->batch->name ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 min-w-[160px]">
                            @php
                                $ps = $enrollment->payment_status;
                                $plan = $enrollment->installmentPlan;
                                $isInst = $enrollment->payment_type === 'installment';
                                $psColor = match($ps) {
                                    'paid'     => 'bg-green-100 text-green-700',
                                    'partial'  => 'bg-blue-100 text-blue-700',
                                    'unpaid'   => 'bg-yellow-100 text-yellow-700',
                                    'refunded' => 'bg-gray-100 text-gray-600',
                                    default    => 'bg-gray-100 text-gray-500',
                                };
                            @endphp
                            @if($isInst && $plan)
                                {{-- Installment breakdown --}}
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $psColor }}">
                                            {{ $ps === 'paid' ? 'Fully Paid' : 'Installment' }}
                                        </span>
                                        @if($plan->status === 'overdue')
                                        <span class="px-1.5 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700">Overdue</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-700 font-medium">
                                        ₦{{ number_format($plan->amount_paid, 0) }}
                                        <span class="text-gray-400">/ ₦{{ number_format($plan->total_amount, 0) }}</span>
                                    </div>
                                    @if($plan->outstanding_balance > 0)
                                    <div class="text-xs text-red-600">₦{{ number_format($plan->outstanding_balance, 0) }} left</div>
                                    @endif
                                    @php
                                        $paidPct = $plan->total_amount > 0 ? round(($plan->amount_paid / $plan->total_amount) * 100) : 0;
                                    @endphp
                                    <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                                        <div class="h-1 rounded-full {{ $ps === 'paid' ? 'bg-green-500' : 'bg-blue-500' }}"
                                             style="width: {{ $paidPct }}%"></div>
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $plan->paid_installments ?? 0 }} / {{ $plan->installment_count }} installments
                                    </div>
                                </div>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $psColor }}">
                                    {{ ucfirst($ps) }}
                                </span>
                                @if($enrollment->amount_paid > 0)
                                <div class="text-xs text-gray-500 mt-0.5">₦{{ number_format($enrollment->amount_paid, 0) }}</div>
                                @endif
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($enrollment->access_locked)
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Locked</span>
                            @else
                            @php
                                $stColor = match($enrollment->status) {
                                    'active'    => 'bg-green-100 text-green-700',
                                    'completed' => 'bg-purple-100 text-purple-700',
                                    'pending'   => 'bg-yellow-100 text-yellow-700',
                                    'cancelled' => 'bg-gray-200 text-gray-600',
                                    default     => 'bg-gray-100 text-gray-500',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $stColor }}">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap">
                            {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('M d, Y') : '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5" x-data="{ open: false }" @click.away="open = false">
                                <a href="{{ route('admin.enrollments.show', $enrollment) }}"
                                   class="text-brand-600 hover:text-brand-800 text-xs font-medium">View</a>

                                <div class="relative">
                                    <button @click="open = !open"
                                            class="text-gray-400 hover:text-gray-600 px-1 py-0.5 rounded">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-transition
                                         class="absolute right-0 mt-1 w-44 bg-white border border-gray-200 rounded-xl shadow-lg z-20 overflow-hidden">

                                        @if($enrollment->status === 'pending' || $enrollment->access_locked)
                                        <form action="{{ route('admin.enrollments.approve', $enrollment) }}" method="POST">
                                            @csrf
                                            <button class="w-full text-left px-4 py-2.5 text-sm text-green-700 hover:bg-green-50 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Approve
                                            </button>
                                        </form>
                                        @endif

                                        @if(!$enrollment->access_locked && $enrollment->status !== 'cancelled')
                                        <form action="{{ route('admin.enrollments.suspend', $enrollment) }}" method="POST">
                                            @csrf
                                            <button class="w-full text-left px-4 py-2.5 text-sm text-orange-600 hover:bg-orange-50 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                Suspend
                                            </button>
                                        </form>
                                        @endif

                                        @if($enrollment->status !== 'cancelled')
                                        <form action="{{ route('admin.enrollments.reject', $enrollment) }}" method="POST"
                                              onsubmit="return confirm('Reject this enrollment?')">
                                            @csrf
                                            <button class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                Reject
                                            </button>
                                        </form>
                                        @endif

                                        <a href="{{ route('admin.enrollments.edit', $enrollment) }}"
                                           class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                            No enrollments found matching your filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($enrollments->hasPages())
        <div class="px-5 py-4 border-t">
            {{ $enrollments->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
