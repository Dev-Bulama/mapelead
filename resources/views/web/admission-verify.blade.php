@extends('layouts.app')
@section('title', 'Admission Verification')

@section('content')
<div class="min-h-screen bg-gray-50 py-16">
    <div class="max-w-2xl mx-auto px-4">

        @if(!empty($notFound) && $notFound)
        {{-- ── Not Found ── --}}
        <div class="bg-white rounded-2xl shadow-lg p-10 text-center">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Invalid Admission Number</h1>
            <p class="text-gray-500 mb-3">
                No student record was found for admission number:
            </p>
            <p class="inline-block bg-red-50 border border-red-200 text-red-700 font-mono font-semibold text-sm px-4 py-2 rounded-lg mb-6">
                {{ $number }}
            </p>
            <p class="text-gray-400 text-sm mb-8">
                Please double-check the admission number and try again. If you believe this is an error, contact the institution directly.
            </p>
            <a href="{{ route('home') }}"
               class="bg-brand-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-brand-700 transition-colors">
                Go Home
            </a>
        </div>

        @else
        {{-- ── Found ── --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

            {{-- Header: green verified banner --}}
            <div class="bg-green-600 p-8 text-center text-white">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-white text-opacity-80 text-sm font-medium uppercase tracking-widest mb-1">
                    Admission Verified
                </p>
                <h1 class="text-2xl font-bold">This admission record is authentic</h1>
            </div>

            {{-- Student details --}}
            <div class="p-8">

                {{-- Avatar + Name row --}}
                <div class="flex items-center gap-4 mb-8 pb-8 border-b">
                    <img src="{{ $user->avatar_url }}"
                         alt="{{ $user->full_name }}"
                         class="w-16 h-16 rounded-full object-cover border-2 border-green-200">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Student Name</p>
                        <h2 class="text-xl font-bold text-gray-900">{{ $user->full_name }}</h2>
                        <p class="text-sm font-mono font-semibold text-green-700 mt-0.5 tracking-widest">
                            {{ $user->admission_number }}
                        </p>
                    </div>
                </div>

                {{-- Details grid --}}
                <div class="grid grid-cols-2 gap-6 mb-8">
                    @if($enrollment)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Course</p>
                        <p class="font-semibold text-gray-900">{{ $enrollment->course?->title ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Training Type</p>
                        <p class="font-medium text-gray-900 capitalize">{{ $enrollment->training_type ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Enrollment Date</p>
                        <p class="font-medium text-gray-900">
                            {{ $enrollment->enrolled_at?->format('F d, Y') ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Status</p>
                        @php
                            $status = $enrollment->status ?? 'unknown';
                            $badgeClass = match($status) {
                                'active'    => 'bg-green-100 text-green-800',
                                'completed' => 'bg-blue-100 text-blue-800',
                                'suspended' => 'bg-yellow-100 text-yellow-800',
                                default     => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize {{ $badgeClass }}">
                            {{ ucfirst($status) }}
                        </span>
                    </div>
                    @else
                    <div class="col-span-2">
                        <p class="text-sm text-gray-400 italic">No active enrollment found for this student.</p>
                    </div>
                    @endif
                </div>

                {{-- Verified notice --}}
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-green-800">Verified Student</p>
                        <p class="text-xs text-green-700 mt-0.5">
                            This admission record was issued by
                            {{ \App\Models\SiteSetting::get('site_name', 'Mapelead') }}
                            and is authentic. Verified on {{ now()->format('M d, Y') }}.
                        </p>
                    </div>
                </div>
            </div>

            <div class="px-8 pb-8 text-center">
                <p class="text-xs text-gray-400">Verification URL: {{ request()->url() }}</p>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
