@extends('layouts.admin')
@section('title', 'Lead: ' . $lead->first_name . ' ' . $lead->last_name)

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Lead Profile</h2>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.leads.edit', $lead->id) }}" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">Edit Lead</a>
            <a href="{{ route('admin.leads.index') }}" class="border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2.5 rounded-xl hover:text-gray-900 transition-colors">← Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 p-5 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-brand-100 rounded-xl flex items-center justify-center">
                        <span class="text-brand-700 font-bold text-lg">{{ strtoupper(substr($lead->first_name, 0, 1)) }}{{ strtoupper(substr($lead->last_name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900">{{ $lead->first_name }} {{ $lead->last_name }}</p>
                        <p class="text-xs text-gray-500">{{ $lead->email }}</p>
                    </div>
                </div>
                @php
                    $statusColors = ['new'=>'bg-blue-100 text-blue-700','contacted'=>'bg-yellow-100 text-yellow-700','qualified'=>'bg-purple-100 text-purple-700','enrolled'=>'bg-green-100 text-green-700','lost'=>'bg-gray-100 text-gray-600'];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $statusColors[$lead->status] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ ucfirst($lead->status) }}
                </span>
                <div class="space-y-3 text-sm pt-2 border-t border-gray-100">
                    @foreach([
                        ['label' => 'Phone', 'value' => $lead->phone ?? '—'],
                        ['label' => 'Source', 'value' => ucfirst($lead->source ?? '—')],
                        ['label' => 'Last Contact', 'value' => $lead->last_contacted_at?->diffForHumans() ?? 'Never'],
                        ['label' => 'Created', 'value' => $lead->created_at->format('M d, Y')],
                    ] as $row)
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ $row['label'] }}</span>
                            <span class="text-gray-900 font-medium">{{ $row['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Notes</h3>
                @if($lead->notes)
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $lead->notes }}</p>
                @else
                    <p class="text-sm text-gray-400">No notes yet. <a href="{{ route('admin.leads.edit', $lead->id) }}" class="text-brand-600 hover:underline">Add a note</a></p>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
