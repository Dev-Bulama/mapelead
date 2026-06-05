@extends('layouts.admin')
@section('title', 'Edit Lesson')
@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a href="{{ route('admin.modules.lessons.index', $lesson->module_id) }}" class="hover:text-brand-600">Lessons</a>
            <span>/</span><span>Edit</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Edit Lesson</h1>
    </div>

    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
        <ul class="space-y-1">@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form action="{{ route('admin.lessons.update', $lesson) }}" method="POST" class="bg-white rounded-2xl border p-6 space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Lesson Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $lesson->title) }}" required
                class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                    @foreach(['video'=>'Video','text'=>'Text/Reading','quiz'=>'Quiz','assignment'=>'Assignment','live'=>'Live Session'] as $val => $label)
                    <option value="{{ $val }}" {{ old('type', $lesson->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $lesson->duration_minutes) }}" min="1"
                    class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Video URL</label>
            <input type="url" name="video_url" value="{{ old('video_url', $lesson->video_url) }}"
                class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Video Provider</label>
            <select name="video_provider" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                <option value="">— None —</option>
                @foreach(['youtube'=>'YouTube','vimeo'=>'Vimeo','wistia'=>'Wistia','bunny'=>'Bunny.net','other'=>'Other'] as $val => $label)
                <option value="{{ $val }}" {{ old('video_provider', $lesson->video_provider) === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $lesson->sort_order) }}" min="0"
                class="w-32 border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Content / Notes</label>
            <textarea name="content" rows="5"
                class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">{{ old('content', $lesson->content) }}</textarea>
        </div>
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_free_preview" value="1" id="free_preview"
                    {{ old('is_free_preview', $lesson->is_free_preview) ? 'checked' : '' }} class="rounded text-brand-600">
                <label for="free_preview" class="text-sm text-gray-700">Free Preview</label>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_published" value="1" id="is_published"
                    {{ old('is_published', $lesson->is_published) ? 'checked' : '' }} class="rounded text-brand-600">
                <label for="is_published" class="text-sm text-gray-700">Published</label>
            </div>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-brand-600 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-brand-700 transition-colors">Save Changes</button>
            <a href="{{ route('admin.modules.lessons.index', $lesson->module_id) }}" class="px-5 py-2 border rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-colors">Cancel</a>
        </div>
    </form>
</div>
@endsection
