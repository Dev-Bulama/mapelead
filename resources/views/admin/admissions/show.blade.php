@extends('layouts.admin')
@section('title', 'Enrollment #' . $enrollment->id)

@section('content')
<div class="space-y-6 max-w-5xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.enrollments.index') }}" class="hover:text-brand-600">Admissions</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Enrollment #{{ $enrollment->id }}</span>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm">{{ session('error') }}</div>
    @endif

    {{-- Status Bar --}}
    <div class="bg-white rounded-xl border p-5 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <img src="{{ $enrollment->user->avatar_url }}" alt="" class="w-14 h-14 rounded-full object-cover bg-gray-200">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $enrollment->user->full_name }}</h2>
                <p class="text-sm text-gray-500">{{ $enrollment->user->email }}</p>
                @if($enrollment->user->admission_number)
                <span class="inline-block mt-1 font-mono text-xs bg-brand-50 text-brand-700 border border-brand-200 px-2 py-0.5 rounded">
                    {{ $enrollment->user->admission_number }}
                </span>
                @endif
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            @if($enrollment->access_locked)
            <span class="px-3 py-1.5 bg-red-100 text-red-700 rounded-full text-sm font-semibold">Access Locked</span>
            <form action="{{ route('admin.enrollments.unlock', $enrollment) }}" method="POST">
                @csrf
                <button class="px-4 py-1.5 bg-green-600 text-white rounded-full text-sm font-semibold hover:bg-green-700 transition-colors">
                    Restore Access
                </button>
            </form>
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
            <span class="px-3 py-1.5 rounded-full text-sm font-semibold {{ $stColor }}">{{ ucfirst($enrollment->status) }}</span>
            @endif

            <a href="{{ route('admin.enrollments.edit', $enrollment) }}"
               class="px-4 py-1.5 border border-gray-300 text-gray-700 rounded-full text-sm font-medium hover:bg-gray-50 transition-colors">
                Edit
            </a>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Left: Main Details --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Course & Enrollment Info --}}
            <div class="bg-white rounded-xl border p-5">
                <h3 class="font-semibold text-gray-900 mb-4 pb-2 border-b">Enrollment Details</h3>
                <div class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Course</p>
                        <p class="text-gray-900 font-medium">{{ $enrollment->course->title ?? '—' }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $enrollment->course->category->name ?? '' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Instructor</p>
                        <p class="text-gray-900">{{ $enrollment->course->instructor?->user?->full_name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Enrolled On</p>
                        <p class="text-gray-900">{{ $enrollment->enrolled_at?->format('F j, Y \a\t g:i A') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Progress</p>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                                <div class="bg-brand-600 h-1.5 rounded-full" style="width: {{ $enrollment->progress_percent }}%"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-700">{{ $enrollment->progress_percent }}%</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Batch</p>
                        <p class="text-gray-900">{{ $enrollment->batch->batch->name ?? 'No batch assigned' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Payment Type</p>
                        <p class="text-gray-900">{{ ucfirst($enrollment->payment_type ?? 'full') }}</p>
                    </div>
                </div>
            </div>

            {{-- Payment Info --}}
            <div class="bg-white rounded-xl border p-5">
                <h3 class="font-semibold text-gray-900 mb-4 pb-2 border-b">Payment Information</h3>
                <div class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Amount Paid</p>
                        <p class="text-gray-900 font-bold text-lg">₦{{ number_format($enrollment->amount_paid, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Payment Status</p>
                        @php
                            $ps = $enrollment->payment_status;
                            $psColor = match($ps) {
                                'paid'     => 'bg-green-100 text-green-700',
                                'partial'  => 'bg-blue-100 text-blue-700',
                                'unpaid'   => 'bg-yellow-100 text-yellow-700',
                                'refunded' => 'bg-gray-100 text-gray-600',
                                default    => 'bg-gray-100 text-gray-500',
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $psColor }}">{{ ucfirst($ps) }}</span>
                    </div>

                    @if($enrollment->isInstallment() && $enrollment->installmentPlan)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Outstanding Balance</p>
                        <p class="text-red-600 font-bold">₦{{ number_format($enrollment->getOutstandingBalance(), 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Installments</p>
                        @php $paid = $enrollment->installmentPlan->schedule->where('status','paid')->count(); $total = $enrollment->installmentPlan->schedule->count(); @endphp
                        <p class="text-gray-900">{{ $paid }} / {{ $total }} paid</p>
                    </div>
                    @endif
                </div>

                @if($enrollment->payments->isNotEmpty())
                <div class="mt-4 pt-4 border-t">
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-3">Payment History</p>
                    <div class="space-y-2">
                        @foreach($enrollment->payments->take(5) as $payment)
                        <div class="flex items-center justify-between text-xs py-1.5 border-b border-gray-50 last:border-0">
                            <div>
                                <span class="font-mono text-gray-600">{{ $payment->reference }}</span>
                                <span class="ml-2 text-gray-400">{{ $payment->created_at?->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-gray-800">₦{{ number_format($payment->amount, 2) }}</span>
                                @php $pColor = $payment->status === 'success' ? 'bg-green-100 text-green-700' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-600'); @endphp
                                <span class="px-1.5 py-0.5 rounded {{ $pColor }}">{{ ucfirst($payment->status) }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Installment Schedule --}}
            @if($enrollment->isInstallment() && $enrollment->installmentPlan && $enrollment->installmentPlan->schedule->isNotEmpty())
            <div class="bg-white rounded-xl border p-5">
                <h3 class="font-semibold text-gray-900 mb-4 pb-2 border-b">Installment Schedule</h3>
                <div class="space-y-2">
                    @foreach($enrollment->installmentPlan->schedule as $item)
                    <div class="flex items-center justify-between text-sm py-2 border-b border-gray-50 last:border-0">
                        <div>
                            <span class="font-medium text-gray-800">
                                {{ $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('M d, Y') : '—' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-gray-800">₦{{ number_format($item->amount, 2) }}</span>
                            @php $sColor = match($item->status ?? 'pending') {
                                'paid'    => 'bg-green-100 text-green-700',
                                'overdue' => 'bg-red-100 text-red-700',
                                default   => 'bg-yellow-100 text-yellow-700',
                            }; @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $sColor }}">
                                {{ ucfirst($item->status ?? 'pending') }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- Right Sidebar --}}
        <div class="space-y-5">

            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl border p-5">
                <h3 class="font-semibold text-gray-900 mb-4 text-sm">Quick Actions</h3>
                <div class="space-y-2">

                    @if($enrollment->status === 'pending' || $enrollment->access_locked)
                    <form action="{{ route('admin.enrollments.approve', $enrollment) }}" method="POST">
                        @csrf
                        <button class="w-full flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Approve Access
                        </button>
                    </form>
                    @endif

                    @if(!$enrollment->access_locked)
                    <div x-data="{ show: false }">
                        <button @click="show = !show"
                                class="w-full flex items-center gap-2 px-4 py-2.5 border border-orange-300 text-orange-700 rounded-lg text-sm font-medium hover:bg-orange-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Suspend Access
                        </button>
                        <div x-show="show" x-transition class="mt-2">
                            <form action="{{ route('admin.enrollments.suspend', $enrollment) }}" method="POST" class="space-y-2">
                                @csrf
                                <textarea name="reason" rows="2" placeholder="Reason for suspension (optional)"
                                          class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400"></textarea>
                                <button type="submit" class="w-full bg-orange-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-orange-700">
                                    Confirm Suspend
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                    <div x-data="{ show: false }">
                        <button @click="show = !show"
                                class="w-full flex items-center gap-2 px-4 py-2.5 border border-red-300 text-red-700 rounded-lg text-sm font-medium hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reject Enrollment
                        </button>
                        <div x-show="show" x-transition class="mt-2">
                            <form action="{{ route('admin.enrollments.reject', $enrollment) }}" method="POST" class="space-y-2">
                                @csrf
                                <textarea name="reason" rows="2" placeholder="Reason for rejection (optional)"
                                          class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-400"></textarea>
                                <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-red-700">
                                    Confirm Reject
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Batch Reassignment --}}
            <div class="bg-white rounded-xl border p-5">
                <h3 class="font-semibold text-gray-900 mb-1 text-sm">Reassign Batch</h3>
                <p class="text-xs text-gray-400 mb-4">Current: <strong>{{ $enrollment->batch->batch->name ?? 'None' }}</strong></p>
                <form action="{{ route('admin.enrollments.reassign-batch', $enrollment) }}" method="POST" class="space-y-3">
                    @csrf
                    <select name="batch_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                        <option value="">No batch</option>
                        @foreach($availableBatches as $batch)
                        <option value="{{ $batch->id }}" {{ $enrollment->batch?->batch_id == $batch->id ? 'selected' : '' }}>
                            {{ $batch->name }}
                            @if($batch->max_students) ({{ $batch->max_students - $batch->current_students }} seats) @endif
                        </option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full bg-brand-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-brand-700 transition-colors">
                        Reassign
                    </button>
                </form>
            </div>

            {{-- Access Lock Info --}}
            @if($enrollment->access_locked)
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <h3 class="text-xs font-bold text-red-700 uppercase tracking-wide mb-2">Suspension Details</h3>
                <p class="text-xs text-red-600 mb-1">
                    <strong>Since:</strong> {{ $enrollment->access_locked_at?->format('M d, Y g:i A') ?? 'Unknown' }}
                </p>
                <p class="text-xs text-red-600">
                    <strong>Reason:</strong> {{ $enrollment->access_locked_reason ?? 'Not specified' }}
                </p>
            </div>
            @endif

            {{-- Certificate --}}
            @if($enrollment->certificate)
            <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                <h3 class="text-xs font-bold text-purple-700 uppercase tracking-wide mb-2">Certificate Issued</h3>
                <p class="font-mono text-xs text-purple-800">{{ $enrollment->certificate->certificate_number }}</p>
                <p class="text-xs text-purple-600 mt-1">{{ $enrollment->certificate->issued_at?->format('M d, Y') }}</p>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
