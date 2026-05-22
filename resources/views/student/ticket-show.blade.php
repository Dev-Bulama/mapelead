@extends('layouts.student')
@section('title', 'Ticket #' . $ticket->ticket_number)
@section('page_title', 'Ticket #' . $ticket->ticket_number)

@section('content')
<div class="space-y-6">

    <div>
        <a href="{{ route('student.tickets') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Support Tickets
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100">
        <div class="p-5 border-b border-gray-100 flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-mono font-bold text-brand-600 bg-brand-50 px-2.5 py-1 rounded-lg">#{{ $ticket->ticket_number }}</span>
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
                        $priorityClass = match($ticket->priority) {
                            'high'   => 'bg-red-100 text-red-700',
                            'medium' => 'bg-yellow-100 text-yellow-700',
                            default  => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityClass }}">
                        {{ ucfirst($ticket->priority) }} Priority
                    </span>
                </div>
                <h2 class="text-lg font-bold text-gray-900">{{ $ticket->subject }}</h2>
                <div class="flex flex-wrap items-center gap-3 mt-1.5">
                    <span class="text-sm text-gray-500">{{ $ticket->category }}</span>
                    <span class="text-gray-300">·</span>
                    <span class="text-sm text-gray-500">Opened {{ $ticket->created_at->format('M j, Y \a\t g:i A') }}</span>
                </div>
            </div>
        </div>

        <div class="p-5">
            <div class="border-l-4 border-brand-400 bg-brand-50 rounded-r-xl px-4 py-4">
                <div class="flex items-center gap-2 mb-2">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->full_name) }}&background=4f46e5&color=fff&size=32"
                         alt="{{ auth()->user()->full_name }}"
                         class="w-7 h-7 rounded-full object-cover">
                    <span class="text-sm font-semibold text-gray-900">{{ auth()->user()->full_name }}</span>
                    <span class="text-xs text-gray-400">{{ $ticket->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $ticket->description }}</p>
            </div>
        </div>
    </div>

    @if($ticket->replies->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100">
            <div class="p-5 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Conversation</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($ticket->replies as $reply)
                    @php
                        $isStaff = $reply->user && (method_exists($reply->user, 'hasRole') ? $reply->user->hasRole('admin') : $reply->user->role === 'admin');
                        $avatarName = urlencode($reply->user->full_name ?? 'Support Team');
                        $avatarBg   = $isStaff ? '4f46e5' : '6b7280';
                    @endphp
                    <div class="p-5 {{ $isStaff ? 'bg-brand-50/40' : '' }}">
                        <div class="flex items-start gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ $avatarName }}&background={{ $avatarBg }}&color=fff&size=40"
                                 alt="{{ $reply->user->full_name ?? 'Support Team' }}"
                                 class="w-9 h-9 rounded-full object-cover shrink-0 ring-2 {{ $isStaff ? 'ring-brand-200' : 'ring-gray-100' }}">
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ $isStaff ? 'Support Team' : ($reply->user->full_name ?? 'You') }}
                                    </span>
                                    @if($isStaff)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-brand-100 text-brand-700">Staff</span>
                                    @endif
                                    <span class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed {{ $isStaff ? 'bg-white rounded-xl px-4 py-3 border border-brand-100' : 'bg-gray-50 rounded-xl px-4 py-3 border border-gray-100' }}">{{ $reply->message }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($ticket->status !== 'closed')
        <div class="bg-white rounded-2xl border border-gray-100">
            <div class="p-5 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Reply</h3>
            </div>
            <form action="{{ route('student.tickets.reply', $ticket->id) }}" method="POST" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Your Reply</label>
                    <textarea name="message" required rows="5"
                              placeholder="Type your message here..."
                              class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-shadow resize-none">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Send Reply
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-gray-50 border border-gray-200 rounded-2xl px-5 py-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-gray-200 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-700">This ticket is closed</p>
                <p class="text-xs text-gray-500 mt-0.5">If you need further assistance, please open a new ticket.</p>
            </div>
            <a href="{{ route('student.tickets') }}"
               class="ml-auto shrink-0 text-sm font-medium text-brand-600 hover:text-brand-700 hover:underline transition-colors">
                Open New Ticket
            </a>
        </div>
    @endif

</div>
@endsection
