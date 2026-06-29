@extends('layouts.admin')
@section('title', 'Contact Submissions')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Contact Form Submissions</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $contacts->total() }} total submissions</p>
        </div>
        <div class="flex items-center gap-3">
            @php $unread = \App\Models\ContactForm::where('status', 'new')->count(); @endphp
            @if($unread)
            <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                {{ $unread }} unread
            </span>
            @endif
            <a href="{{ route('admin.contacts.export') }}" class="border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-xl hover:bg-gray-50 transition-colors">
                Export CSV
            </a>
            <a href="{{ route('admin.leads.index') }}" class="border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-xl hover:bg-gray-50 transition-colors">
                ← CRM
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm flex items-center gap-2">
        <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        @if($contacts->isEmpty())
            <div class="p-16 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm font-medium text-gray-500">No contact submissions yet.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3 w-5"></th>
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">From</th>
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Subject</th>
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Preview</th>
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Status</th>
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Date</th>
                            <th class="text-right font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($contacts as $contact)
                        @php
                        $isNew = $contact->status === 'new';
                        $statusColors = [
                            'new'      => 'bg-blue-50 text-blue-700',
                            'read'     => 'bg-gray-100 text-gray-500',
                            'replied'  => 'bg-green-50 text-green-700',
                            'archived' => 'bg-yellow-50 text-yellow-700',
                        ];
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors {{ $isNew ? 'bg-blue-50/30' : '' }}">
                            <td class="px-4 py-3.5 w-5">
                                @if($isNew)
                                <span class="block w-2 h-2 bg-blue-500 rounded-full"></span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-gray-900 {{ $isNew ? '' : 'font-medium' }}">{{ $contact->name }}</div>
                                <div class="text-xs text-gray-400">{{ $contact->email }}</div>
                                @if($contact->phone)
                                <div class="text-xs text-gray-400">{{ $contact->phone }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-gray-700 {{ $isNew ? 'font-semibold' : '' }}">{{ $contact->subject ?? '(no subject)' }}</span>
                            </td>
                            <td class="px-5 py-3.5 max-w-xs">
                                <p class="text-gray-500 text-xs truncate">{{ Str::limit($contact->message, 80) }}</p>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColors[$contact->status] ?? 'bg-gray-100 text-gray-500' }}">
                                    {{ ucfirst($contact->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-400 text-xs whitespace-nowrap">
                                {{ $contact->created_at->format('M d, Y') }}
                                <div>{{ $contact->created_at->format('g:i A') }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('admin.contacts.show', $contact) }}"
                                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:text-brand-800 px-3 py-1.5 rounded-lg hover:bg-brand-50 transition-colors">
                                    View & Reply
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($contacts->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $contacts->links() }}</div>
            @endif
        @endif
    </div>

</div>
@endsection
