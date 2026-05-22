@extends('layouts.admin')

@section('title', 'Revenue Analytics')

@section('content')
@php
    $totalTransactions = $revenue->sum('count');
    $avgTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;
    $maxRevenue = $revenue->max('total') ?: 1;
    $totalCourseRevenue = $topCourses->sum('revenue') ?: 1;
@endphp

<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Revenue Analytics</h1>
            <p class="text-sm text-gray-500 mt-1">Financial performance and earnings breakdown.</p>
        </div>
        <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl w-fit">
            @foreach([7 => '7d', 14 => '14d', 30 => '30d', 90 => '90d'] as $days => $label)
            <a href="{{ request()->fullUrlWithQuery(['period' => $days]) }}"
                class="{{ $period == $days ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }} px-4 py-1.5 rounded-lg text-sm font-medium transition-all duration-150">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Total Revenue</p>
            <p class="text-3xl font-bold text-gray-900">&#x20A6;{{ number_format($totalRevenue, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">Last {{ $period }} days</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Total Transactions</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalTransactions) }}</p>
            <p class="text-xs text-gray-400 mt-1">Completed payments</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Avg Transaction</p>
            <p class="text-3xl font-bold text-gray-900">&#x20A6;{{ number_format($avgTransaction, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">Per payment</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-1">Revenue Over Time</h2>
        <p class="text-sm text-gray-500 mb-6">Daily earnings for the last {{ $period }} days.</p>
        @if($revenue->isEmpty())
        <div class="flex items-center justify-center h-48 text-gray-400 text-sm">No revenue data for this period.</div>
        @else
        <div class="flex items-end gap-1 h-48 overflow-x-auto pb-1">
            @foreach($revenue as $day)
            @php
                $heightPct = $maxRevenue > 0 ? max(2, round(($day->total / $maxRevenue) * 100)) : 2;
                $dateLabel = \Carbon\Carbon::parse($day->date)->format('M d');
            @endphp
            <div class="flex flex-col items-center gap-1 flex-1 min-w-[24px] group" title="{{ $dateLabel }}: &#x20A6;{{ number_format($day->total, 2) }}">
                <div class="w-full bg-green-500 rounded-t-sm transition-opacity group-hover:opacity-80" style="height: {{ $heightPct }}%; min-height: 2px;"></div>
                <span class="text-gray-400 text-center leading-none" style="font-size: 9px; writing-mode: vertical-rl; transform: rotate(180deg); max-height: 40px; overflow: hidden;">{{ $dateLabel }}</span>
            </div>
            @endforeach
        </div>
        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
            <span class="text-xs text-gray-400">Peak: &#x20A6;{{ number_format($maxRevenue, 2) }}/day</span>
            <span class="text-xs text-gray-400">Total: &#x20A6;{{ number_format($totalRevenue, 2) }}</span>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-1">Top Courses by Revenue</h2>
        <p class="text-sm text-gray-500 mb-5">Highest earning courses in the selected period.</p>
        @if($topCourses->isEmpty())
        <div class="flex items-center justify-center h-32 text-gray-400 text-sm">No course revenue data available.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide pb-3 pr-4 w-10">Rank</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide pb-3 pr-4">Course Title</th>
                        <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wide pb-3 pr-4 w-28">Enrollments</th>
                        <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wide pb-3 pr-4 w-32">Revenue</th>
                        <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wide pb-3 w-16">% Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($topCourses as $i => $course)
                    @php $coursePct = round(($course->revenue / $totalCourseRevenue) * 100); @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 pr-4">
                            @if($i === 0)
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold text-amber-700 bg-amber-50">1</span>
                            @elseif($i === 1)
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold text-gray-500 bg-gray-100">2</span>
                            @elseif($i === 2)
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold text-orange-700 bg-orange-50">3</span>
                            @else
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-medium text-gray-400 bg-gray-50">{{ $i + 1 }}</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4">
                            <p class="font-medium text-gray-900 truncate max-w-[260px]">
                                {{ $course->course->title ?? 'Course #' . $course->course_id }}
                            </p>
                        </td>
                        <td class="py-3 pr-4 text-right text-sm text-gray-700 font-medium">{{ number_format($course->enrollments) }}</td>
                        <td class="py-3 pr-4 text-right text-sm font-semibold text-gray-900">&#x20A6;{{ number_format($course->revenue, 2) }}</td>
                        <td class="py-3 text-right">
                            <span class="inline-flex items-center px-2 py-0.5 bg-green-50 text-green-700 text-xs font-semibold rounded-full">{{ $coursePct }}%</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t border-gray-200">
                    <tr>
                        <td colspan="3" class="pt-3 text-xs font-medium text-gray-500">Showing {{ $topCourses->count() }} courses</td>
                        <td class="pt-3 text-right text-sm font-bold text-gray-900">&#x20A6;{{ number_format($totalRevenue, 2) }}</td>
                        <td class="pt-3 text-right text-xs text-gray-500">100%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-5">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">Full Transaction History</p>
                <p class="text-sm text-gray-500 mt-0.5">See the Payments section for a detailed, searchable list of all individual transactions.</p>
                <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium mt-3 transition-colors" style="color: #4f46e5;">
                    Go to Payments
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
