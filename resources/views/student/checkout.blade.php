@extends('layouts.student')
@section('title', 'Enroll: ' . $course->title)
@section('page_title', 'Course Enrollment')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        {{-- Course Header --}}
        <div class="bg-brand-950 text-white p-6 flex gap-4">
            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="w-24 h-16 object-cover rounded-xl shrink-0">
            <div>
                <p class="text-brand-300 text-sm">{{ $course->category->name }}</p>
                <h2 class="font-bold text-lg mt-0.5">{{ $course->title }}</h2>
                <p class="text-brand-200 text-sm">by {{ $course->instructor->user->full_name }}</p>
            </div>
        </div>

        <div class="p-6 space-y-5">
            {{-- Price Summary --}}
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-600">Course Price</span>
                    <span class="font-medium">₦{{ number_format($course->price) }}</span>
                </div>
                @if($course->discount_price && $course->discount_price < $course->price)
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-green-600">Discount</span>
                        <span class="text-green-600 font-medium">-₦{{ number_format($course->price - $course->discount_price) }}</span>
                    </div>
                @endif
                <div class="border-t border-gray-200 pt-2 mt-2 flex justify-between font-bold text-base">
                    <span>Total</span>
                    <span class="text-brand-700">₦{{ number_format($course->effective_price) }}</span>
                </div>
            </div>

            {{-- Course Includes --}}
            <div>
                <h3 class="font-semibold text-gray-900 mb-3">This enrollment includes:</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    @foreach([
                        ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => ($course->duration_hours ?? 0) . ' hours of content'],
                        ['icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138', 'text' => 'Certificate of Completion'],
                        ['icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Instructor support'],
                        ['icon' => 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z', 'text' => 'Lifetime access'],
                    ] as $item)
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                            {{ $item['text'] }}
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Payment Gateway --}}
            <form action="{{ route('enroll.payment.init', $course->slug) }}" method="POST">
                @csrf
                @if($course->is_free)
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl text-lg transition-colors">
                        Enroll for Free
                    </button>
                @else
                    <div class="space-y-3 mb-4">
                        <p class="text-sm font-medium text-gray-700">Select Payment Method:</p>
                        @foreach(['paystack' => 'Pay with Paystack (Card/Bank/USSD)', 'flutterwave' => 'Pay with Flutterwave'] as $gw => $label)
                            <label class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-brand-400 has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50">
                                <input type="radio" name="gateway" value="{{ $gw }}" {{ $gw === 'paystack' ? 'checked' : '' }} class="text-brand-600">
                                <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-4 rounded-xl text-lg transition-colors">
                        Pay ₦{{ number_format($course->effective_price) }} Now
                    </button>
                    <p class="text-xs text-gray-400 text-center mt-2">🔒 Secure payment. 30-day money-back guarantee.</p>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
