@extends('layouts.admin')
@section('title', 'New Blog Category')
@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <h1 class="text-2xl font-bold text-gray-900">New Blog Category</h1>

    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
        <ul class="space-y-1">@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form action="{{ route('admin.blog.categories.store') }}" method="POST" class="bg-white rounded-2xl border p-6 space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Color (hex)</label>
                <input type="text" name="color" value="{{ old('color', '#14215B') }}"
                    class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500"
                    placeholder="#14215B">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                    class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2"
                    class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">{{ old('description') }}</textarea>
            </div>
            @if($parents->isNotEmpty())
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Category</label>
                <select name="parent_id" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                    <option value="">None (top-level)</option>
                    @foreach($parents as $p)
                    <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-span-2 flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" checked class="rounded text-brand-600">
                <label for="is_active" class="text-sm text-gray-700">Active</label>
            </div>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-brand-600 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-brand-700">Create</button>
            <a href="{{ route('admin.blog.categories.index') }}" class="px-5 py-2 border rounded-xl text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>
@endsection
