@extends('layouts.admin')
@section('title', 'Services')
@section('content')
<div class="space-y-6" x-data="{ showForm: false }">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Services</h2>
            <p class="text-sm text-gray-500 mt-1">Manage the services section displayed on the homepage</p>
        </div>
        <button @click="showForm = !showForm"
            class="bg-brand-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-brand-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Service
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
        <h3 class="font-semibold text-gray-900 border-b pb-3">Add Service</h3>
        <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="e.g. Professional Certification">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="3" required class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="Describe what this service offers..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon (SVG path keyword or emoji)</label>
                    <input type="text" name="icon" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="e.g. shield-check, star, 🎓">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <input type="color" name="color" value="#14215B" class="h-10 w-full border rounded-xl px-2 py-1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full border rounded-xl px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Link URL</label>
                    <input type="url" name="link_url" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="https://...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Link Text</label>
                    <input type="text" name="link_text" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="Learn More">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="0" min="0" class="w-full border rounded-xl px-3 py-2 text-sm">
                </div>
            </div>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" checked class="rounded text-brand-600">
                    <span class="text-sm text-gray-700">Featured</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-brand-600">
                    <span class="text-sm text-gray-700">Active</span>
                </label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-brand-600 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-brand-700 transition-colors">Add Service</button>
                <button type="button" @click="showForm = false" class="px-5 py-2 border rounded-xl text-sm text-gray-600 hover:bg-gray-50">Cancel</button>
            </div>
        </form>
    </div>

    {{-- Services List --}}
    @if($services->isEmpty())
    <div class="bg-white rounded-2xl border p-16 text-center text-gray-400">
        <p class="text-sm font-medium">No services added yet</p>
    </div>
    @else
    <div class="space-y-3">
        @foreach($services as $service)
        <div class="bg-white rounded-2xl border p-5 flex items-start gap-4" x-data="{ editing: false }">
            <div class="w-12 h-12 rounded-xl flex-shrink-0 flex items-center justify-center text-white text-xl font-bold"
                style="background-color: {{ $service->color ?? '#14215B' }}">
                @if($service->image_url)
                <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="w-10 h-10 object-cover rounded-lg">
                @else
                {{ mb_substr($service->title, 0, 1) }}
                @endif
            </div>
            <div class="flex-1">
                <div x-show="!editing">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="font-semibold text-gray-900">{{ $service->title }}</h3>
                        @if(!$service->is_active)
                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Inactive</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500">{{ Str::limit($service->description, 120) }}</p>
                </div>
                <div x-show="editing" x-cloak class="space-y-3">
                    <form action="{{ route('admin.services.update', $service) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf @method('PUT')
                        <input type="text" name="title" value="{{ $service->title }}" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="Title">
                        <textarea name="description" rows="2" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">{{ $service->description }}</textarea>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Icon (keyword or emoji)</label>
                                <input type="text" name="icon" value="{{ $service->icon }}" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="e.g. shield-check, 🎓">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Color</label>
                                <input type="color" name="color" value="{{ $service->color ?? '#14215B' }}" class="h-10 w-full border rounded-xl px-1 py-0.5">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Image</label>
                            <input type="file" name="image" accept="image/*" class="w-full border rounded-xl px-3 py-2 text-sm file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-medium file:bg-brand-50 file:text-brand-700">
                            @if($service->image)
                            <p class="text-xs text-gray-400 mt-1">Current: <span class="font-medium text-gray-600">{{ basename($service->image) }}</span> — upload a new file to replace it.</p>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <input type="url" name="link_url" value="{{ $service->link_url }}" class="border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="Link URL">
                            <input type="text" name="link_text" value="{{ $service->link_text }}" class="border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="Link Text">
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-1.5 text-sm"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" {{ $service->is_active ? 'checked' : '' }} class="rounded text-brand-600"> Active</label>
                            <label class="flex items-center gap-1.5 text-sm"><input type="hidden" name="is_featured" value="0"><input type="checkbox" name="is_featured" value="1" {{ $service->is_featured ? 'checked' : '' }} class="rounded text-brand-600"> Featured</label>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-brand-600 text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-brand-700">Save</button>
                            <button type="button" @click="editing = false" class="border text-sm font-semibold px-4 py-2 rounded-xl hover:bg-gray-50">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
            <div x-show="!editing" class="flex gap-2 flex-shrink-0">
                <button @click="editing = true" class="text-xs font-semibold text-gray-500 border rounded-lg px-3 py-1.5 hover:bg-gray-50 transition-colors">Edit</button>
                <form action="{{ route('admin.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Delete this service?')">
                    @csrf @method('DELETE')
                    <button class="text-xs font-semibold text-red-500 border border-red-200 rounded-lg px-3 py-1.5 hover:bg-red-50 transition-colors">Delete</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
