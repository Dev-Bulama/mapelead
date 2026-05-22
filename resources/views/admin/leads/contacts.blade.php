@extends('layouts.admin')
@section('title', 'Contact Submissions')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Contact Form Submissions</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $contacts->total() }} total submissions</p>
        </div>
        <a href="{{ route('admin.leads.index') }}" class="border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2.5 rounded-xl hover:text-gray-900 transition-colors">← CRM</a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200">
        @if($contacts->isEmpty())
            <div class="p-16 text-center text-gray-500">No contact submissions yet.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Name</th>
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Email</th>
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Subject</th>
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Message</th>
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($contacts as $contact)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $contact->name }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $contact->email }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $contact->subject ?? '—' }}</td>
                                <td class="px-5 py-3 max-w-xs">
                                    <p class="text-gray-600 truncate">{{ $contact->message }}</p>
                                </td>
                                <td class="px-5 py-3 text-gray-500">{{ $contact->created_at->format('M d, Y') }}</td>
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
