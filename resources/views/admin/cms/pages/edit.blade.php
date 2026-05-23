@extends('layouts.admin')
@section('title', 'Edit Page — ' . $page->title)

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Page</h1>
            <p class="text-sm text-gray-500 mt-0.5 font-mono">/{{ $page->slug }}</p>
        </div>
        <a href="{{ route('admin.cms.pages.index') }}"
           class="text-sm text-gray-600 bg-white border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
            &larr; Back
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm p-4">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.cms.pages.update', $page) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-xl border p-5 space-y-4">

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $page->title) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2">
                @error('title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $page->slug) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2">
                @error('slug')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Content</label>
                <textarea name="content" rows="10"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2">{{ old('content', $page->content) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
                    <option value="draft" @selected(old('status', $page->status) === 'draft')>Draft</option>
                    <option value="published" @selected(old('status', $page->status) === 'published')>Published</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Meta Description</label>
                    <input type="text" name="meta_description" value="{{ old('meta_description', $page->meta_description) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-white hover:opacity-90 transition"
                        style="background-color:#14215B;">
                    Save Changes
                </button>
                <a href="{{ route('admin.cms.pages.index') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                    Cancel
                </a>
            </div>
        </div>
    </form>

</div>
@endsection
