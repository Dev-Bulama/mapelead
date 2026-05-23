@extends('layouts.student')
@section('title', 'Enroll: ' . $course->title)
@section('page_title', 'Course Enrollment')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6" x-data="checkout()">

    @if($errors->any())
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
        <ul class="space-y-1">@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form action="{{ route('enroll.payment.init', $course->slug) }}" method="POST">
        @csrf

        <div class="grid lg:grid-cols-3 gap-6">

            {{-- Main Form --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Step 1: Training Mode --}}
                <div class="bg-white rounded-2xl border p-5">
                    <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <span class="w-6 h-6 bg-brand-600 text-white rounded-full text-xs font-bold flex items-center justify-center">1</span>
                        Training Mode
                    </h3>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach(['online' => ['Online', 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'], 'physical' => ['Physical', 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'], 'hybrid' => ['Hybrid', 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9']] as $type => [$label, $icon])
                        <label class="cursor-pointer">
                            <input type="radio" name="training_type" value="{{ $type }}" x-model="trainingType" class="sr-only">
                            <div :class="trainingType === '{{ $type }}' ? 'border-brand-600 bg-brand-50 text-brand-700' : 'border-gray-200 text-gray-600'"
                                class="border-2 rounded-xl p-3 text-center transition-all hover:border-brand-300">
                                <svg class="w-5 h-5 mx-auto mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                                </svg>
                                <span class="text-xs font-semibold block">{{ $label }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Step 2: Batch Selection --}}
                @if($batches->isNotEmpty())
                <div class="bg-white rounded-2xl border p-5">
                    <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <span class="w-6 h-6 bg-brand-600 text-white rounded-full text-xs font-bold flex items-center justify-center">2</span>
                        Select Batch / Cohort
                    </h3>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-gray-200 transition-all">
                            <input type="radio" name="batch_id" value="" class="text-brand-600" checked>
                            <span class="text-sm text-gray-500">No specific batch preference</span>
                        </label>
                        @foreach($batches as $batch)
                        <label class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition-all"
                            :class="selectedBatch == {{ $batch->id }} ? 'border-brand-600 bg-brand-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="batch_id" value="{{ $batch->id }}" x-model="selectedBatch" class="text-brand-600">
                            <div class="flex-1">
                                <span class="text-sm font-semibold text-gray-900">{{ $batch->name }}</span>
                                <div class="flex gap-3 mt-0.5">
                                    <span class="text-xs text-gray-400">Starts {{ \Carbon\Carbon::parse($batch->start_date)->format('M d, Y') }}</span>
                                    @if($batch->max_students)
                                        <span class="text-xs text-gray-400">{{ max(0, $batch->max_students - $batch->current_students) }} seats left</span>
                                    @endif
                                </div>
                            </div>
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">Open</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @else
                    <input type="hidden" name="batch_id" value="">
                @endif

                {{-- Step 3: Payment Plan --}}
                @if(!$course->is_free)
                <div class="bg-white rounded-2xl border p-5">
                    <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <span class="w-6 h-6 bg-brand-600 text-white rounded-full text-xs font-bold flex items-center justify-center">{{ $batches->isNotEmpty() ? '3' : '2' }}</span>
                        Payment Plan
                    </h3>
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_type" value="full" x-model="paymentType" class="sr-only">
                            <div :class="paymentType === 'full' ? 'border-brand-600 bg-brand-50' : 'border-gray-200'"
                                class="border-2 rounded-xl p-4 transition-all hover:border-brand-300">
                                <p class="text-sm font-bold text-gray-900 mb-1">Full Payment</p>
                                <p class="text-xs text-gray-500 mb-2">Pay once, instant access</p>
                                <p class="text-xl font-bold text-brand-700">₦{{ number_format($course->effective_price) }}</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_type" value="installment" x-model="paymentType" class="sr-only">
                            <div :class="paymentType === 'installment' ? 'border-brand-600 bg-brand-50' : 'border-gray-200'"
                                class="border-2 rounded-xl p-4 transition-all hover:border-brand-300">
                                <p class="text-sm font-bold text-gray-900 mb-1">Installment</p>
                                <p class="text-xs text-gray-500 mb-2">Pay in 2–12 payments</p>
                                <p class="text-xs font-semibold text-green-600">Flexible payment plan</p>
                            </div>
                        </label>
                    </div>

                    <div x-show="paymentType === 'installment'" x-transition class="space-y-3 pt-4 border-t">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Down Payment (₦) <span class="text-red-500">*</span></label>
                                <input type="number" name="down_payment" x-model.number="downPayment"
                                    min="1000" step="500"
                                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500"
                                    placeholder="e.g. 20000">
                                <p class="text-xs text-gray-400 mt-1">
                                    Remaining: <strong class="text-red-600">₦<span x-text="Math.max(0, {{ $course->effective_price }} - downPayment).toLocaleString()"></span></strong>
                                </p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Number of Installments <span class="text-red-500">*</span></label>
                                <select name="installment_count" x-model.number="installmentCount" class="w-full border rounded-lg px-3 py-2 text-sm">
                                    @for($i = 2; $i <= 12; $i++)
                                        <option value="{{ $i }}">{{ $i }} monthly payments</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">First Installment Due Date <span class="text-red-500">*</span></label>
                            <input type="date" name="first_due_date"
                                min="{{ now()->addDays(7)->format('Y-m-d') }}"
                                value="{{ now()->addMonth()->format('Y-m-d') }}"
                                class="w-full border rounded-lg px-3 py-2 text-sm">
                        </div>
                        <div class="bg-blue-50 rounded-xl p-3 text-xs text-blue-800">
                            <p class="font-semibold mb-1">Your plan summary:</p>
                            <p>Pay <strong>₦<span x-text="downPayment.toLocaleString()"></span></strong> today, then
                                <strong x-text="installmentCount"></strong> monthly payments of
                                ≈ <strong>₦<span x-text="installmentCount > 0 && downPayment < {{ $course->effective_price }} ? Math.ceil(({{ $course->effective_price }} - downPayment) / installmentCount).toLocaleString() : '0'"></span></strong> each</p>
                        </div>
                    </div>
                </div>
                @else
                    <input type="hidden" name="payment_type" value="full">
                @endif

            </div>

            {{-- Order Summary Sidebar --}}
            <div>
                <div class="bg-white rounded-2xl border overflow-hidden sticky top-6">
                    <div class="bg-brand-600 p-4 text-white">
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="w-full h-24 object-cover rounded-lg mb-3 opacity-90">
                        <h3 class="font-bold text-sm">{{ $course->title }}</h3>
                        <p class="text-brand-200 text-xs mt-0.5">{{ $course->category->name ?? '' }}</p>
                    </div>
                    <div class="p-4">
                        @if(!$course->is_free)
                        <div class="mb-3 pb-3 border-b">
                            @if($course->discount_price && $course->discount_price < $course->price)
                            <div class="flex justify-between text-xs text-gray-400 mb-1">
                                <span>Original</span><span class="line-through">₦{{ number_format($course->price) }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between font-bold text-gray-900">
                                <span>Total</span><span class="text-brand-700">₦{{ number_format($course->effective_price) }}</span>
                            </div>
                            <div x-show="paymentType === 'installment' && downPayment > 0" class="mt-2 pt-2 border-t">
                                <div class="flex justify-between text-sm font-bold text-green-700">
                                    <span>Due today</span>
                                    <span>₦<span x-text="downPayment.toLocaleString()"></span></span>
                                </div>
                            </div>
                        </div>
                        @endif

                        <ul class="text-xs text-gray-600 space-y-1.5 mb-4">
                            <li class="flex items-center gap-2"><svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Lifetime course access</li>
                            <li class="flex items-center gap-2"><svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Certificate on completion</li>
                            <li class="flex items-center gap-2"><svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Instructor support</li>
                        </ul>

                        @if($course->is_free)
                        <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 rounded-xl text-sm hover:bg-green-700 transition-colors">Enroll Free</button>
                        @else
                        <button type="submit" class="w-full bg-brand-600 text-white font-bold py-3 rounded-xl text-sm hover:bg-brand-700 transition-colors">
                            <span x-show="paymentType === 'full'">Pay ₦{{ number_format($course->effective_price) }}</span>
                            <span x-show="paymentType === 'installment'">Pay ₦<span x-text="downPayment.toLocaleString()"></span> Now</span>
                        </button>
                        @endif
                        <p class="text-center text-xs text-gray-400 mt-2">🔒 Secure · Paystack</p>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

@push('scripts')
<script>
function checkout() {
    return {
        trainingType: '{{ old('training_type', 'online') }}',
        paymentType: '{{ old('payment_type', 'full') }}',
        selectedBatch: null,
        downPayment: 0,
        installmentCount: 3,
    }
}
</script>
@endpush
@endsection
