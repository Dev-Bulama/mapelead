@extends('layouts.admin')
@section('title', 'Script Injections')

@section('content')
<div class="space-y-6" x-data="{ showForm: false }">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Script Injections</h2>
            <p class="text-sm text-gray-500 mt-1">Inject third-party scripts (Analytics, Chat widgets, Meta Pixel, etc.)</p>
        </div>
        <button @click="showForm = !showForm"
                class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Script
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Add Form --}}
    <div x-show="showForm" x-cloak class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
        <h3 class="font-semibold text-gray-900 border-b border-gray-100 pb-3">Add New Script</h3>
        <form action="{{ route('admin.settings.scripts.store') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Google Analytics"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Provider</label>
                    <input type="text" name="provider" placeholder="e.g. Google"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Inject Location <span class="text-red-500">*</span></label>
                    <select name="location" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="head">Head</option>
                        <option value="body_start">Body Start</option>
                        <option value="body_end">Body End</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Script Code <span class="text-red-500">*</span></label>
                <textarea name="code" rows="6" required
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand-500"
                          placeholder="Paste your full &lt;script&gt; tag or inline code here..."></textarea>
            </div>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-brand-600">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors text-sm">
                    Save Script
                </button>
            </div>
        </form>
    </div>

    {{-- Scripts List --}}
    <div class="bg-white rounded-2xl border border-gray-200">
        @if($scripts->isEmpty())
            <div class="p-16 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                </div>
                <p class="text-gray-500">No scripts added yet.</p>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($scripts->groupBy('location') as $location => $locationScripts)
                    <div class="px-5 py-3 bg-gray-50">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            {{ match($location) { 'head' => '&lt;head&gt;', 'body_start' => '&lt;body&gt; Start', 'body_end' => '&lt;/body&gt; End', default => $location } }}
                        </span>
                    </div>
                    @foreach($locationScripts as $script)
                        <div class="px-5 py-4 flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-1">
                                    <p class="font-semibold text-gray-900 text-sm">{{ $script->name }}</p>
                                    @if($script->provider)
                                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">{{ $script->provider }}</span>
                                    @endif
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $script->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $script->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <pre class="text-xs text-gray-500 bg-gray-50 rounded-lg p-2 truncate">{{ Str::limit($script->code, 120) }}</pre>
                            </div>
                            <form action="{{ route('admin.settings.scripts.destroy', $script->id) }}" method="POST"
                                  onsubmit="return confirm('Delete this script?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors shrink-0">
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endforeach
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
