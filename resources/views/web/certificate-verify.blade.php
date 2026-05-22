@extends('layouts.app')
@section('title', 'Certificate Verification')

@section('content')
<div class="min-h-screen bg-gray-50 py-16">
    <div class="max-w-2xl mx-auto px-4">

        @if(!empty($notFound) && $notFound)
        {{-- Not Found --}}
        <div class="bg-white rounded-2xl shadow-lg p-10 text-center">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Certificate Not Found</h1>
            <p class="text-gray-500 mb-6">The certificate verification token is invalid or the certificate has been revoked.</p>
            <a href="{{ route('home') }}" class="bg-brand-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-brand-700">Go Home</a>
        </div>
        @else
        {{-- Certificate Found --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            {{-- Header --}}
            <div class="bg-brand-600 p-8 text-center text-white">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <p class="text-white text-opacity-80 text-sm font-medium uppercase tracking-widest mb-1">Certificate Verified</p>
                <h1 class="text-2xl font-bold">This certificate is authentic</h1>
            </div>

            {{-- Details --}}
            <div class="p-8">
                <div class="flex items-center gap-4 mb-8 pb-8 border-b">
                    <img src="{{ $certificate->user->avatar_url }}" alt="{{ $certificate->user->full_name }}" class="w-16 h-16 rounded-full object-cover">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Awarded to</p>
                        <h2 class="text-xl font-bold text-gray-900">{{ $certificate->user->full_name }}</h2>
                        @if($certificate->user->admission_number)
                        <p class="text-sm text-gray-500 font-mono">{{ $certificate->user->admission_number }}</p>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Course</p>
                        <p class="font-semibold text-gray-900">{{ $certificate->course->title }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Certificate Number</p>
                        <p class="font-mono font-semibold text-brand-600">{{ $certificate->certificate_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Issue Date</p>
                        <p class="font-medium text-gray-900">{{ $certificate->issued_at?->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Attendance</p>
                        <p class="font-medium text-gray-900">{{ $certificate->attendance_percentage ?? 'N/A' }}%</p>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-green-800">Verified Certificate</p>
                        <p class="text-xs text-green-700 mt-0.5">This certificate was issued by {{ \App\Models\SiteSetting::get('site_name', 'Mapelead') }} and is authentic. Verified on {{ now()->format('M d, Y') }}.</p>
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
