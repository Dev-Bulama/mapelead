@extends('layouts.admin')
@section('title', 'Payment — ' . $payment->reference)

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Payment Details</h1>
            <p class="text-sm text-gray-500 mt-0.5 font-mono">{{ $payment->reference }}</p>
        </div>
        <a href="{{ route('admin.payments.index') }}"
           class="text-sm text-gray-600 bg-white border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
            &larr; Back to Payments
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm p-4">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main Details --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Payment Info --}}
            <div class="bg-white rounded-xl border p-5">
                <h2 class="text-sm font-semibold text-gray-900 mb-4">Payment Information</h2>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">Reference</dt>
                        <dd class="font-mono text-gray-900">{{ $payment->reference }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">Gateway</dt>
                        <dd class="text-gray-900 capitalize">{{ $payment->gateway ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">Amount</dt>
                        <dd class="text-xl font-bold text-gray-900">&#8358;{{ number_format($payment->amount, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">Currency</dt>
                        <dd class="text-gray-900">{{ $payment->currency ?? 'NGN' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">Status</dt>
                        <dd>
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
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">Paid At</dt>
                        <dd class="text-gray-900">{{ $payment->paid_at ? $payment->paid_at->format('d M Y, H:i') : '—' }}</dd>
                    </div>
                    @if($payment->notes)
                    <div class="col-span-2">
                        <dt class="text-xs text-gray-500 mb-0.5">Notes</dt>
                        <dd class="text-gray-700">{{ $payment->notes }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Enrollment / Course --}}
            @if($payment->enrollment)
            <div class="bg-white rounded-xl border p-5">
                <h2 class="text-sm font-semibold text-gray-900 mb-4">Enrollment &amp; Course</h2>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">Course</dt>
                        <dd class="text-gray-900 font-medium">{{ $payment->enrollment->course?->title ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">Enrollment Status</dt>
                        <dd class="text-gray-900 capitalize">{{ $payment->enrollment->status }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">Payment Status</dt>
                        <dd class="text-gray-900 capitalize">{{ $payment->enrollment->payment_status ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">Enrolled At</dt>
                        <dd class="text-gray-900">{{ $payment->enrollment->enrolled_at?->format('d M Y') ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">

            {{-- Student --}}
            <div class="bg-white rounded-xl border p-5">
                <h2 class="text-sm font-semibold text-gray-900 mb-4">Student</h2>
                @if($payment->user)
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold shrink-0"
                         style="background-color:#14215B">
                        {{ strtoupper(substr($payment->user->first_name, 0, 1)) }}{{ strtoupper(substr($payment->user->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $payment->user->first_name }} {{ $payment->user->last_name }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->user->email }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.users.show', $payment->user) }}"
                   class="text-xs text-brand-600 hover:underline" style="color:#14215B">
                    View Student Profile &rarr;
                </a>
                @else
                <p class="text-sm text-gray-400">No student linked.</p>
                @endif
            </div>

            {{-- Refund Form --}}
            @if($payment->status === 'success')
            <div class="bg-white rounded-xl border p-5">
                <h2 class="text-sm font-semibold text-gray-900 mb-4">Issue Refund</h2>
                <form action="{{ route('admin.payments.refund', $payment->id) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to refund this payment?')">
                    @csrf
                    <div class="mb-3">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Reason <span class="text-red-500">*</span></label>
                        <textarea name="reason" rows="3" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
                            placeholder="Reason for refund...">{{ old('reason') }}</textarea>
                        @error('reason')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                            class="w-full px-4 py-2 rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition">
                        Process Refund
                    </button>
                </form>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection
