@extends('layouts.student')
@section('title', 'Support')
@section('page_title', 'Support Tickets')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">

    {{-- Top bar --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">Manage and track your support requests</p>
        <button @click="showModal = true"
                class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Ticket
        </button>
    </div>

    {{-- Stats Strip --}}
    @php
        $allTickets   = $tickets->getCollection();
        $totalCount   = $tickets->total();
        $openCount    = $allTickets->where('status', 'open')->count();
        $inProgCount  = $allTickets->where('status', 'in_progress')->count();
        $closedCount  = $allTickets->whereIn('status', ['resolved', 'closed'])->count();
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Total Tickets', 'value' => $totalCount,  'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'brand'],
            ['label' => 'Open',          'value' => $openCount,   'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',        'color' => 'blue'],
            ['label' => 'In Progress',   'value' => $inProgCount, 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',                                                                                    'color' => 'yellow'],
            ['label' => 'Resolved',      'value' => $closedCount, 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                                                 'color' => 'green'],
        ] as $stat)
            <div class="bg-white rounded-2xl p-4 border border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $stat['label'] }}</span>
                    <div class="w-8 h-8 bg-{{ $stat['color'] }}-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Tickets Table / Empty State --}}
    <div class="bg-white rounded-2xl border border-gray-100">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Your Tickets</h3>
        </div>

        @if($tickets->isEmpty())
            <div class="p-16 text-center">
                <div class="w-20 h-20 bg-brand-50 rounded-2xl flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">No tickets yet</h3>
                <p class="text-gray-500 mt-2 mb-6">Our support team is ready to help!</p>
                <button @click="showModal = true"
                        class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-3 rounded-xl transition-colors inline-block">
                    Open a Ticket
                </button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Ticket #</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Subject</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3 hidden md:table-cell">Category</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Priority</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3">Status</th>
                            <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide px-5 py-3 hidden lg:table-cell">Date</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($tickets as $ticket)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-4">
                                    <span class="text-sm font-mono font-semibold text-brand-600">#{{ $ticket->ticket_number }}</span>
                                </td>
                                <td class="px-5 py-4 max-w-xs">
                                    <span class="text-sm font-medium text-gray-900 line-clamp-1">{{ $ticket->subject }}</span>
                                </td>
                                <td class="px-5 py-4 hidden md:table-cell">
                                    <span class="text-sm text-gray-600">{{ $ticket->category }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    @php
                                        $priorityClass = match($ticket->priority) {
                                            'high'   => 'bg-red-100 text-red-700',
                                            'medium' => 'bg-yellow-100 text-yellow-700',
                                            default  => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityClass }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @php
                                        $statusClass = match($ticket->status) {
                                            'open'        => 'bg-blue-100 text-blue-700',
                                            'in_progress' => 'bg-yellow-100 text-yellow-700',
                                            'resolved'    => 'bg-green-100 text-green-700',
                                            default       => 'bg-gray-100 text-gray-600',
                                        };
                                        $statusLabel = match($ticket->status) {
                                            'in_progress' => 'In Progress',
                                            default       => ucfirst($ticket->status),
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    <span class="text-sm text-gray-500">{{ $ticket->created_at->format('M j, Y') }}</span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('student.tickets.show', $ticket->id) }}"
                                       class="text-sm font-medium text-brand-600 hover:text-brand-700 hover:underline transition-colors">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($tickets->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $tickets->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- New Ticket Modal --}}
    <div x-show="showModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="absolute inset-0 bg-black/50" @click="showModal = false"></div>

        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">New Support Ticket</h3>
                <button @click="showModal = false"
                        class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('student.tickets.store') }}" method="POST" class="p-5 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Subject</label>
                    <input type="text" name="subject" required
                           placeholder="Brief description of your issue"
                           class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-shadow">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Category</label>
                        <select name="category" required
                                class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-shadow bg-white">
                            <option value="">Select category</option>
                            <option value="Course Issues">Course Issues</option>
                            <option value="Technical Support">Technical Support</option>
                            <option value="Payment">Payment</option>
                            <option value="General">General</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Priority</label>
                        <select name="priority" required
                                class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-shadow bg-white">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                    <textarea name="description" required rows="4"
                              placeholder="Please describe your issue in detail..."
                              class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-shadow resize-none"></textarea>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" @click="showModal = false"
                            class="flex-1 border border-gray-200 text-gray-700 font-medium py-2.5 rounded-xl hover:bg-gray-50 transition-colors text-sm">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2.5 rounded-xl transition-colors text-sm">
                        Submit Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
