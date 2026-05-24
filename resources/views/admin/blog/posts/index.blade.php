@extends('layouts.admin')
@section('title', 'Blog Posts')
@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Blog Posts</h1>
        <a href="{{ route('admin.blog.posts.create') }}"
            class="inline-flex items-center gap-2 bg-brand-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-brand-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Post
        </a>
    </div>

    @if(session('success'))<div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>@endif

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-xl border p-4 flex flex-wrap items-center gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
            class="border rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-brand-500 flex-1 min-w-40"
            placeholder="Search posts...">
        <select name="category_id" class="border rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-brand-500">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <select name="status" class="border rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-brand-500">
            <option value="">All Statuses</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
        </select>
        <button type="submit" class="bg-brand-600 text-white px-4 py-1.5 rounded-lg text-sm font-semibold hover:bg-brand-700">Filter</button>
        @if(request()->hasAny(['search','category_id','status']))
        <a href="{{ route('admin.blog.posts.index') }}" class="text-sm text-gray-500 hover:underline">Clear</a>
        @endif
    </form>

    <div class="bg-white rounded-2xl border overflow-hidden">
        @if($posts->isEmpty())
        <div class="p-12 text-center text-gray-400">
            <p class="text-sm font-medium">No posts yet.</p>
        </div>
        @else
        <table class="w-full text-sm">
            <thead class="text-xs text-gray-500 uppercase tracking-wide border-b bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Post</th>
                    <th class="px-4 py-3 text-left">Author</th>
                    <th class="px-4 py-3 text-center">Category</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Views</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($posts as $post)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-900 max-w-xs truncate">{{ $post->title }}</p>
                        @if($post->published_at)
                        <p class="text-xs text-gray-400">{{ $post->published_at->format('M d, Y') }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $post->author?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs text-gray-500">{{ $post->category?->name ?? '—' }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @php $statusColors = ['published'=>'green','draft'=>'yellow','scheduled'=>'blue']; @endphp
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                            bg-{{ $statusColors[$post->status] ?? 'gray' }}-100 text-{{ $statusColors[$post->status] ?? 'gray' }}-700">
                            {{ ucfirst($post->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-gray-500">{{ number_format($post->views) }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            @if($post->status !== 'published')
                            <form action="{{ route('admin.blog.posts.publish', $post->id) }}" method="POST">
                                @csrf
                                <button class="text-xs text-green-600 hover:underline">Publish</button>
                            </form>
                            @endif
                            <a href="{{ route('admin.blog.posts.edit', $post) }}" class="text-xs text-gray-600 hover:underline">Edit</a>
                            <form action="{{ route('admin.blog.posts.destroy', $post) }}" method="POST"
                                onsubmit="return confirm('Delete this post?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-500 hover:underline">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t">{{ $posts->links() }}</div>
        @endif
    </div>
</div>
@endsection
