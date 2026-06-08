@extends('layouts.admin')
@section('title', 'Course Categories')
@section('content')
<div class="space-y-6" x-data="{ showForm: false }">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Course Categories</h2>
            <p class="text-sm text-gray-500 mt-1">Manage categories used to organise courses</p>
        </div>
        <button @click="showForm = !showForm"
            class="bg-brand-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-brand-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Category
        </button>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
        <ul>@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Add Form --}}
    <div x-show="showForm" x-cloak class="bg-white rounded-2xl border p-6 space-y-4">
        <h3 class="font-semibold text-gray-900 border-b pb-3">Add Category</h3>
        <form action="{{ route('admin.course-categories.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required maxlength="100"
                        class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500"
                        placeholder="e.g. Web Development">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500"
                        placeholder="Brief description of this category..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon</label>
                    <input type="text" name="icon" maxlength="50"
                        class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500"
                        placeholder="e.g. laptop, code, star">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="0" min="0"
                        class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
            </div>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-brand-600">
                    <span class="text-sm text-gray-700">Active</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" class="rounded text-brand-600">
                    <span class="text-sm text-gray-700">Featured</span>
                </label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="bg-brand-600 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-brand-700 transition-colors">
                    Add Category
                </button>
                <button type="button" @click="showForm = false"
                    class="px-5 py-2 border rounded-xl text-sm text-gray-600 hover:bg-gray-50">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    {{-- Categories List --}}
    @if($categories->isEmpty())
    <div class="bg-white rounded-2xl border p-16 text-center text-gray-400">
        <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        <p class="text-sm font-medium">No categories added yet</p>
        <p class="text-xs mt-1">Click "Add Category" to create your first one.</p>
    </div>
    @else
    <div class="bg-white rounded-2xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b text-left">
                    <th class="px-5 py-3 font-semibold text-gray-600">Name</th>
                    <th class="px-5 py-3 font-semibold text-gray-600 hidden sm:table-cell">Description</th>
                    <th class="px-5 py-3 font-semibold text-gray-600 text-center">Sort</th>
                    <th class="px-5 py-3 font-semibold text-gray-600 text-center">Active</th>
                    <th class="px-5 py-3 font-semibold text-gray-600 text-center">Featured</th>
                    <th class="px-5 py-3 font-semibold text-gray-600 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($categories as $category)
                <tr x-data="{ editing: false }" class="hover:bg-gray-50 transition-colors">

                    {{-- View row --}}
                    <td class="px-5 py-4 align-top" x-show="!editing">
                        <div class="font-medium text-gray-900">{{ $category->name }}</div>
                        @if($category->icon)
                        <div class="text-xs text-gray-400 mt-0.5">{{ $category->icon }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-4 align-top text-gray-500 hidden sm:table-cell" x-show="!editing">
                        {{ Str::limit($category->description, 80) }}
                    </td>
                    <td class="px-5 py-4 align-top text-center text-gray-600" x-show="!editing">
                        {{ $category->sort_order ?? 0 }}
                    </td>
                    <td class="px-5 py-4 align-top text-center" x-show="!editing">
                        @if($category->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Yes</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">No</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 align-top text-center" x-show="!editing">
                        @if($category->is_featured)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-brand-100 text-brand-700">Yes</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">No</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 align-top text-right" x-show="!editing">
                        <div class="flex items-center justify-end gap-2">
                            <button @click="editing = true"
                                class="text-xs font-semibold text-gray-500 border rounded-lg px-3 py-1.5 hover:bg-gray-50 transition-colors">
                                Edit
                            </button>
                            <form action="{{ route('admin.course-categories.destroy', $category) }}" method="POST"
                                onsubmit="return confirm('Delete this category?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-xs font-semibold text-red-500 border border-red-200 rounded-lg px-3 py-1.5 hover:bg-red-50 transition-colors">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>

                    {{-- Edit row (spans full width) --}}
                    <td colspan="6" x-show="editing" x-cloak class="px-5 py-4">
                        <form action="{{ route('admin.course-categories.update', $category) }}" method="POST" class="space-y-3">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="col-span-2 sm:col-span-1">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" value="{{ $category->name }}" required maxlength="100"
                                        class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Icon</label>
                                    <input type="text" name="icon" value="{{ $category->icon }}" maxlength="50"
                                        class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500"
                                        placeholder="e.g. laptop, code, star">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                                    <textarea name="description" rows="2"
                                        class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">{{ $category->description }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Sort Order</label>
                                    <input type="number" name="sort_order" value="{{ $category->sort_order ?? 0 }}" min="0"
                                        class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <label class="flex items-center gap-2 cursor-pointer text-sm">
                                    <input type="checkbox" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }} class="rounded text-brand-600">
                                    Active
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer text-sm">
                                    <input type="checkbox" name="is_featured" value="1" {{ $category->is_featured ? 'checked' : '' }} class="rounded text-brand-600">
                                    Featured
                                </label>
                            </div>
                            <div class="flex gap-2 pt-1">
                                <button type="submit"
                                    class="bg-brand-600 text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-brand-700 transition-colors">
                                    Save
                                </button>
                                <button type="button" @click="editing = false"
                                    class="border text-sm font-semibold px-4 py-2 rounded-xl hover:bg-gray-50 transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection
