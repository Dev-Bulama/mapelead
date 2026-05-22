@extends('layouts.admin')
@section('title', 'Leads & CRM')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Leads & CRM</h2>
            <p class="text-sm text-gray-500 mt-1">Manage prospective students and follow-ups</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.newsletter') }}" class="border border-gray-200 text-gray-600 hover:text-gray-900 text-sm font-medium px-4 py-2 rounded-xl transition-colors">Newsletter Subscribers</a>
            <a href="{{ route('admin.contacts') }}" class="border border-gray-200 text-gray-600 hover:text-gray-900 text-sm font-medium px-4 py-2 rounded-xl transition-colors">Contact Forms</a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-2xl border border-gray-200 p-4 flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
               class="flex-1 min-w-[200px] border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
        <select name="status" class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            <option value="">All Statuses</option>
            @foreach(['new' => 'New', 'contacted' => 'Contacted', 'qualified' => 'Qualified', 'enrolled' => 'Enrolled', 'lost' => 'Lost'] as $val => $label)
                <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="source" class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            <option value="">All Sources</option>
            @foreach(['website' => 'Website', 'social' => 'Social Media', 'referral' => 'Referral', 'organic' => 'Organic'] as $val => $label)
                <option value="{{ $val }}" {{ request('source') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">Filter</button>
        @if(request()->hasAny(['search','status','source']))
            <a href="{{ route('admin.leads.index') }}" class="border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-xl hover:bg-gray-50 transition-colors">Clear</a>
        @endif
    </form>

    <div class="bg-white rounded-2xl border border-gray-200">
        @if($leads->isEmpty())
            <div class="p-16 text-center">
                <div class="w-16 h-16 bg-brand-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-gray-500">No leads found.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Name / Email</th>
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Source</th>
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Status</th>
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3 hidden lg:table-cell">Last Contact</th>
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3 hidden md:table-cell">Created</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($leads as $lead)
                            @php
                                $statusColors = ['new'=>'bg-blue-100 text-blue-700','contacted'=>'bg-yellow-100 text-yellow-700','qualified'=>'bg-purple-100 text-purple-700','enrolled'=>'bg-green-100 text-green-700','lost'=>'bg-gray-100 text-gray-600'];
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-4">
                                    <p class="font-medium text-gray-900">{{ $lead->first_name }} {{ $lead->last_name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $lead->email }}</p>
                                </td>
                                <td class="px-5 py-4 capitalize text-gray-600">{{ $lead->source ?? '—' }}</td>
                                <td class="px-5 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$lead->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($lead->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 hidden lg:table-cell text-gray-500">{{ $lead->last_contacted_at?->diffForHumans() ?? 'Never' }}</td>
                                <td class="px-5 py-4 hidden md:table-cell text-gray-500">{{ $lead->created_at->format('M d, Y') }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.leads.show', $lead->id) }}" class="text-brand-600 hover:text-brand-700 text-xs font-medium">View</a>
                                        <a href="{{ route('admin.leads.edit', $lead->id) }}" class="text-gray-600 hover:text-gray-900 text-xs font-medium">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($leads->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $leads->links() }}</div>
            @endif
        @endif
    </div>

</div>
@endsection
