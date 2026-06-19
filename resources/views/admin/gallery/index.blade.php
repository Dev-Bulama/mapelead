@extends('layouts.admin')
@section('title', 'Gallery')

@section('content')
<div class="space-y-6" x-data="adminGallery()">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Gallery</h2>
            <p class="text-sm text-gray-500 mt-1">Manage photos displayed on the website. <a href="{{ route('gallery') }}" target="_blank" class="text-brand-600 hover:underline">View public gallery →</a></p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-500">{{ $items->count() }} photo{{ $items->count() !== 1 ? 's' : '' }}</span>
            <button @click="showUpload = !showUpload"
                    class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Upload Photos
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm flex items-center gap-2">
        <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
        <ul>@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Upload Panel --}}
    <div x-show="showUpload" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-5">
            <h3 class="font-semibold text-gray-900 text-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Upload New Photos
            </h3>

            <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                {{-- Drop zone --}}
                <div x-data="{ dragging: false, files: [] }"
                     @dragenter.prevent="dragging = true"
                     @dragleave.prevent="dragging = false"
                     @dragover.prevent
                     @drop.prevent="dragging = false; files = Array.from($event.dataTransfer.files); $refs.fileInput.files = $event.dataTransfer.files"
                     :class="dragging ? 'border-brand-500 bg-brand-50' : 'border-gray-300 bg-gray-50 hover:border-brand-400 hover:bg-brand-50'"
                     class="border-2 border-dashed rounded-2xl p-8 text-center cursor-pointer transition-all duration-200"
                     @click="$refs.fileInput.click()">
                    <input type="file" name="images[]" multiple accept="image/*" required
                           x-ref="fileInput"
                           @change="files = Array.from($event.target.files)"
                           class="hidden">
                    <template x-if="files.length === 0">
                        <div>
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <p class="text-gray-700 font-semibold mb-1">Drop photos here or click to browse</p>
                            <p class="text-gray-400 text-sm">JPEG, PNG, WebP · Max 5 MB each · Multiple files supported</p>
                        </div>
                    </template>
                    <template x-if="files.length > 0">
                        <div>
                            <svg class="w-10 h-10 mx-auto text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-gray-800 font-semibold" x-text="files.length + ' file' + (files.length > 1 ? 's' : '') + ' selected'"></p>
                            <p class="text-gray-400 text-xs mt-1">Click again to change selection</p>
                        </div>
                    </template>
                </div>

                {{-- Meta fields --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Title <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input type="text" name="title" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none" placeholder="e.g. Graduation Ceremony">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Category</label>
                        <input type="text" name="category" list="upload-cats"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none"
                               placeholder="e.g. Campus, Events, Training">
                        <datalist id="upload-cats">
                            @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Caption</label>
                        <input type="text" name="caption" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none" placeholder="Short description">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                        <span class="text-sm text-gray-700">Publish immediately (visible on site)</span>
                    </label>
                    <div class="flex gap-3">
                        <button type="button" @click="showUpload = false" class="text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">Cancel</button>
                        <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors">
                            Upload Photos
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Category Filters --}}
    @if($categories->isNotEmpty())
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.gallery.index') }}"
           class="px-3 py-1 text-xs font-semibold rounded-full transition-colors {{ !request('cat') ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            All ({{ $items->count() }})
        </a>
        @foreach($categories as $cat)
        <a href="{{ route('admin.gallery.index', ['cat' => $cat]) }}"
           class="px-3 py-1 text-xs font-semibold rounded-full transition-colors {{ request('cat') === $cat ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            {{ $cat }}
        </a>
        @endforeach
    </div>
    @endif

    {{-- Gallery Grid --}}
    @if($items->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center text-gray-400">
        <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="text-sm font-medium text-gray-500">No photos yet. Upload some above.</p>
    </div>
    @else

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-0 divide-x divide-y divide-gray-100">
            @foreach($items as $item)
            <div class="relative group bg-gray-50 aspect-square overflow-hidden">
                <img src="{{ $item->image_url }}"
                     alt="{{ $item->alt_text ?? $item->title ?? 'Gallery' }}"
                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                     loading="lazy">

                {{-- Hidden overlay --}}
                @if(!$item->is_active)
                <div class="absolute inset-0 bg-gray-900/60 flex items-center justify-center">
                    <span class="bg-gray-700 text-white text-xs font-semibold px-2 py-1 rounded-lg">Hidden</span>
                </div>
                @endif

                {{-- Category badge --}}
                @if($item->category)
                <span class="absolute top-1.5 left-1.5 bg-black/60 text-white text-[10px] px-1.5 py-0.5 rounded font-medium">
                    {{ $item->category }}
                </span>
                @endif

                {{-- Action overlay --}}
                <div class="absolute inset-0 bg-black/55 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2 p-2">
                    {{-- Edit button --}}
                    <button @click="openEdit({{ $item->id }}, '{{ addslashes($item->title ?? '') }}', '{{ addslashes($item->category ?? '') }}', '{{ addslashes($item->caption ?? '') }}', {{ $item->is_active ? 'true' : 'false' }}, {{ $item->sort_order ?? 0 }})"
                            class="w-full bg-white/90 hover:bg-white text-gray-900 text-xs font-semibold py-1.5 rounded-lg transition-colors flex items-center justify-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </button>

                    {{-- Toggle visibility --}}
                    <form action="{{ route('admin.gallery.update', $item) }}" method="POST" class="w-full">
                        @csrf @method('PUT')
                        <input type="hidden" name="is_active" value="{{ $item->is_active ? '0' : '1' }}">
                        <button type="submit"
                                class="w-full {{ $item->is_active ? 'bg-yellow-400/90 hover:bg-yellow-400 text-yellow-900' : 'bg-green-500/90 hover:bg-green-500 text-white' }} text-xs font-semibold py-1.5 rounded-lg transition-colors flex items-center justify-center gap-1">
                            @if($item->is_active)
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            Hide
                            @else
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Show
                            @endif
                        </button>
                    </form>

                    {{-- Delete --}}
                    <form action="{{ route('admin.gallery.destroy', $item) }}" method="POST" class="w-full" onsubmit="return confirm('Permanently delete this photo?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="w-full bg-red-500/90 hover:bg-red-500 text-white text-xs font-semibold py-1.5 rounded-lg transition-colors flex items-center justify-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Edit Modal --}}
    <div x-show="editOpen" x-cloak
         x-transition:enter="transition duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4"
         @click.self="editOpen = false"
         @keydown.escape.window="editOpen = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6"
             x-transition:enter="transition duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             @click.stop>
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-gray-900 text-lg">Edit Photo</h3>
                <button @click="editOpen = false" class="text-gray-400 hover:text-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form :action="'/admin/gallery/' + editId" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Title</label>
                    <input type="text" name="title" :value="editTitle"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Category</label>
                    <input type="text" name="category" :value="editCategory" list="edit-cats"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    <datalist id="edit-cats">
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Caption</label>
                    <input type="text" name="caption" :value="editCaption"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Sort Order</label>
                    <input type="number" name="sort_order" :value="editSortOrder" min="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    <p class="text-xs text-gray-400 mt-1">Lower numbers appear first.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Replace Image <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="file" name="image" accept="image/*"
                           class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                </div>
                <div class="flex items-center gap-3 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" :checked="editActive"
                               class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                        <span class="text-sm text-gray-700">Visible on site</span>
                    </label>
                </div>
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="editOpen = false"
                            class="text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors px-4 py-2">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function adminGallery() {
    return {
        showUpload: false,
        editOpen: false,
        editId: null,
        editTitle: '',
        editCategory: '',
        editCaption: '',
        editActive: true,
        editSortOrder: 0,
        openEdit(id, title, category, caption, active, sortOrder) {
            this.editId = id;
            this.editTitle = title;
            this.editCategory = category;
            this.editCaption = caption;
            this.editActive = active;
            this.editSortOrder = sortOrder;
            this.editOpen = true;
        }
    }
}
</script>
@endpush
@endsection
