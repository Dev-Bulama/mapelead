@extends('layouts.student')
@section('title', 'My Admission')
@section('page_title', 'My Admission')

@section('content')
<div class="space-y-6">

    {{-- No Admission Number Warning --}}
    @if(! $user->admission_number)
        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl px-5 py-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-yellow-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <p class="text-sm text-yellow-800 font-medium">Admission number not yet assigned. Contact admin.</p>
        </div>
    @endif

    {{-- Flash error --}}
    @if(session('error'))
        <div x-data="{show:true}" x-show="show" x-cloak x-init="setTimeout(()=>show=false,5000)"
             class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm flex items-center justify-between">
            {{ session('error') }}
            <button @click="show=false" class="text-red-400 hover:text-red-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        {{-- LEFT COLUMN                                                         --}}
        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ── ADMISSION CARD ── --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">

                {{-- Card Header --}}
                <div class="px-6 py-4 flex items-center justify-between" style="background-color: #14215B;">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/>
                            </svg>
                        </div>
                        <span class="text-white font-bold tracking-wider text-sm uppercase">Official Admission</span>
                    </div>
                    <span class="text-white/60 text-xs">{{ \App\Models\SiteSetting::get('site_name', 'Mapelead Academy') }}</span>
                </div>

                {{-- Card Body --}}
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">

                        {{-- Student Photo --}}
                        <div class="shrink-0">
                            <img src="{{ $user->avatar_url }}"
                                 alt="{{ $user->full_name }}"
                                 class="w-20 h-20 rounded-full object-cover ring-4"
                                 style="ring-color: #14215B; outline: 4px solid #14215B20;">
                        </div>

                        {{-- Student Info --}}
                        <div class="flex-1 min-w-0">
                            <h2 class="text-2xl font-bold text-gray-900">{{ $user->full_name }}</h2>

                            @if($user->admission_number)
                                <div class="mt-2 inline-flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                    </svg>
                                    <span class="font-mono font-bold text-xl tracking-widest" style="color: #14215B;">
                                        {{ $user->admission_number }}
                                    </span>
                                </div>
                            @else
                                <div class="mt-2 inline-flex items-center gap-2 bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-2">
                                    <span class="font-mono text-sm text-yellow-700">Pending Assignment</span>
                                </div>
                            @endif

                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                {{-- Status badge --}}
                                @if($enrollment)
                                    @if(in_array($enrollment->status, ['active', 'completed']))
                                        <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            Admitted
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 bg-yellow-100 text-yellow-700 text-xs font-semibold px-3 py-1 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>
                                            Pending
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-600 text-xs font-semibold px-3 py-1 rounded-full">
                                        No Enrollment
                                    </span>
                                @endif

                                <span class="text-xs text-gray-400">{{ $user->email }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── COURSE DETAILS CARD ── --}}
            @if($enrollment)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/>
                    </svg>
                    <h3 class="font-semibold text-gray-900 text-sm">Course Details</h3>
                </div>

                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Course Name --}}
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Course</p>
                        <p class="font-bold text-gray-900 text-lg leading-tight">{{ $enrollment->course->title }}</p>
                        @if($enrollment->course->category)
                            <p class="text-xs font-medium mt-0.5" style="color: #14215B;">{{ $enrollment->course->category->name }}</p>
                        @endif
                    </div>

                    {{-- Training Type --}}
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Training Type</p>
                        <div class="flex items-center gap-2">
                            @php $type = $enrollment->course->type ?? 'online'; @endphp
                            @if($type === 'online')
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Online</span>
                            @elseif($type === 'physical' || $type === 'in-person')
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Physical / In-Person</span>
                            @else
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">{{ ucfirst($type) }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Batch / Cohort --}}
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Batch / Cohort</p>
                        @if($enrollment->batch && $enrollment->batch->batch)
                            <p class="text-sm font-semibold text-gray-700">{{ $enrollment->batch->batch->name }}</p>
                        @else
                            <p class="text-sm text-gray-400">—</p>
                        @endif
                    </div>

                    {{-- Enrollment Date --}}
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Enrolled On</p>
                        <p class="text-sm font-medium text-gray-700">
                            {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('F j, Y') : '—' }}
                        </p>
                    </div>

                    {{-- Duration --}}
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Training Duration</p>
                        @if($enrollment->course->duration_weeks)
                            <p class="text-sm font-medium text-gray-700">{{ $enrollment->course->duration_weeks }} Weeks</p>
                        @elseif($enrollment->course->duration_hours)
                            <p class="text-sm font-medium text-gray-700">{{ $enrollment->course->duration_hours }} Hours</p>
                        @else
                            <p class="text-sm text-gray-400">—</p>
                        @endif
                    </div>

                    {{-- Payment Status --}}
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Payment Status</p>
                        @php $ps = $enrollment->payment_status ?? 'pending'; @endphp
                        @if($ps === 'paid')
                            <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Paid
                            </span>
                        @elseif($ps === 'installment')
                            <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                Installment
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                {{ ucfirst($ps) }}
                            </span>
                        @endif
                    </div>

                    {{-- Progress --}}
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Course Progress</p>
                        <div class="flex items-center gap-3">
                            <div class="flex-1 bg-gray-100 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all" style="width: {{ $enrollment->progress_percent ?? 0 }}%; background-color: #14215B;"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-700">{{ $enrollment->progress_percent ?? 0 }}%</span>
                        </div>
                    </div>

                </div>
            </div>
            @endif

        </div>{{-- /LEFT COLUMN --}}

        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        {{-- RIGHT COLUMN                                                        --}}
        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        <div class="space-y-5">

            {{-- ── QUICK ACTIONS ── --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 text-sm">Quick Actions</h3>
                </div>
                <div class="p-4 space-y-3">

                    {{-- Download Admission Slip --}}
                    <a href="{{ route('student.admission.download') }}"
                       class="flex items-center gap-3 w-full text-white text-sm font-semibold px-4 py-3 rounded-xl transition-all hover:opacity-90 active:scale-95"
                       style="background-color: #14215B;">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download Admission Slip
                    </a>

                    {{-- Verify Admission --}}
                    @if($user->admission_number)
                        <a href="{{ route('admission.verify.public', $user->admission_number) }}"
                           target="_blank" rel="noopener"
                           class="flex items-center gap-3 w-full text-sm font-semibold px-4 py-3 rounded-xl border-2 transition-all hover:bg-gray-50"
                           style="border-color: #14215B; color: #14215B;">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Verify My Admission
                            <svg class="w-3 h-3 ml-auto opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    @endif

                    {{-- View Certificates --}}
                    <a href="{{ route('student.certificates') }}"
                       class="flex items-center gap-3 w-full text-sm font-medium text-gray-600 px-4 py-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                        View My Certificates
                    </a>

                </div>
            </div>

            {{-- ── ADMISSION CHECKLIST ── --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 text-sm">Admission Checklist</h3>
                </div>
                <div class="p-4 space-y-3">

                    @php
                        $hasAdmissionNumber   = (bool) $user->admission_number;
                        $isPaid               = $enrollment && $enrollment->payment_status === 'paid';
                        $hasActiveAccess      = $enrollment && ! $enrollment->access_locked && $enrollment->status === 'active';
                        $isCertEligible       = $enrollment && $enrollment->status === 'completed' && ($enrollment->progress_percent ?? 0) >= 100;
                    @endphp

                    {{-- 1. Registration Complete --}}
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 bg-green-100">
                            <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-700 font-medium">Registration Complete</span>
                    </div>

                    {{-- 2. Admission Number Assigned --}}
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 {{ $hasAdmissionNumber ? 'bg-green-100' : 'bg-gray-100' }}">
                            @if($hasAdmissionNumber)
                                <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </div>
                        <span class="text-sm font-medium {{ $hasAdmissionNumber ? 'text-gray-700' : 'text-gray-400' }}">
                            Admission Number Assigned
                        </span>
                        @if(! $hasAdmissionNumber)
                            <span class="text-xs text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-full ml-auto">Pending</span>
                        @endif
                    </div>

                    {{-- 3. Payment Complete --}}
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 {{ $isPaid ? 'bg-green-100' : 'bg-gray-100' }}">
                            @if($isPaid)
                                <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            @endif
                        </div>
                        <span class="text-sm font-medium {{ $isPaid ? 'text-gray-700' : 'text-gray-400' }}">Payment Complete</span>
                        @if(! $isPaid)
                            <span class="text-xs text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-full ml-auto">Pending</span>
                        @endif
                    </div>

                    {{-- 4. Course Access Active --}}
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 {{ $hasActiveAccess ? 'bg-green-100' : 'bg-gray-100' }}">
                            @if($hasActiveAccess)
                                <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            @endif
                        </div>
                        <span class="text-sm font-medium {{ $hasActiveAccess ? 'text-gray-700' : 'text-gray-400' }}">Course Access Active</span>
                        @if(! $hasActiveAccess)
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full ml-auto">Inactive</span>
                        @endif
                    </div>

                    {{-- 5. Certificate Eligible --}}
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 {{ $isCertEligible ? 'bg-green-100' : 'bg-gray-100' }}">
                            @if($isCertEligible)
                                <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138"/>
                                </svg>
                            @endif
                        </div>
                        <span class="text-sm font-medium {{ $isCertEligible ? 'text-gray-700' : 'text-gray-400' }}">Certificate Eligible</span>
                        @if(! $isCertEligible)
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full ml-auto">Not yet</span>
                        @endif
                    </div>

                </div>
            </div>

            {{-- ── ALL ENROLLMENTS ── --}}
            @if($enrollments->count() > 1)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 text-sm">All Enrollments</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($enrollments as $enr)
                        <div class="px-5 py-3 flex items-center gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $enr->course->title ?? '—' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $enr->enrolled_at ? $enr->enrolled_at->format('M j, Y') : '—' }}
                                </p>
                            </div>
                            @php $st = $enr->status; @endphp
                            <span class="shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full
                                {{ $st === 'active'    ? 'bg-green-100 text-green-700'  :
                                   ($st === 'completed' ? 'bg-blue-100 text-blue-700'    :
                                   ($st === 'suspended' ? 'bg-red-100 text-red-700'      :
                                    'bg-gray-100 text-gray-600')) }}">
                                {{ ucfirst($st) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>{{-- /RIGHT COLUMN --}}

    </div>{{-- /grid --}}
</div>
@endsection
