@extends('layouts.admin')
@section('title', 'Blog Categories')
@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Blog Categories</h1>
        <a href="{{ route('admin.blog.categories.create') }}"
            class="inline-flex items-center gap-2 bg-brand-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-brand-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Category
        </a>
    </div>

    @if(session('success'))<div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>@endif

    <div class="bg-white rounded-2xl border overflow-hidden">
        @if($categories->isEmpty())
        <div class="p-12 text-center text-gray-400"><p class="text-sm">No categories yet.</p></div>
        @else
        <table class="w-full text-sm">
            <thead class="text-xs text-gray-500 uppercase tracking-wide border-b bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Category</th>
                    <th class="px-4 py-3 text-center">Posts</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($categories as $cat)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            @if($cat->color)
                            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background:{{ $cat->color }}"></span>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-900">{{ $cat->name }}</p>
                                @if($cat->description)
                                <p class="text-xs text-gray-400">{{ Str::limit($cat->description, 60) }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-sm font-semibold text-gray-700">{{ $cat->posts_count }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($cat->is_active)
                        <span class="inline-flex px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">Active</span>
                        @else
                        <span class="inline-flex px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full text-xs font-medium">Inactive</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.blog.categories.edit', $cat) }}" class="text-xs text-gray-600 hover:underline">Edit</a>
                            <form action="{{ route('admin.blog.categories.destroy', $cat) }}" method="POST"
                                onsubmit="return confirm('Delete this category?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-500 hover:underline">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
