@extends('layouts.student')
@section('title', 'Notifications')
@section('page_title', 'Notifications')

@section('content')
@php
    $notifications = $notifications ?? auth()->user()->notificationsLog()->paginate(20);
@endphp
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">Stay up to date with your latest activity</p>
        <form action="{{ route('student.notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-2 text-sm font-medium text-brand-600 hover:text-brand-700 bg-brand-50 hover:bg-brand-100 px-4 py-2 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Mark all as read
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100">
        @if($notifications->isEmpty())
            <div class="p-16 text-center">
                <div class="w-20 h-20 bg-brand-50 rounded-2xl flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">You're all caught up!</h3>
                <p class="text-gray-500 mt-2">No notifications at the moment. We'll let you know when something happens.</p>
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach($notifications as $notification)
                    @php
                        $iconBg = match($notification->type) {
                            'success' => 'bg-green-100',
                            'warning' => 'bg-yellow-100',
                            'error'   => 'bg-red-100',
                            default   => 'bg-brand-100',
                        };
                        $iconColor = match($notification->type) {
                            'success' => 'text-green-600',
                            'warning' => 'text-yellow-600',
                            'error'   => 'text-red-600',
                            default   => 'text-brand-600',
                        };
                        $iconPath = match($notification->type) {
                            'success' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                            'warning' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                            'error'   => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                            default   => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                        };
                    @endphp
                    <div class="flex items-start gap-4 px-5 py-4 transition-colors {{ $notification->is_read ? '' : 'bg-brand-50' }}">
                        <div class="w-9 h-9 {{ $iconBg }} rounded-full flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-semibold text-gray-900 {{ $notification->is_read ? '' : 'text-gray-900' }}">
                                    {{ $notification->title }}
                                </p>
                                @if(!$notification->is_read)
                                    <span class="w-2 h-2 rounded-full bg-brand-500 shrink-0 mt-1.5"></span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 mt-0.5 leading-relaxed">{{ $notification->message }}</p>
                            <p class="text-xs text-gray-400 mt-1.5">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($notifications->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $notifications->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
