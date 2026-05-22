@extends('layouts.admin')
@section('title', 'Newsletter Subscribers')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Newsletter Subscribers</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $subscribers->total() }} total subscribers</p>
        </div>
        <a href="{{ route('admin.leads.index') }}" class="border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2.5 rounded-xl hover:text-gray-900 transition-colors">← CRM</a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200">
        @if($subscribers->isEmpty())
            <div class="p-16 text-center text-gray-500">No subscribers yet.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Email</th>
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Name</th>
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Status</th>
                            <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Subscribed</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($subscribers as $sub)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $sub->email }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $sub->name ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($sub->is_active ?? true) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ ($sub->is_active ?? true) ? 'Active' : 'Unsubscribed' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-gray-500">{{ $sub->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($subscribers->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $subscribers->links() }}</div>
            @endif
        @endif
    </div>

</div>
@endsection
