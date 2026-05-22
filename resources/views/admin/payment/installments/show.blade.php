@extends('layouts.admin')
@section('title', 'Installment Plan Details')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.installments.index') }}" class="text-brand-600 hover:underline text-sm">← Back to Plans</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    {{-- Student & Course Info --}}
    <div class="grid md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl border p-5">
            <h3 class="font-semibold text-gray-900 mb-3">Student Information</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Name</dt><dd class="font-medium">{{ $installmentPlan->enrollment->user->full_name }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Email</dt><dd>{{ $installmentPlan->enrollment->user->email }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Admission No.</dt><dd class="font-mono">{{ $installmentPlan->enrollment->user->admission_number ?? 'Not assigned' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Access</dt>
                    <dd>
                        @if($installmentPlan->enrollment->access_locked)
                            <span class="text-red-600 font-medium">Locked</span>
                        @else
                            <span class="text-green-600 font-medium">Active</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
        <div class="bg-white rounded-xl border p-5">
            <h3 class="font-semibold text-gray-900 mb-3">Payment Summary</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Total Amount</dt><dd class="font-bold">₦{{ number_format($installmentPlan->total_amount, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Down Payment</dt><dd>₦{{ number_format($installmentPlan->down_payment, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Amount Paid</dt><dd class="text-green-600 font-medium">₦{{ number_format($installmentPlan->amount_paid, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Outstanding</dt><dd class="text-red-600 font-bold">₦{{ number_format($installmentPlan->outstanding_balance, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Status</dt>
                    <dd><span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $installmentPlan->status === 'completed' ? 'bg-green-100 text-green-700' : ($installmentPlan->status === 'overdue' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">{{ ucfirst($installmentPlan->status) }}</span></dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Record Payment --}}
    @if($installmentPlan->outstanding_balance > 0)
    <div class="bg-white rounded-xl border p-5 mb-6">
        <h3 class="font-semibold text-gray-900 mb-3">Record Payment</h3>
        <form action="{{ route('admin.installments.payment', $installmentPlan) }}" method="POST" class="flex gap-3 items-end">
            @csrf
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₦)</label>
                <input type="number" name="amount" step="0.01" min="1" max="{{ $installmentPlan->outstanding_balance }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" required
                    placeholder="Enter amount paid">
            </div>
            <button type="submit" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-700">Record Payment</button>
        </form>
    </div>
    @endif

    {{-- Unlock Access --}}
    @if($installmentPlan->enrollment->access_locked)
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-center justify-between">
        <div>
            <p class="font-medium text-red-800 text-sm">Student's access is currently locked</p>
            <p class="text-red-600 text-xs mt-1">{{ $installmentPlan->enrollment->access_locked_reason }}</p>
        </div>
        <form action="{{ route('admin.installments.unlock', $installmentPlan) }}" method="POST">
            @csrf
            <button class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700">Unlock Access</button>
        </form>
    </div>
    @endif

    {{-- Schedule --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b">
            <h3 class="font-semibold text-gray-900">Payment Schedule</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">#</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Amount</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Due Date</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Paid</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($installmentPlan->schedule as $schedule)
                <tr class="{{ $schedule->status === 'overdue' ? 'bg-red-50' : '' }}">
                    <td class="px-4 py-3 text-gray-500">{{ $schedule->installment_number }}</td>
                    <td class="px-4 py-3 font-medium">₦{{ number_format($schedule->amount, 2) }}</td>
                    <td class="px-4 py-3">{{ $schedule->due_date->format('M d, Y') }}</td>
                    <td class="px-4 py-3 text-green-600">₦{{ number_format($schedule->amount_paid, 2) }}</td>
                    <td class="px-4 py-3">
                        @php $sc = ['paid'=>'green','pending'=>'gray','overdue'=>'red','partially_paid'=>'yellow','waived'=>'blue']; @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $sc[$schedule->status] ?? 'gray' }}-100 text-{{ $sc[$schedule->status] ?? 'gray' }}-700">{{ ucwords(str_replace('_',' ',$schedule->status)) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
