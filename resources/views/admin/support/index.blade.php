@extends('layouts.admin')
@section('title', 'Support Tickets')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Support Tickets</h1>
        <p class="text-sm text-gray-500 mt-0.5">Manage and respond to student support requests.</p>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm p-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm p-4">{{ session('error') }}</div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border p-5">
            <p class="text-xs text-gray-500 mb-1">Open</p>
            <p class="text-2xl font-bold text-blue-600">{{ $openCount }}</p>
        </div>
        <div class="bg-white rounded-xl border p-5">
            <p class="text-xs text-gray-500 mb-1">In Progress</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $inProgressCount }}</p>
        </div>
        <div class="bg-white rounded-xl border p-5">
            <p class="text-xs text-gray-500 mb-1">Resolved</p>
            <p class="text-2xl font-bold text-green-600">{{ $resolvedCount }}</p>
        </div>
        <div class="bg-white rounded-xl border p-5">
            <p class="text-xs text-gray-500 mb-1">Closed</p>
            <p class="text-2xl font-bold text-gray-500">{{ $closedCount }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.support.index') }}" class="bg-white rounded-xl border p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by subject, ticket # or student name..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2">
            </div>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
                <option value="">All Statuses</option>
                <option value="open" @selected(request('status') === 'open')>Open</option>
                <option value="in_progress" @selected(request('status') === 'in_progress')>In Progress</option>
                <option value="resolved" @selected(request('status') === 'resolved')>Resolved</option>
                <option value="closed" @selected(request('status') === 'closed')>Closed</option>
            </select>
            <select name="priority" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
                <option value="">All Priorities</option>
                <option value="high" @selected(request('priority') === 'high')>High</option>
                <option value="medium" @selected(request('priority') === 'medium')>Medium</option>
                <option value="low" @selected(request('priority') === 'low')>Low</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium text-white hover:opacity-90 transition" style="background-color:#14215B;">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'priority']))
            <a href="{{ route('admin.support.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Clear</a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Ticket #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Subject</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Student</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Priority</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Created</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm font-mono text-gray-700">{{ $ticket->ticket_number }}</td>
                        <td class="px-4 py-3">
                            <p class="text-sm font-medium text-gray-900">{{ \Str::limit($ticket->subject, 50) }}</p>
                            @if($ticket->replies->count())
                            <p class="text-xs text-gray-400">{{ $ticket->replies->count() }} {{ Str::plural('reply', $ticket->replies->count()) }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($ticket->user)
                            <div class="text-sm text-gray-900">{{ $ticket->user->first_name }} {{ $ticket->user->last_name }}</div>
                            <div class="text-xs text-gray-500">{{ $ticket->user->email }}</div>
                            @else
                            <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $priorityMap = [
                                    'high'   => 'bg-red-100 text-red-700',
                                    'medium' => 'bg-yellow-100 text-yellow-700',
                                    'low'    => 'bg-blue-100 text-blue-700',
                                ];
                                $pcls = $priorityMap[$ticket->priority] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $pcls }}">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusMap = [
                                    'open'        => 'bg-blue-100 text-blue-700',
                                    'in_progress' => 'bg-yellow-100 text-yellow-700',
                                    'resolved'    => 'bg-green-100 text-green-700',
                                    'closed'      => 'bg-gray-100 text-gray-600',
                                ];
                                $scls = $statusMap[$ticket->status] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $scls }}">
                                {{ ucwords(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $ticket->created_at->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.support.show', $ticket) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-medium text-white hover:opacity-90 transition"
                               style="background-color:#14215B;">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">No tickets found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
