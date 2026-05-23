@extends('layouts.admin')
@section('title', 'Ticket — ' . $support->ticket_number)

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $support->subject }}</h1>
            <p class="text-sm text-gray-500 mt-0.5 font-mono">{{ $support->ticket_number }}</p>
        </div>
        <a href="{{ route('admin.support.index') }}"
           class="text-sm text-gray-600 bg-white border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
            &larr; Back to Tickets
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm p-4">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Description --}}
            <div class="bg-white rounded-xl border p-5">
                <h2 class="text-sm font-semibold text-gray-900 mb-3">Description</h2>
                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $support->description }}</p>
            </div>

            {{-- Replies Thread --}}
            <div class="bg-white rounded-xl border p-5">
                <h2 class="text-sm font-semibold text-gray-900 mb-4">
                    Replies
                    @if($support->replies->count())
                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600 font-medium">
                        {{ $support->replies->count() }}
                    </span>
                    @endif
                </h2>

                @if($support->replies->isEmpty())
                <p class="text-sm text-gray-400 py-6 text-center">No replies yet.</p>
                @else
                <div class="space-y-4">
                    @foreach($support->replies as $reply)
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0"
                             style="background-color:#14215B">
                            {{ strtoupper(substr($reply->user?->first_name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $reply->user?->first_name }} {{ $reply->user?->last_name }}
                                </span>
                                @if($reply->is_internal)
                                <span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700 font-medium">Internal</span>
                                @endif
                                <span class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                                {{ $reply->message }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Reply Form --}}
                <div class="mt-6 pt-5 border-t border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Add Reply</h3>
                    <form action="{{ route('admin.support.reply', $support) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea name="message" rows="4" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
                                placeholder="Type your reply...">{{ old('message') }}</textarea>
                            @error('message')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_internal" value="1" class="w-4 h-4 rounded border-gray-300">
                                <span class="text-xs text-gray-600">Internal note (not visible to student)</span>
                            </label>
                            <button type="submit"
                                    class="px-4 py-2 rounded-lg text-sm font-medium text-white hover:opacity-90 transition"
                                    style="background-color:#14215B;">
                                Send Reply
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">

            {{-- Ticket Info --}}
            <div class="bg-white rounded-xl border p-5">
                <h2 class="text-sm font-semibold text-gray-900 mb-4">Ticket Info</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-gray-500">Status</dt>
                        <dd class="mt-0.5">
                            @php
                                $statusMap = [
                                    'open'        => 'bg-blue-100 text-blue-700',
                                    'in_progress' => 'bg-yellow-100 text-yellow-700',
                                    'resolved'    => 'bg-green-100 text-green-700',
                                    'closed'      => 'bg-gray-100 text-gray-600',
                                ];
                                $scls = $statusMap[$support->status] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $scls }}">
                                {{ ucwords(str_replace('_', ' ', $support->status)) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Priority</dt>
                        <dd class="mt-0.5">
                            @php
                                $priorityMap = [
                                    'high'   => 'bg-red-100 text-red-700',
                                    'medium' => 'bg-yellow-100 text-yellow-700',
                                    'low'    => 'bg-blue-100 text-blue-700',
                                ];
                                $pcls = $priorityMap[$support->priority] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $pcls }}">
                                {{ ucfirst($support->priority) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Created</dt>
                        <dd class="text-gray-900">{{ $support->created_at->format('d M Y, H:i') }}</dd>
                    </div>
                    @if($support->resolved_at)
                    <div>
                        <dt class="text-xs text-gray-500">Resolved</dt>
                        <dd class="text-gray-900">{{ $support->resolved_at->format('d M Y, H:i') }}</dd>
                    </div>
                    @endif
                    @if($support->category)
                    <div>
                        <dt class="text-xs text-gray-500">Category</dt>
                        <dd class="text-gray-900">{{ $support->category }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Student --}}
            <div class="bg-white rounded-xl border p-5">
                <h2 class="text-sm font-semibold text-gray-900 mb-3">Student</h2>
                @if($support->user)
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0"
                         style="background-color:#14215B">
                        {{ strtoupper(substr($support->user->first_name, 0, 1)) }}{{ strtoupper(substr($support->user->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $support->user->first_name }} {{ $support->user->last_name }}</p>
                        <p class="text-xs text-gray-500">{{ $support->user->email }}</p>
                    </div>
                </div>
                @else
                <p class="text-sm text-gray-400">No student linked.</p>
                @endif
            </div>

            {{-- Update Status / Assign --}}
            <div class="bg-white rounded-xl border p-5">
                <h2 class="text-sm font-semibold text-gray-900 mb-4">Update Ticket</h2>
                <form action="{{ route('admin.support.update', $support) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
                                <option value="open" @selected($support->status === 'open')>Open</option>
                                <option value="in_progress" @selected($support->status === 'in_progress')>In Progress</option>
                                <option value="resolved" @selected($support->status === 'resolved')>Resolved</option>
                                <option value="closed" @selected($support->status === 'closed')>Closed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Assigned To</label>
                            <input type="number" name="assigned_to" value="{{ $support->assigned_to }}"
                                   placeholder="User ID"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
                        </div>
                        <button type="submit"
                                class="w-full px-4 py-2 rounded-lg text-sm font-medium text-white hover:opacity-90 transition"
                                style="background-color:#14215B;">
                            Update
                        </button>
                    </div>
                </form>

                @if($support->status !== 'closed')
                <form action="{{ route('admin.support.close', $support->id) }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit"
                            class="w-full px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition"
                            onclick="return confirm('Close this ticket?')">
                        Close Ticket
                    </button>
                </form>
                @endif

                <form action="{{ route('admin.support.destroy', $support) }}" method="POST" class="mt-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-4 py-2 rounded-lg text-sm font-medium bg-red-50 text-red-600 hover:bg-red-100 transition"
                            onclick="return confirm('Delete this ticket permanently?')">
                        Delete Ticket
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection
