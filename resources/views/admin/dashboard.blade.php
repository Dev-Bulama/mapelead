@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- ═══════════════════════════════════════════════════════════════
         PAGE HEADER
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                Welcome back, {{ auth()->user()->first_name }}. Here's what's happening today.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-400 bg-white border border-gray-200 px-3 py-1.5 rounded-lg">
                {{ now()->format('l, F j, Y') }}
            </span>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         SECTION 1 — STATS CARDS
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">

        {{-- Total Users --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm col-span-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ number_format($stats['total_users']) }}
                    </p>
                    <p class="text-xs text-green-600 mt-1 font-medium">
                        +{{ $stats['new_users_today'] }} today
                    </p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Revenue --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm col-span-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        ₦{{ number_format($stats['total_revenue'], 0) }}
                    </p>
                    <p class="text-xs text-green-600 mt-1 font-medium">All time</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Enrollments --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm col-span-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Enrollments</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ number_format($stats['total_enrollments']) }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1 font-medium">Total enrolled</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Active Courses --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm col-span-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Active Courses</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ number_format($stats['total_courses']) }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1 font-medium">Published</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Pending Leads --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm col-span-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pending Leads</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ number_format($stats['pending_leads']) }}
                    </p>
                    <p class="text-xs text-orange-500 mt-1 font-medium">Needs action</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Revenue Today --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm col-span-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Revenue Today</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        ₦{{ number_format($stats['revenue_today'], 0) }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1 font-medium">{{ now()->format('M j') }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         SECTION 2 — REVENUE CHART (last 30 days)
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Revenue — Last 30 Days</h3>
                <p class="text-xs text-gray-400 mt-0.5">Daily revenue trend from successful payments</p>
            </div>
            <a href="{{ route('admin.analytics.revenue') }}"
               class="text-xs text-brand-600 hover:text-brand-700 font-medium">
                Full report →
            </a>
        </div>
        <div class="px-5 pt-4 pb-2">
            @php
                $maxRevenue = $revenueChart->max('total') ?: 1;
            @endphp
            @if($revenueChart->count() > 0)
                <div class="flex items-end gap-1 h-40 overflow-x-auto pb-2">
                    @foreach($revenueChart as $point)
                        @php
                            $heightPct = round(($point->total / $maxRevenue) * 100);
                            $heightPx  = max(4, round(($point->total / $maxRevenue) * 140));
                            $date      = \Carbon\Carbon::parse($point->date);
                        @endphp
                        <div class="group flex flex-col items-center flex-1 min-w-[20px] relative"
                             title="₦{{ number_format($point->total) }} on {{ $date->format('M j') }}">
                            <div class="relative w-full">
                                {{-- Tooltip --}}
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 hidden group-hover:block
                                            bg-gray-900 text-white text-xs rounded px-2 py-1 whitespace-nowrap z-10 pointer-events-none">
                                    {{ $date->format('M j') }}: ₦{{ number_format($point->total, 0) }}
                                </div>
                                {{-- Bar --}}
                                <div class="w-full rounded-t bg-brand-500 hover:bg-brand-600 transition-colors cursor-default"
                                     style="height: {{ $heightPx }}px; min-height: 4px;"></div>
                            </div>
                            <span class="text-gray-400 text-[9px] mt-1 whitespace-nowrap hidden md:block">
                                {{ $date->format('d') }}
                            </span>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center justify-between mt-1 text-xs text-gray-400 px-0.5">
                    @if($revenueChart->count() >= 2)
                        <span>{{ \Carbon\Carbon::parse($revenueChart->first()->date)->format('M j') }}</span>
                        <span>{{ \Carbon\Carbon::parse($revenueChart->last()->date)->format('M j') }}</span>
                    @endif
                </div>
            @else
                <div class="h-40 flex flex-col items-center justify-center text-gray-300">
                    <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <p class="text-sm">No revenue data in the last 30 days</p>
                </div>
            @endif
        </div>

        {{-- Enrollment mini-chart beneath --}}
        @if($enrollmentChart->count() > 0)
            <div class="px-5 py-3 border-t border-gray-50">
                <p class="text-xs text-gray-400 font-medium mb-2">Enrollments (same period)</p>
                @php $maxEnrol = $enrollmentChart->max('total') ?: 1; @endphp
                <div class="flex items-end gap-1 h-10 overflow-x-auto">
                    @foreach($enrollmentChart as $point)
                        @php
                            $ePx = max(2, round(($point->total / $maxEnrol) * 36));
                            $eDate = \Carbon\Carbon::parse($point->date);
                        @endphp
                        <div class="group flex flex-col items-center flex-1 min-w-[20px]"
                             title="{{ $eDate->format('M j') }}: {{ $point->total }} enrollments">
                            <div class="w-full rounded-t bg-purple-300 hover:bg-purple-400 transition-colors cursor-default"
                                 style="height: {{ $ePx }}px; min-height: 2px;"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         SECTION 3 — TOP COURSES + RECENT ENROLLMENTS (2-col)
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Top 5 Courses --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800">Top 5 Courses by Enrollments</h3>
                <a href="{{ route('admin.courses.index') }}"
                   class="text-xs text-brand-600 hover:text-brand-700 font-medium">View all →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="admin-table w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium">Course</th>
                            <th class="px-4 py-3 text-left font-medium">Type</th>
                            <th class="px-4 py-3 text-right font-medium">Students</th>
                            <th class="px-4 py-3 text-right font-medium">Rating</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($topCourses as $i => $course)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-bold text-gray-400 w-4 shrink-0">{{ $i + 1 }}</span>
                                        @if($course->thumbnail)
                                            <img src="{{ asset('storage/'.$course->thumbnail) }}"
                                                 class="w-8 h-8 rounded object-cover shrink-0" alt="">
                                        @else
                                            <div class="w-8 h-8 rounded bg-brand-100 flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-800 truncate max-w-[160px]">{{ $course->title }}</p>
                                            <p class="text-xs text-gray-400 truncate">{{ $course->instructor->user->first_name ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        {{ $course->type === 'online' ? 'bg-blue-100 text-blue-700' : ($course->type === 'physical' ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700') }}">
                                        {{ ucfirst($course->type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800">
                                    {{ number_format($course->enrollments_count) }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="text-yellow-500 font-medium text-xs">
                                        ★ {{ number_format($course->average_rating, 1) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-gray-400 text-sm">
                                    No courses yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Enrollments --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800">Recent Enrollments</h3>
                <a href="{{ route('admin.enrollments.index') }}"
                   class="text-xs text-brand-600 hover:text-brand-700 font-medium">View all →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="admin-table w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium">Student</th>
                            <th class="px-4 py-3 text-left font-medium">Course</th>
                            <th class="px-4 py-3 text-left font-medium">Status</th>
                            <th class="px-4 py-3 text-right font-medium">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentEnrollments as $enrollment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-brand-100 flex items-center justify-center shrink-0">
                                            <span class="text-xs font-bold text-brand-700">
                                                {{ strtoupper(substr($enrollment->user->first_name ?? 'U', 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-800 truncate max-w-[120px]">
                                                {{ $enrollment->user->first_name ?? 'Unknown' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-gray-600 truncate max-w-[120px] text-xs">
                                        {{ $enrollment->course->title ?? 'N/A' }}
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $sBadge = match($enrollment->status) {
                                            'active'    => 'bg-green-100 text-green-700',
                                            'completed' => 'bg-blue-100 text-blue-700',
                                            'pending'   => 'bg-yellow-100 text-yellow-700',
                                            'cancelled' => 'bg-red-100 text-red-700',
                                            default     => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $sBadge }}">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-xs text-gray-400 whitespace-nowrap">
                                    {{ $enrollment->created_at->format('M j, Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-gray-400 text-sm">
                                    No enrollments yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         SECTION 4 — RECENT PAYMENTS
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Recent Payments</h3>
            <a href="{{ route('admin.payments.index') }}"
               class="text-xs text-brand-600 hover:text-brand-700 font-medium">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="admin-table w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium">Reference</th>
                        <th class="px-5 py-3 text-left font-medium">User</th>
                        <th class="px-5 py-3 text-left font-medium">Course</th>
                        <th class="px-5 py-3 text-right font-medium">Amount</th>
                        <th class="px-5 py-3 text-center font-medium">Status</th>
                        <th class="px-5 py-3 text-right font-medium">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentPayments as $payment)
                        <tr class="hover:bg-gray-50">
                            {{-- Reference --}}
                            <td class="px-5 py-3">
                                <a href="{{ route('admin.payments.show', $payment->id) }}"
                                   class="font-mono text-xs text-brand-600 hover:text-brand-700 hover:underline">
                                    {{ $payment->reference }}
                                </a>
                            </td>
                            {{-- User --}}
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center shrink-0">
                                        <span class="text-xs font-bold text-gray-600">
                                            {{ strtoupper(substr($payment->user->first_name ?? 'U', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-800 truncate max-w-[120px]">
                                            {{ $payment->user->first_name ?? '' }} {{ $payment->user->last_name ?? 'Unknown' }}
                                        </p>
                                        <p class="text-xs text-gray-400 truncate max-w-[120px]">
                                            {{ $payment->user->email ?? '' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            {{-- Course --}}
                            <td class="px-5 py-3">
                                <p class="text-gray-600 truncate max-w-[160px] text-xs">
                                    {{ $payment->enrollment->course->title ?? '—' }}
                                </p>
                            </td>
                            {{-- Amount --}}
                            <td class="px-5 py-3 text-right font-semibold text-gray-800 whitespace-nowrap">
                                ₦{{ number_format($payment->amount, 2) }}
                            </td>
                            {{-- Status badge --}}
                            <td class="px-5 py-3 text-center">
                                @php
                                    $pBadge = match($payment->status) {
                                        'success'  => 'bg-green-100 text-green-700 ring-1 ring-green-200',
                                        'pending'  => 'bg-yellow-100 text-yellow-700 ring-1 ring-yellow-200',
                                        'failed'   => 'bg-red-100 text-red-700 ring-1 ring-red-200',
                                        'refunded' => 'bg-blue-100 text-blue-700 ring-1 ring-blue-200',
                                        default    => 'bg-gray-100 text-gray-600',
                                    };
                                    $pDot = match($payment->status) {
                                        'success'  => 'bg-green-500',
                                        'pending'  => 'bg-yellow-500',
                                        'failed'   => 'bg-red-500',
                                        'refunded' => 'bg-blue-500',
                                        default    => 'bg-gray-400',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pBadge }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $pDot }}"></span>
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            {{-- Date --}}
                            <td class="px-5 py-3 text-right text-xs text-gray-400 whitespace-nowrap">
                                {{ $payment->paid_at ? $payment->paid_at->format('M j, Y H:i') : $payment->created_at->format('M j, Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-gray-400 text-sm">
                                No payments recorded yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
