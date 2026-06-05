@extends('layouts.admin')
@section('title', 'Gallery')
@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Gallery</h2>
            <p class="text-sm text-gray-500 mt-1">Manage the photo gallery displayed on the website</p>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
        <ul>@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Upload Form --}}
    <div class="bg-white rounded-2xl border p-6 space-y-4">
        <h3 class="font-semibold text-gray-900 border-b pb-3">Upload Images</h3>
        <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title (optional)</label>
                    <input type="text" name="title" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <input type="text" name="category" list="gallery-cats" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500" placeholder="e.g. Campus, Training, Events">
                    <datalist id="gallery-cats">
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Caption</label>
                    <input type="text" name="caption" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Images <span class="text-red-500">*</span></label>
                <input type="file" name="images[]" multiple accept="image/*" required
                    class="block w-full border border-dashed border-gray-300 rounded-xl px-4 py-8 text-sm text-center cursor-pointer hover:border-brand-400 transition-colors">
                <p class="text-xs text-gray-400 mt-1">Select multiple images at once. Max 5MB each.</p>
            </div>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-brand-600">
                    <span class="text-sm text-gray-700">Active (visible on site)</span>
                </label>
                <button type="submit" class="bg-brand-600 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-brand-700 transition-colors">Upload</button>
            </div>
        </form>
    </div>

    {{-- Gallery Grid --}}
    @if($items->isEmpty())
    <div class="bg-white rounded-2xl border p-16 text-center text-gray-400">
        <p class="text-sm font-medium">No gallery images yet</p>
    </div>
    @else

    {{-- Category filter --}}
    @if($categories->isNotEmpty())
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.gallery.index') }}" class="px-3 py-1 text-xs font-semibold rounded-full {{ !request('cat') ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">All</a>
        @foreach($categories as $cat)
        <a href="{{ route('admin.gallery.index', ['cat' => $cat]) }}" class="px-3 py-1 text-xs font-semibold rounded-full {{ request('cat') === $cat ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">{{ $cat }}</a>
        @endforeach
    </div>
    @endif

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-3">
        @foreach($items as $item)
        @if(!request('cat') || request('cat') === $item->category)
        <div class="relative group rounded-xl overflow-hidden border bg-gray-50 aspect-square">
            <img src="{{ $item->image_url }}" alt="{{ $item->alt_text ?? $item->title ?? 'Gallery' }}"
                class="w-full h-full object-cover">

            @if(!$item->is_active)
            <div class="absolute inset-0 bg-gray-900/50 flex items-center justify-center">
                <span class="text-white text-xs font-semibold">Hidden</span>
            </div>
            @endif

            @if($item->category)
            <span class="absolute top-1 left-1 bg-black/60 text-white text-xs px-1.5 py-0.5 rounded">{{ $item->category }}</span>
            @endif

            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2 p-2">
                <form action="{{ route('admin.gallery.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete this image?')">
                    @csrf @method('DELETE')
                    <button class="bg-red-500 text-white text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-red-600 transition-colors">Delete</button>
                </form>
            </div>

            @if($item->caption || $item->title)
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2">
                <p class="text-white text-xs truncate">{{ $item->caption ?? $item->title }}</p>
            </div>
            @endif
        </div>
        @endif
        @endforeach
    </div>
    @endif
</div>
@endsection
