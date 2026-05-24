@extends('layouts.admin')
@section('title', 'New Blog Post')
@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <h1 class="text-2xl font-bold text-gray-900">New Blog Post</h1>

    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
        <ul class="space-y-1">@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form action="{{ route('admin.blog.posts.store') }}" method="POST" class="space-y-5">
        @csrf
        <div class="bg-white rounded-2xl border p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Excerpt</label>
                <textarea name="excerpt" rows="2"
                    class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500"
                    placeholder="Short summary shown in post listings...">{{ old('excerpt') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Content <span class="text-red-500">*</span></label>
                <textarea name="content" rows="14" required
                    class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 font-mono">{{ old('content') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">HTML is supported.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border p-6 space-y-4">
            <h3 class="font-semibold text-gray-900">Publish Settings</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                        <option value="draft" {{ old('status','draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                        <option value="">— None —</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Publish Date</label>
                    <input type="datetime-local" name="published_at" value="{{ old('published_at') }}"
                        class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
            </div>
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_featured" value="1" id="is_featured"
                        {{ old('is_featured') ? 'checked' : '' }} class="rounded text-brand-600">
                    <label for="is_featured" class="text-sm text-gray-700">Featured post</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="allow_comments" value="1" id="allow_comments"
                        {{ old('allow_comments', true) ? 'checked' : '' }} class="rounded text-brand-600">
                    <label for="allow_comments" class="text-sm text-gray-700">Allow comments</label>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-brand-600 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-brand-700 transition-colors">Create Post</button>
            <a href="{{ route('admin.blog.posts.index') }}" class="px-5 py-2 border rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-colors">Cancel</a>
        </div>
    </form>
</div>
@endsection
