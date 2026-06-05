@extends('layouts.admin')
@section('title', 'Installment Plan')

@section('content')
<div class="p-6 max-w-5xl mx-auto space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.installments.index') }}" class="hover:text-brand-600">Installment Plans</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">{{ $installmentPlan->enrollment->user->full_name ?? 'Plan #'.$installmentPlan->id }}</span>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    {{-- Top Overview Cards --}}
    @php
        $paidCount  = $installmentPlan->schedule->where('status', 'paid')->count();
        $totalCount = $installmentPlan->schedule->count();
        $paidPct    = $installmentPlan->total_amount > 0
            ? round(($installmentPlan->amount_paid / $installmentPlan->total_amount) * 100)
            : 0;
        $nextDue = $installmentPlan->schedule->whereIn('status', ['pending', 'overdue'])->sortBy('due_date')->first();
    @endphp

    <div class="grid md:grid-cols-2 gap-5">

        {{-- Student card --}}
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Student</h3>
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-brand-600 flex items-center justify-center text-white font-bold text-sm shrink-0">
                    {{ strtoupper(substr($installmentPlan->enrollment->user->first_name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-900">{{ $installmentPlan->enrollment->user->full_name ?? '—' }}</p>
                    <p class="text-sm text-gray-500">{{ $installmentPlan->enrollment->user->email ?? '—' }}</p>
                    @if($installmentPlan->enrollment->user->admission_number ?? null)
                        <span class="inline-block mt-1 font-mono text-xs bg-brand-50 text-brand-700 border border-brand-200 px-2 py-0.5 rounded">
                            {{ $installmentPlan->enrollment->user->admission_number }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="mt-4 pt-4 border-t text-sm">
                <div class="flex justify-between py-1">
                    <span class="text-gray-500">Course</span>
                    <span class="font-medium text-gray-800 text-right max-w-[60%] truncate">{{ $installmentPlan->enrollment->course->title ?? '—' }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-gray-500">Access</span>
                    <span>
                        @if($installmentPlan->enrollment->access_locked)
                            <span class="text-red-600 font-semibold">Locked</span>
                        @else
                            <span class="text-green-600 font-semibold">Active</span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-gray-500">Enrolled</span>
                    <span class="text-gray-700">{{ $installmentPlan->enrollment->enrolled_at?->format('M d, Y') ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- Payment summary card --}}
        <div class="bg-white rounded-xl border p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide">Payment Summary</h3>
                <span class="px-3 py-1 rounded-full text-xs font-semibold
                    {{ $installmentPlan->status === 'completed' ? 'bg-green-100 text-green-700'
                    : ($installmentPlan->status === 'overdue' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                    {{ ucfirst($installmentPlan->status) }}
                </span>
            </div>

            {{-- Progress bar --}}
            <div class="mb-4">
                <div class="flex justify-between text-xs text-gray-600 mb-1">
                    <span>{{ $paidCount }} of {{ $totalCount }} installments paid</span>
                    <span class="font-semibold">{{ $paidPct }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $installmentPlan->status === 'overdue' ? 'bg-red-500' : ($installmentPlan->status === 'completed' ? 'bg-green-500' : 'bg-blue-500') }}"
                         style="width: {{ $paidPct }}%"></div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3 text-center">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-400 mb-0.5">Total</p>
                    <p class="font-bold text-gray-900 text-sm">₦{{ number_format($installmentPlan->total_amount, 0) }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-3">
                    <p class="text-xs text-gray-400 mb-0.5">Paid</p>
                    <p class="font-bold text-green-700 text-sm">₦{{ number_format($installmentPlan->amount_paid, 0) }}</p>
                </div>
                <div class="bg-red-50 rounded-lg p-3">
                    <p class="text-xs text-gray-400 mb-0.5">Remaining</p>
                    <p class="font-bold text-red-600 text-sm">₦{{ number_format($installmentPlan->outstanding_balance, 0) }}</p>
                </div>
            </div>

            @if($nextDue)
            <div class="mt-3 p-3 rounded-lg {{ $nextDue->status === 'overdue' ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200' }}">
                <p class="text-xs {{ $nextDue->status === 'overdue' ? 'text-red-700 font-semibold' : 'text-yellow-700 font-semibold' }}">
                    {{ $nextDue->status === 'overdue' ? 'Overdue:' : 'Next due:' }}
                    ₦{{ number_format($nextDue->amount, 0) }}
                    — {{ $nextDue->due_date->format('M d, Y') }}
                    @if($nextDue->status === 'overdue')
                        ({{ $nextDue->due_date->diffForHumans() }})
                    @else
                        (in {{ $nextDue->due_date->diffForHumans() }})
                    @endif
                </p>
            </div>
            @endif
        </div>
    </div>

    {{-- Access lock alert --}}
    @if($installmentPlan->enrollment->access_locked)
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="font-semibold text-red-800 text-sm">Student's course access is locked</p>
            <p class="text-red-600 text-xs mt-1">{{ $installmentPlan->enrollment->access_locked_reason }}</p>
            <p class="text-red-500 text-xs mt-0.5">
                Locked {{ $installmentPlan->enrollment->access_locked_at?->format('M d, Y g:i A') }}
            </p>
        </div>
        <form action="{{ route('admin.installments.unlock', $installmentPlan) }}" method="POST">
            @csrf
            <button class="bg-green-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-green-700 transition-colors">
                Restore Access
            </button>
        </form>
    </div>
    @endif

    {{-- Record Payment --}}
    @if($installmentPlan->outstanding_balance > 0)
    <div class="bg-white rounded-xl border p-5">
        <h3 class="font-semibold text-gray-900 mb-1">Record Installment Payment</h3>
        <p class="text-xs text-gray-400 mb-4">Recording a payment here creates a ledger entry and updates the student's schedule.</p>
        <form action="{{ route('admin.installments.payment', $installmentPlan) }}" method="POST">
            @csrf
            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Amount (₦) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" step="0.01" min="1"
                           value="{{ $nextDue ? number_format($nextDue->amount, 2, '.', '') : '' }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" required
                           placeholder="0.00">
                    @if($nextDue)
                    <p class="text-xs text-gray-400 mt-1">Next installment: ₦{{ number_format($nextDue->amount, 0) }}</p>
                    @endif
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Payment Channel</label>
                    <select name="gateway" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                        <option value="manual">Manual / Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="paystack">Paystack</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Notes (optional)</label>
                    <input type="text" name="notes" maxlength="255"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500"
                           placeholder="e.g. Paid via bank transfer">
                </div>
            </div>
            <div class="mt-4 flex gap-3">
                <button type="submit"
                        class="bg-brand-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-brand-700 transition-colors">
                    Record Payment
                </button>
                @if($nextDue)
                <button type="button"
                        onclick="document.querySelector('[name=amount]').value='{{ number_format($nextDue->amount, 2, '.', '') }}'"
                        class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                    Fill Exact Amount
                </button>
                @endif
            </div>
        </form>
    </div>
    @endif

    {{-- Installment Schedule Table --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Payment Schedule</h3>
            <span class="text-xs text-gray-400">{{ $paidCount }} / {{ $totalCount }} paid</span>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-600 font-semibold">#</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-semibold">Due Date</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-semibold">Amount</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-semibold">Paid</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-semibold">Paid On</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($installmentPlan->schedule->sortBy('installment_number') as $item)
                @php
                    $sc = [
                        'paid'          => ['bg-green-100 text-green-700', 'Paid'],
                        'pending'       => ['bg-gray-100 text-gray-600',   'Pending'],
                        'overdue'       => ['bg-red-100 text-red-700',     'Overdue'],
                        'partially_paid'=> ['bg-yellow-100 text-yellow-700','Part Paid'],
                        'waived'        => ['bg-blue-100 text-blue-700',   'Waived'],
                    ];
                    [$scColor, $scLabel] = $sc[$item->status] ?? ['bg-gray-100 text-gray-500', ucfirst($item->status)];
                @endphp
                <tr class="{{ $item->status === 'overdue' ? 'bg-red-50/60' : '' }}">
                    <td class="px-4 py-3 text-gray-500 font-medium">{{ $item->installment_number }}</td>
                    <td class="px-4 py-3">
                        <span class="{{ $item->status === 'overdue' ? 'text-red-700 font-semibold' : 'text-gray-700' }}">
                            {{ $item->due_date->format('M d, Y') }}
                        </span>
                        @if($item->status === 'overdue')
                            <span class="text-xs text-red-500 block">{{ $item->due_date->diffForHumans() }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-semibold text-gray-900">₦{{ number_format($item->amount, 2) }}</td>
                    <td class="px-4 py-3 text-green-700">
                        {{ $item->amount_paid > 0 ? '₦' . number_format($item->amount_paid, 2) : '—' }}
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs">
                        {{ $item->paid_at ? $item->paid_at->format('M d, Y') : '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $scColor }}">{{ $scLabel }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Payment Transaction Log --}}
    @if($payments->isNotEmpty())
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b">
            <h3 class="font-semibold text-gray-900">Transaction History</h3>
            <p class="text-xs text-gray-400 mt-0.5">All payment records linked to this enrollment</p>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-600 font-semibold">Reference</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-semibold">Amount</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-semibold">Channel</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-semibold">Notes</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-semibold">Status</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-semibold">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($payments as $payment)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $payment->reference }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-900">₦{{ number_format($payment->amount, 2) }}</td>
                    <td class="px-4 py-3 text-gray-500 capitalize">{{ $payment->gateway ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $payment->notes ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @php $pColor = $payment->status === 'success' ? 'bg-green-100 text-green-700' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'); @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $pColor }}">{{ ucfirst($payment->status) }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $payment->created_at->format('M d, Y g:i A') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection
