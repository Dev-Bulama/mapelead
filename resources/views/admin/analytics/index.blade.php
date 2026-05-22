@extends('layouts.admin')

@section('title', 'Analytics')

@section('content')
@php
    $totalViews = $pageViews->sum('views');
    $uniquePages = $topPages->count();
    $totalNewUsers = $newUsers->sum('count');
    $avgPerDay = $period > 0 ? round($totalViews / $period) : 0;
    $maxViews = $pageViews->max('views') ?: 1;
    $maxNewUsers = $newUsers->max('count') ?: 1;
    $totalTopViews = $topPages->sum('views') ?: 1;
    $deviceTotal = $deviceBreakdown->sum('count') ?: 1;
@endphp

<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Analytics</h1>
            <p class="text-sm text-gray-500 mt-1">Traffic and engagement overview.</p>
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

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Total Page Views</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalViews) }}</p>
            <p class="text-xs text-gray-400 mt-1">Last {{ $period }} days</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Unique Pages</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($uniquePages) }}</p>
            <p class="text-xs text-gray-400 mt-1">Pages visited</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">New Users</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalNewUsers) }}</p>
            <p class="text-xs text-gray-400 mt-1">Registrations</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Avg / Day</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($avgPerDay) }}</p>
            <p class="text-xs text-gray-400 mt-1">Page views per day</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6" x-data>
            <h2 class="text-base font-semibold text-gray-900 mb-1">Page Views</h2>
            <p class="text-sm text-gray-500 mb-6">Daily traffic over the last {{ $period }} days.</p>
            @if($pageViews->isEmpty())
            <div class="flex items-center justify-center h-48 text-gray-400 text-sm">No data for this period.</div>
            @else
            <div class="flex items-end gap-1 h-48 overflow-x-auto pb-1">
                @foreach($pageViews as $day)
                @php
                    $heightPct = $maxViews > 0 ? max(2, round(($day->views / $maxViews) * 100)) : 2;
                    $dateLabel = \Carbon\Carbon::parse($day->date)->format('M d');
                @endphp
                <div class="flex flex-col items-center gap-1 flex-1 min-w-[24px] group" title="{{ $dateLabel }}: {{ number_format($day->views) }} views">
                    <div class="w-full rounded-t-sm transition-opacity group-hover:opacity-80" style="height: {{ $heightPct }}%; background-color: #6366f1; min-height: 2px;"></div>
                    <span class="text-gray-400 text-center leading-none" style="font-size: 9px; writing-mode: vertical-rl; transform: rotate(180deg); max-height: 40px; overflow: hidden;">{{ $dateLabel }}</span>
                </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                <span class="text-xs text-gray-400">Max: {{ number_format($maxViews) }} views/day</span>
                <span class="text-xs text-gray-400">Total: {{ number_format($totalViews) }}</span>
            </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-1">Device Breakdown</h2>
            <p class="text-sm text-gray-500 mb-6">Traffic by device type.</p>
            @if($deviceBreakdown->isEmpty())
            <div class="flex items-center justify-center h-32 text-gray-400 text-sm">No data.</div>
            @else
            <div class="space-y-4">
                @php
                    $deviceColors = ['desktop' => 'bg-indigo-500', 'mobile' => 'bg-violet-500', 'tablet' => 'bg-indigo-300'];
                @endphp
                @foreach($deviceBreakdown as $device)
                @php
                    $pct = round(($device->count / $deviceTotal) * 100);
                    $colorKey = strtolower($device->device);
                    $barColor = $deviceColors[$colorKey] ?? 'bg-gray-400';
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700 capitalize">{{ $device->device }}</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $pct }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5">
                        <div class="{{ $barColor }} h-2.5 rounded-full transition-all duration-300" style="width: {{ $pct }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">{{ number_format($device->count) }} sessions</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-1">Top Pages</h2>
            <p class="text-sm text-gray-500 mb-5">Most visited URLs in the last {{ $period }} days.</p>
            @if($topPages->isEmpty())
            <div class="flex items-center justify-center h-32 text-gray-400 text-sm">No data for this period.</div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide pb-2 pr-3 w-8">#</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide pb-2 pr-3">URL</th>
                            <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wide pb-2 pr-3 w-16">Views</th>
                            <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wide pb-2 w-12">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($topPages as $i => $page)
                        @php $pagePct = round(($page->views / $totalTopViews) * 100); @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-2.5 pr-3 text-xs text-gray-400 font-medium">{{ $i + 1 }}</td>
                            <td class="py-2.5 pr-3">
                                <a href="{{ $page->url }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 hover:underline font-medium truncate block max-w-[180px]" title="{{ $page->url }}">
                                    {{ Str::limit($page->url, 40) }}
                                </a>
                            </td>
                            <td class="py-2.5 pr-3 text-right text-sm font-semibold text-gray-900">{{ number_format($page->views) }}</td>
                            <td class="py-2.5 text-right text-xs text-gray-500">{{ $pagePct }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-6" x-data>
            <h2 class="text-base font-semibold text-gray-900 mb-1">New User Registrations</h2>
            <p class="text-sm text-gray-500 mb-6">Daily signups over the last {{ $period }} days.</p>
            @if($newUsers->isEmpty())
            <div class="flex items-center justify-center h-48 text-gray-400 text-sm">No data for this period.</div>
            @else
            <div class="flex items-end gap-1 h-48 overflow-x-auto pb-1">
                @foreach($newUsers as $day)
                @php
                    $heightPct = $maxNewUsers > 0 ? max(2, round(($day->count / $maxNewUsers) * 100)) : 2;
                    $dateLabel = \Carbon\Carbon::parse($day->date)->format('M d');
                @endphp
                <div class="flex flex-col items-center gap-1 flex-1 min-w-[24px] group" title="{{ $dateLabel }}: {{ $day->count }} new users">
                    <div class="w-full rounded-t-sm transition-opacity group-hover:opacity-80" style="height: {{ $heightPct }}%; background-color: #818cf8; min-height: 2px;"></div>
                    <span class="text-gray-400 text-center leading-none" style="font-size: 9px; writing-mode: vertical-rl; transform: rotate(180deg); max-height: 40px; overflow: hidden;">{{ $dateLabel }}</span>
                </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                <span class="text-xs text-gray-400">Peak: {{ number_format($maxNewUsers) }} users/day</span>
                <span class="text-xs text-gray-400">Total: {{ number_format($totalNewUsers) }}</span>
            </div>
            @endif
        </div>

    </div>

</div>
@endsection
