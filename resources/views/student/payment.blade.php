@extends('layouts.student')
@section('title', 'Payment')
@section('page_title', 'Payment')

@section('content')
<div class="max-w-lg mx-auto space-y-6">

    @if(session('success') || isset($success))
        <div class="bg-white rounded-2xl border border-green-200 p-8 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Payment Successful!</h2>
            <p class="text-gray-500 mt-2">{{ session('success') ?? 'Your enrollment is now active. Start learning right away!' }}</p>

            @if(isset($enrollment))
                <div class="mt-6 bg-gray-50 rounded-xl p-4 text-sm text-left space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Course</span>
                        <span class="font-medium text-gray-900">{{ $enrollment->course->title }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Reference</span>
                        <span class="font-mono text-xs text-brand-600">{{ $enrollment->payment->reference ?? '—' }}</span>
                    </div>
                </div>
            @endif

            <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('student.courses') }}"
                   class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-3 rounded-xl transition-colors">
                    Go to My Courses
                </a>
                <a href="{{ route('courses.index') }}"
                   class="border border-gray-200 text-gray-700 hover:bg-gray-50 font-medium px-6 py-3 rounded-xl transition-colors">
                    Browse More Courses
                </a>
            </div>
        </div>

    @elseif(session('error') || isset($failed))
        <div class="bg-white rounded-2xl border border-red-200 p-8 text-center">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Payment Failed</h2>
            <p class="text-gray-500 mt-2">{{ session('error') ?? 'We could not verify your payment. Please try again.' }}</p>
            <a href="{{ route('courses.index') }}" class="inline-block mt-6 bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-3 rounded-xl transition-colors">
                Back to Courses
            </a>
        </div>

    @else
        <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center">
            <div class="w-16 h-16 bg-brand-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-brand-400 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Processing Payment</h2>
            <p class="text-gray-500 mt-2">Please wait while we verify your payment...</p>
        </div>
    @endif

</div>
@endsection
