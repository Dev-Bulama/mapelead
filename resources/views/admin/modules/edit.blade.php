@extends('layouts.admin')
@section('title', 'Edit Module')
@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a href="{{ route('admin.courses.modules.index', $module->course_id) }}" class="hover:text-brand-600">Modules</a>
            <span>/</span><span>Edit</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Edit Module</h1>
    </div>

    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
        <ul class="space-y-1">@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form action="{{ route('admin.courses.modules.update', [$module->course_id, $module]) }}" method="POST" class="bg-white rounded-2xl border p-6 space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Module Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $module->title) }}" required
                class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3"
                class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">{{ old('description', $module->description) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $module->sort_order) }}" min="0"
                class="w-32 border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
        </div>
        <div class="flex items-center gap-3">
            <input type="checkbox" name="is_free_preview" value="1" id="is_free_preview"
                {{ old('is_free_preview', $module->is_free_preview) ? 'checked' : '' }} class="rounded text-brand-600">
            <label for="is_free_preview" class="text-sm text-gray-700">Free preview</label>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-brand-600 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-brand-700 transition-colors">Save Changes</button>
            <a href="{{ route('admin.courses.modules.index', $module->course_id) }}" class="px-5 py-2 border rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-colors">Cancel</a>
        </div>
    </form>
</div>
@endsection
