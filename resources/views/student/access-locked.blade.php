@extends('layouts.student')
@section('title', 'Access Suspended')
@section('page_title', 'Access Suspended')

@section('content')
<div class="flex items-start justify-center min-h-[60vh] pt-10">
    <div class="w-full max-w-lg">

        {{-- Main Card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            {{-- Top accent --}}
            <div class="h-1.5 bg-gradient-to-r from-red-500 to-orange-400"></div>

            <div class="p-8 text-center">

                {{-- Padlock Icon --}}
                <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-5"
                     style="background: linear-gradient(135deg, #fee2e2 0%, #fed7aa 100%);">
                    <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>

                {{-- Heading --}}
                <h1 class="text-2xl font-bold text-gray-900 mb-3">Course Access Suspended</h1>

                {{-- Reason --}}
                <p class="text-gray-500 text-sm leading-relaxed max-w-sm mx-auto">
                    {{ $enrollment->access_locked_reason ?? 'Your access has been suspended due to an outstanding payment.' }}
                </p>

            </div>

            {{-- Info Box --}}
            @if($enrollment)
            <div class="mx-6 mb-6 bg-red-50 border border-red-100 rounded-xl p-4 text-left">
                <h3 class="text-xs font-bold text-red-700 uppercase tracking-wide mb-3">Suspension Details</h3>

                <div class="space-y-2.5">

                    {{-- Course Name --}}
                    <div class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-red-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/>
                        </svg>
                        <div>
                            <p class="text-xs text-red-500 font-semibold uppercase tracking-wide">Course</p>
                            <p class="text-sm text-gray-800 font-medium">{{ $enrollment->course->title ?? '—' }}</p>
                        </div>
                    </div>

                    {{-- Outstanding Balance --}}
                    @if($enrollment->isInstallment() && $enrollment->getOutstandingBalance() > 0)
                    <div class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-red-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-red-500 font-semibold uppercase tracking-wide">Outstanding Balance</p>
                            <p class="text-sm font-bold text-red-700">
                                &#8358;{{ number_format($enrollment->getOutstandingBalance(), 2) }}
                            </p>
                        </div>
                    </div>
                    @endif

                    {{-- Next Payment Date --}}
                    @if($enrollment->isInstallment() && $enrollment->installmentPlan && $enrollment->installmentPlan->nextDue())
                    @php $nextDue = $enrollment->installmentPlan->nextDue(); @endphp
                    <div class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-red-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-red-500 font-semibold uppercase tracking-wide">Next Payment Due</p>
                            <p class="text-sm font-medium text-gray-800">
                                {{ $nextDue->due_date ? \Carbon\Carbon::parse($nextDue->due_date)->format('F j, Y') : '—' }}
                                &mdash; &#8358;{{ number_format($nextDue->amount, 2) }}
                            </p>
                        </div>
                    </div>
                    @endif

                    {{-- Locked At --}}
                    @if($enrollment->access_locked_at)
                    <div class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-red-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-red-500 font-semibold uppercase tracking-wide">Suspended On</p>
                            <p class="text-sm text-gray-700">
                                {{ $enrollment->access_locked_at->format('F j, Y \a\t g:i A') }}
                            </p>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
            @endif

            {{-- CTA Buttons --}}
            <div class="px-6 pb-6 space-y-3">

                {{-- Primary: Make Payment --}}
                <a href="{{ route('student.payments') }}"
                   class="flex items-center justify-center gap-2 w-full text-white text-sm font-bold px-5 py-3.5 rounded-xl transition-all hover:opacity-90 active:scale-95"
                   style="background-color: #14215B;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Make Payment
                </a>

                {{-- Secondary: Contact Support --}}
                <a href="{{ route('student.tickets') }}"
                   class="flex items-center justify-center gap-2 w-full text-sm font-semibold px-5 py-3.5 rounded-xl border-2 transition-all hover:bg-gray-50"
                   style="border-color: #14215B; color: #14215B;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Contact Support
                </a>

            </div>

            {{-- Disclaimer note --}}
            <div class="px-6 pb-6">
                <div class="bg-gray-50 rounded-xl px-4 py-3 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        If you believe this is an error, please contact our support team with your
                        enrollment details and we will resolve it promptly.
                    </p>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
