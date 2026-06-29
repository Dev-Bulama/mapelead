@extends('layouts.admin')
@section('title', 'Contact Message — ' . $contact->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.contacts') }}" class="hover:text-brand-600 transition-colors">Contact Submissions</a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-700 font-medium">{{ $contact->name }}</span>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm flex items-center gap-2">
        <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Message Card --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        {{-- Header --}}
        <div class="p-6 border-b border-gray-100 flex items-start justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-brand-100 rounded-full flex items-center justify-center shrink-0">
                    <span class="text-brand-700 font-bold text-lg">{{ strtoupper(substr($contact->name, 0, 1)) }}</span>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900 text-lg">{{ $contact->name }}</h2>
                    <div class="flex items-center gap-3 mt-0.5">
                        <a href="mailto:{{ $contact->email }}" class="text-brand-600 hover:underline text-sm">{{ $contact->email }}</a>
                        @if($contact->phone)
                        <span class="text-gray-400 text-sm">·</span>
                        <a href="tel:{{ $contact->phone }}" class="text-gray-500 text-sm hover:text-gray-700">{{ $contact->phone }}</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                {{-- Status Badge --}}
                @php
                $statusColors = [
                    'new'      => 'bg-blue-50 text-blue-700',
                    'read'     => 'bg-gray-100 text-gray-600',
                    'replied'  => 'bg-green-50 text-green-700',
                    'archived' => 'bg-yellow-50 text-yellow-700',
                ];
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$contact->status] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ ucfirst($contact->status) }}
                </span>
                {{-- Change Status --}}
                <form action="{{ route('admin.contacts.status', $contact) }}" method="POST">
                    @csrf @method('PATCH')
                    <select name="status" onchange="this.form.submit()"
                            class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-brand-500 outline-none bg-white text-gray-600">
                        @foreach(['new' => 'New', 'read' => 'Read', 'replied' => 'Replied', 'archived' => 'Archive'] as $val => $label)
                        <option value="{{ $val }}" {{ $contact->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        {{-- Meta info --}}
        <div class="px-6 py-3 bg-gray-50 border-b border-gray-100 flex flex-wrap gap-4 text-xs text-gray-500">
            <span><span class="font-medium text-gray-700">Subject:</span> {{ $contact->subject ?? '(no subject)' }}</span>
            <span><span class="font-medium text-gray-700">Type:</span> {{ ucfirst(str_replace('_', ' ', $contact->form_type)) }}</span>
            <span><span class="font-medium text-gray-700">Received:</span> {{ $contact->created_at->format('M d, Y \a\t g:i A') }}</span>
            @if($contact->ip_address)
            <span><span class="font-medium text-gray-700">IP:</span> {{ $contact->ip_address }}</span>
            @endif
        </div>

        {{-- Message body --}}
        <div class="p-6">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Message</h3>
            <div class="bg-gray-50 rounded-xl p-4 text-gray-700 leading-relaxed whitespace-pre-wrap text-sm border border-gray-100">{{ $contact->message }}</div>
        </div>

        {{-- Previous reply --}}
        @if($contact->admin_reply)
        <div class="px-6 pb-6">
            <div class="border-t border-dashed border-gray-200 pt-5">
                <h3 class="text-xs font-semibold text-green-600 uppercase tracking-wide mb-3 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    Your Reply — sent {{ $contact->replied_at?->format('M d, Y \a\t g:i A') }}
                </h3>
                <div class="bg-green-50 rounded-xl p-4 text-gray-700 leading-relaxed whitespace-pre-wrap text-sm border border-green-100">{{ $contact->admin_reply }}</div>
            </div>
        </div>
        @endif
    </div>

    {{-- Reply Form --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
            {{ $contact->admin_reply ? 'Send Another Reply' : 'Reply to ' . $contact->name }}
        </h3>

        @if($errors->any())
        <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm mb-4">
            @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
        </div>
        @endif

        <form action="{{ route('admin.contacts.reply', $contact) }}" method="POST">
            @csrf
            <div class="mb-1.5 text-xs text-gray-400">
                Sending to: <span class="font-medium text-gray-600">{{ $contact->email }}</span>
                · Subject: <span class="font-medium text-gray-600">Re: {{ $contact->subject ?? 'Your Message' }}</span>
            </div>
            <textarea name="reply" rows="6" required
                      class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none resize-y"
                      placeholder="Type your reply here...">{{ old('reply') }}</textarea>
            <div class="flex items-center justify-between mt-4">
                <a href="{{ route('admin.contacts') }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">← Back to all messages</a>
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Send Reply
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
