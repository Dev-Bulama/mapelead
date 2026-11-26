@extends('layouts.student')
@section('title', 'Enroll: ' . $course->title)
@section('page_title', 'Course Enrollment')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6" x-data="checkout()">

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 flex items-start gap-2">
        <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif
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
                    <div class="space-y-3">

                        {{-- Online --}}
                        <label class="cursor-pointer block">
                            <input type="radio" name="training_type" value="online" x-model="trainingType" class="sr-only">
                            <div :class="trainingType === 'online' ? 'border-brand-600 bg-brand-50' : 'border-gray-200 hover:border-brand-300'"
                                class="border-2 rounded-xl p-4 transition-all flex items-start gap-3">
                                <div class="mt-0.5 w-9 h-9 flex-shrink-0 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 text-sm">Online</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Self-paced learning from anywhere · Video lectures, assignments & live sessions</p>
                                </div>
                                <div class="text-right shrink-0">
                                    @if($course->is_free)
                                        <span class="text-sm font-bold text-green-600">Free</span>
                                    @else
                                        <span class="text-sm font-bold text-brand-700">₦{{ number_format($prices['online']) }}</span>
                                    @endif
                                </div>
                                <div :class="trainingType === 'online' ? 'border-brand-600 bg-brand-600' : 'border-gray-300'"
                                    class="w-4 h-4 rounded-full border-2 flex-shrink-0 mt-1 flex items-center justify-center">
                                    <div x-show="trainingType === 'online'" class="w-2 h-2 rounded-full bg-white"></div>
                                </div>
                            </div>
                        </label>

                        {{-- Physical: 1-month intensive --}}
                        <label class="cursor-pointer block">
                            <input type="radio" name="training_type" value="physical_monthly" x-model="trainingType" class="sr-only">
                            <div :class="trainingType === 'physical_monthly' ? 'border-brand-600 bg-brand-50' : 'border-gray-200 hover:border-brand-300'"
                                class="border-2 rounded-xl p-4 transition-all flex items-start gap-3">
                                <div class="mt-0.5 w-9 h-9 flex-shrink-0 rounded-lg bg-orange-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 text-sm">Physical — 1 Month Intensive</p>
                                    <p class="text-xs text-gray-500 mt-0.5">In-person daily sessions · <strong class="text-orange-600">3 sessions per day</strong> · 4-week immersive programme</p>
                                </div>
                                <div class="text-right shrink-0">
                                    @if($course->is_free)
                                        <span class="text-sm font-bold text-green-600">Free</span>
                                    @else
                                        <span class="text-sm font-bold text-brand-700">₦{{ number_format($prices['physical_monthly']) }}</span>
                                    @endif
                                </div>
                                <div :class="trainingType === 'physical_monthly' ? 'border-brand-600 bg-brand-600' : 'border-gray-300'"
                                    class="w-4 h-4 rounded-full border-2 flex-shrink-0 mt-1 flex items-center justify-center">
                                    <div x-show="trainingType === 'physical_monthly'" class="w-2 h-2 rounded-full bg-white"></div>
                                </div>
                            </div>
                        </label>

                        {{-- Physical: 3-month programme --}}
                        <label class="cursor-pointer block">
                            <input type="radio" name="training_type" value="physical_quarterly" x-model="trainingType" class="sr-only">
                            <div :class="trainingType === 'physical_quarterly' ? 'border-brand-600 bg-brand-50' : 'border-gray-200 hover:border-brand-300'"
                                class="border-2 rounded-xl p-4 transition-all flex items-start gap-3">
                                <div class="mt-0.5 w-9 h-9 flex-shrink-0 rounded-lg bg-green-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 text-sm">Physical — 3 Month Programme</p>
                                    <p class="text-xs text-gray-500 mt-0.5">In-person classroom sessions · Weekly schedule · 12-week comprehensive curriculum</p>
                                </div>
                                <div class="text-right shrink-0">
                                    @if($course->is_free)
                                        <span class="text-sm font-bold text-green-600">Free</span>
                                    @else
                                        <span class="text-sm font-bold text-brand-700">₦{{ number_format($prices['physical_quarterly']) }}</span>
                                    @endif
                                </div>
                                <div :class="trainingType === 'physical_quarterly' ? 'border-brand-600 bg-brand-600' : 'border-gray-300'"
                                    class="w-4 h-4 rounded-full border-2 flex-shrink-0 mt-1 flex items-center justify-center">
                                    <div x-show="trainingType === 'physical_quarterly'" class="w-2 h-2 rounded-full bg-white"></div>
                                </div>
                            </div>
                        </label>

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
                                class="border-2 rounded-xl p-4 transition-all hover:border-brand-300 h-full">
                                <p class="text-sm font-bold text-gray-900 mb-1">Full Payment</p>
                                <p class="text-xs text-gray-500 mb-2">Pay once, instant access</p>
                                <p class="text-xl font-bold text-brand-700">₦<span x-text="finalPrice.toLocaleString()"></span></p>
                                <p x-show="couponData" class="text-xs text-green-600 line-through">₦<span x-text="modePrice.toLocaleString()"></span></p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_type" value="installment" x-model="paymentType" class="sr-only">
                            <div :class="paymentType === 'installment' ? 'border-brand-600 bg-brand-50' : 'border-gray-200'"
                                class="border-2 rounded-xl p-4 transition-all hover:border-brand-300 h-full">
                                <p class="text-sm font-bold text-gray-900 mb-1">Installment</p>
                                <p class="text-xs text-gray-500 mb-2">Split across days/weeks</p>
                                <p class="text-xs font-semibold text-green-600">Flexible · max 30 days</p>
                            </div>
                        </label>
                    </div>

                    <div x-show="paymentType === 'installment'" x-transition class="space-y-4 pt-4 border-t">

                        {{-- Installment Plan Selector --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Choose a payment spread <span class="text-red-500">*</span></label>
                            <div class="space-y-2">
                                @foreach($installmentOptions as $idx => $opt)
                                <label class="cursor-pointer block">
                                    <input type="radio" name="installment_option_index" value="{{ $idx }}"
                                           @change="selectOption({{ $idx }})"
                                           {{ $idx === 0 ? 'checked' : '' }} class="sr-only">
                                    <div :class="selectedOptionIndex === {{ $idx }} ? 'border-brand-600 bg-brand-50' : 'border-gray-200 hover:border-gray-300'"
                                         class="border-2 rounded-xl px-4 py-3 transition-all flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $opt['label'] }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">
                                                {{ $opt['count'] }} payments · {{ $opt['period_days'] }} days apart ·
                                                {{ ($opt['count'] - 1) * $opt['period_days'] }} days total
                                            </p>
                                        </div>
                                        <div class="text-right shrink-0 ml-3">
                                            <p class="text-xs text-gray-400">≈ per payment</p>
                                            <p class="text-sm font-bold text-brand-700">
                                                ₦<span x-text="(selectedOptionIndex === {{ $idx }} && downPayment > 0)
                                                    ? Math.ceil((finalPrice - downPayment) / {{ $opt['count'] }}).toLocaleString()
                                                    : Math.ceil(finalPrice / {{ $opt['count'] }}).toLocaleString()">
                                                </span>
                                            </p>
                                            <p x-show="selectedOptionIndex === {{ $idx }} && downPayment > 0"
                                               class="text-xs text-green-600 font-medium">after down payment</p>
                                        </div>
                                        <div :class="selectedOptionIndex === {{ $idx }} ? 'border-brand-600 bg-brand-600' : 'border-gray-300'"
                                             class="w-4 h-4 rounded-full border-2 flex-shrink-0 ml-3 flex items-center justify-center">
                                            <div x-show="selectedOptionIndex === {{ $idx }}" class="w-2 h-2 rounded-full bg-white"></div>
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            {{-- Hidden fields submitted --}}
                            <input type="hidden" name="installment_count" :value="currentOption ? currentOption.count : 2">
                            <input type="hidden" name="period_days" :value="currentOption ? currentOption.period_days : 14">
                        </div>

                        {{-- Down Payment --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Down Payment (₦) <span class="text-red-500">*</span></label>
                            <input type="number" name="down_payment" x-model.number="downPayment"
                                step="500"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none"
                                placeholder="e.g. 20000">
                            <p class="text-xs text-gray-400 mt-1">
                                Remaining after down payment: <strong class="text-red-600">₦<span x-text="Math.max(0, finalPrice - downPayment).toLocaleString()"></span></strong>
                            </p>
                        </div>

                        {{-- First Due Date --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">First Installment Due Date <span class="text-red-500">*</span></label>
                            <input type="date" name="first_due_date"
                                min="{{ now()->addDay()->format('Y-m-d') }}"
                                max="{{ now()->addDays(30)->format('Y-m-d') }}"
                                value="{{ now()->addDays(7)->format('Y-m-d') }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                            <p class="text-xs text-gray-400 mt-1">Must be within 30 days from today</p>
                        </div>

                        {{-- Plan Summary --}}
                        <div class="bg-blue-50 rounded-xl p-4 text-xs text-blue-900">
                            <p class="font-bold mb-2">Your plan summary:</p>

                            {{-- Placeholder when no down payment entered yet --}}
                            <p x-show="downPayment <= 0" class="text-blue-600 italic">
                                Enter a down payment above to see your full breakdown.
                            </p>

                            {{-- Full breakdown when down payment entered --}}
                            <div x-show="downPayment > 0" class="space-y-1">
                                <p>Pay <strong>₦<span x-text="downPayment.toLocaleString()"></span></strong> today as down payment.</p>
                                <p x-show="currentOption">
                                    Then <strong x-text="currentOption ? currentOption.count : ''"></strong> payments of
                                    ≈ <strong>₦<span x-text="currentOption && downPayment < finalPrice ? Math.ceil((finalPrice - downPayment) / currentOption.count).toLocaleString() : '0'"></span></strong>
                                    each, spaced <strong x-text="currentOption ? currentOption.period_days : ''"></strong> days apart.
                                </p>
                                <p x-show="currentOption" class="text-blue-700 font-semibold">
                                    Full payment complete within <span x-text="currentOption ? ((currentOption.count - 1) * currentOption.period_days) + ' days' : ''"></span> of first due date.
                                </p>
                            </div>
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
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                             onerror="this.onerror=null;this.src='{{ \App\Models\Course::placeholderDataUri() }}'"
                             class="w-full h-24 object-cover rounded-lg mb-3 opacity-90">
                        <h3 class="font-bold text-sm">{{ $course->title }}</h3>
                        <p class="text-brand-200 text-xs mt-0.5">{{ $course->category->name ?? '' }}</p>
                    </div>
                    <div class="p-4">
                        @if(!$course->is_free)
                        {{-- Coupon Code --}}
                        <div class="mb-3 pb-3 border-b">
                            <button type="button" @click="couponOpen = !couponOpen"
                                class="text-xs text-brand-600 font-medium hover:underline flex items-center gap-1 w-full">
                                <svg x-show="!couponOpen" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <svg x-show="couponOpen" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                <span x-text="couponOpen ? 'Hide coupon' : 'Have a coupon code?'"></span>
                            </button>

                            <div x-show="couponOpen" x-transition class="mt-2 space-y-2">
                                <div class="space-y-1.5">
                                    <input type="text" x-model="couponInput"
                                        @keydown.enter.prevent="applyCoupon()"
                                        placeholder="Enter coupon code"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm uppercase focus:ring-2 focus:ring-brand-500 focus:outline-none tracking-widest">
                                    <button type="button" @click="applyCoupon()"
                                        :disabled="couponLoading || !couponInput.trim()"
                                        class="w-full bg-brand-600 text-white py-2.5 rounded-lg text-sm font-semibold hover:bg-brand-700 disabled:opacity-50 transition-colors">
                                        <span x-show="!couponLoading">Apply Coupon</span>
                                        <span x-show="couponLoading">Checking…</span>
                                    </button>
                                </div>
                                <p x-show="couponError" x-text="couponError" class="text-xs text-red-600"></p>
                                <div x-show="couponData" class="bg-green-50 border border-green-200 rounded-lg px-3 py-2 flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-bold text-green-700" x-text="couponData?.label"></p>
                                        <p class="text-xs text-green-600" x-text="couponData?.message"></p>
                                    </div>
                                    <button type="button" @click="removeCoupon()" class="text-green-500 hover:text-red-500 ml-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Hidden field submitted with form --}}
                            <input type="hidden" name="coupon_code" :value="couponData ? couponData.code : ''">
                        </div>

                        <div class="mb-3 pb-3 border-b">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span x-text="trainingLabel"></span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-700" x-show="couponData">
                                <span>Subtotal</span>
                                <span>₦<span x-text="modePrice.toLocaleString()"></span></span>
                            </div>
                            <div class="flex justify-between text-sm text-green-600 font-medium" x-show="couponData">
                                <span>Discount (<span x-text="couponData?.label"></span>)</span>
                                <span>−₦<span x-text="(couponData?.discount ?? 0).toLocaleString()"></span></span>
                            </div>
                            <div class="flex justify-between font-bold text-gray-900 mt-1">
                                <span>Total</span>
                                <span class="text-brand-700">₦<span x-text="finalPrice.toLocaleString()"></span></span>
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
                        <button type="submit"
                                :disabled="paymentType === 'installment' && downPayment <= 0"
                                :class="paymentType === 'installment' && downPayment <= 0
                                    ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                    : 'bg-brand-600 hover:bg-brand-700 text-white'"
                                class="w-full font-bold py-3 rounded-xl text-sm transition-colors">
                            <span x-show="paymentType === 'full'">Pay ₦<span x-text="finalPrice.toLocaleString()"></span></span>
                            <span x-show="paymentType === 'installment' && downPayment <= 0">Enter a down payment to continue</span>
                            <span x-show="paymentType === 'installment' && downPayment > 0">Pay ₦<span x-text="downPayment.toLocaleString()"></span> Now</span>
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
    const prices  = @json($prices);
    const options = @json($installmentOptions);
    const couponUrl = '{{ route('enroll.coupon.check') }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    return {
        trainingType: '{{ old('training_type', 'online') }}',
        paymentType:  '{{ old('payment_type', 'full') }}',
        selectedBatch: null,
        downPayment: 0,
        selectedOptionIndex: 0,

        // Coupon state
        couponOpen:    false,
        couponInput:   '',
        couponLoading: false,
        couponError:   '',
        couponData:    null,

        get currentOption() {
            return options[this.selectedOptionIndex] ?? null;
        },

        get modePrice() {
            return prices[this.trainingType] ?? prices['online'];
        },

        get finalPrice() {
            return Math.max(0, this.modePrice - (this.couponData?.discount ?? 0));
        },

        get trainingLabel() {
            const labels = {
                online: 'Online',
                physical_monthly: 'Physical · 1 Month',
                physical_quarterly: 'Physical · 3 Months',
            };
            return labels[this.trainingType] ?? 'Online';
        },

        selectOption(index) {
            this.selectedOptionIndex = index;
        },

        async applyCoupon() {
            const code = this.couponInput.trim();
            if (!code) return;
            this.couponLoading = true;
            this.couponError   = '';
            this.couponData    = null;
            try {
                const res  = await fetch(couponUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ code, amount: this.modePrice }),
                });
                const data = await res.json();
                if (data.valid) {
                    this.couponData  = data;
                    this.couponError = '';
                } else {
                    this.couponError = data.message;
                }
            } catch {
                this.couponError = 'Could not validate coupon. Please try again.';
            } finally {
                this.couponLoading = false;
            }
        },

        removeCoupon() {
            this.couponData  = null;
            this.couponInput = '';
            this.couponError = '';
        },
    };
}
</script>
@endpush
@endsection
