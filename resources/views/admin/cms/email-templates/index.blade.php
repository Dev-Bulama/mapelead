@extends('layouts.admin')
@section('title', 'Email Templates')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Email Templates</h1>
            <p class="text-gray-500 text-sm mt-1">Manage automated email templates with dynamic placeholders</p>
        </div>
        <a href="{{ route('admin.email-templates.create') }}" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-700">+ New Template</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Template</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Slug</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Type</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Status</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Last Used</th>
                    <th class="text-left px-4 py-3 text-gray-600 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($emailTemplates as $template)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $template->name }}</div>
                        <div class="text-xs text-gray-400 truncate max-w-xs">{{ $template->subject }}</div>
                    </td>
                    <td class="px-4 py-3"><code class="text-xs bg-gray-100 px-2 py-0.5 rounded">{{ $template->slug }}</code></td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $template->is_system ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $template->is_system ? 'System' : 'Custom' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <form action="{{ route('admin.email-templates.toggle', $template) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-2 py-0.5 rounded-full text-xs font-medium {{ $template->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                {{ $template->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $template->last_used_at?->diffForHumans() ?? 'Never' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.email-templates.edit', $template) }}" class="text-brand-600 hover:underline text-xs">Edit</a>
                            <a href="{{ route('admin.email-templates.preview', $template) }}" class="text-gray-500 hover:underline text-xs">Preview</a>
                            @if(!$template->is_system)
                            <form action="{{ route('admin.email-templates.destroy', $template) }}" method="POST" onsubmit="return confirm('Delete this template?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline text-xs">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">No email templates found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($emailTemplates->hasPages())
        <div class="p-4 border-t">{{ $emailTemplates->links() }}</div>
        @endif
    </div>
</div>
@endsection
