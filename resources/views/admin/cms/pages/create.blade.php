@extends('layouts.admin')
@section('title', 'Create Page')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Page</h1>
        </div>
        <a href="{{ route('admin.cms.pages.index') }}"
           class="text-sm text-gray-600 bg-white border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
            &larr; Back
        </a>
    </div>

    <form action="{{ route('admin.cms.pages.store') }}" method="POST">
        @csrf
        <div class="bg-white rounded-xl border p-5 space-y-4">

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
                       placeholder="Page title">
                @error('title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Slug <span class="text-gray-400 font-normal">(leave blank to auto-generate)</span></label>
                <input type="text" name="slug" value="{{ old('slug') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2"
                       placeholder="my-page-slug">
                @error('slug')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Content</label>
                <textarea name="content" rows="10"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
                          placeholder="Page content (HTML supported)...">{{ old('content') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
                    <option value="draft" @selected(old('status') === 'draft')>Draft</option>
                    <option value="published" @selected(old('status') === 'published')>Published</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none"
                           placeholder="SEO title">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Meta Description</label>
                    <input type="text" name="meta_description" value="{{ old('meta_description') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none"
                           placeholder="SEO description">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-white hover:opacity-90 transition"
                        style="background-color:#14215B;">
                    Create Page
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
