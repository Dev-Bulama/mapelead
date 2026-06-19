@extends('layouts.app')
@section('title', 'Gallery — ' . \App\Models\SiteSetting::get('site_name', 'MapeLearn'))
@section('meta_description', 'Explore our gallery — campus life, training sessions, graduation ceremonies, and the vibrant MapeLearn community.')

@push('styles')
<style>
.masonry-grid { columns: 2; column-gap: 0.75rem; }
@media(min-width:640px) { .masonry-grid { columns: 3; } }
@media(min-width:768px) { .masonry-grid { columns: 4; } }
@media(min-width:1024px){ .masonry-grid { columns: 5; } }
.masonry-item { break-inside: avoid; margin-bottom: 0.75rem; display: block; }
.gallery-img { opacity: 0; transition: opacity 0.4s ease; }
.gallery-img.loaded { opacity: 1; }
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="hero-gradient text-white py-16">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <span class="inline-block bg-white/10 text-white/80 text-sm font-medium px-4 py-1.5 rounded-full mb-4">Our World</span>
        <h1 class="text-4xl lg:text-5xl font-display font-bold mb-4">Life at MapeLearn</h1>
        <p class="text-brand-200 text-lg">Glimpses of our campus, training sessions, graduation ceremonies, and the vibrant community you'll be part of.</p>
    </div>
</div>

{{-- Category Filters --}}
@if($categories->isNotEmpty())
<div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 overflow-x-auto py-3 scrollbar-hide">
            <a href="{{ route('gallery') }}"
               class="shrink-0 px-4 py-1.5 rounded-full text-sm font-semibold transition-colors {{ !$currentCategory ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                All Photos
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('gallery', ['category' => $cat]) }}"
               class="shrink-0 px-4 py-1.5 rounded-full text-sm font-semibold transition-colors {{ $currentCategory === $cat ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                {{ $cat }}
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Gallery Grid + Lightbox --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12"
     x-data="galleryPage(@json($items->map(fn($i) => ['src' => $i->image_url, 'caption' => $i->caption ?? $i->title ?? '', 'category' => $i->category ?? ''])->values()))">

    @if($items->isEmpty())
    <div class="py-24 text-center text-gray-400">
        <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="text-lg font-medium text-gray-500">No photos in this category yet</p>
        @if($currentCategory)
        <a href="{{ route('gallery') }}" class="mt-4 inline-block text-brand-600 hover:underline text-sm font-medium">← View all photos</a>
        @endif
    </div>
    @else

    {{-- Stats bar --}}
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">
            Showing <span class="font-semibold text-gray-700">{{ $items->firstItem() }}–{{ $items->lastItem() }}</span>
            of <span class="font-semibold text-gray-700">{{ $items->total() }}</span> photos
            @if($currentCategory)<span> in <span class="text-brand-600 font-semibold">{{ $currentCategory }}</span></span>@endif
        </p>
        <p class="text-xs text-gray-400">Click any photo to view full size</p>
    </div>

    {{-- Masonry Grid --}}
    <div class="masonry-grid">
        @foreach($items as $index => $item)
        <div class="masonry-item">
            <button @click="openLightbox({{ $index }}, '{{ addslashes($item->image_url) }}', '{{ addslashes($item->caption ?? $item->title ?? '') }}')"
                    class="group relative block w-full overflow-hidden rounded-xl bg-gray-100 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                <img src="{{ $item->image_url }}"
                     alt="{{ $item->alt_text ?? $item->caption ?? $item->title ?? 'Gallery photo' }}"
                     loading="lazy"
                     class="gallery-img w-full h-auto object-cover group-hover:scale-105 transition-transform duration-500 rounded-xl"
                     onload="this.classList.add('loaded')">

                {{-- Hover overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl">
                    <div class="absolute bottom-0 left-0 right-0 p-3">
                        @if($item->caption || $item->title)
                        <p class="text-white text-xs font-medium line-clamp-2">{{ $item->caption ?? $item->title }}</p>
                        @endif
                        @if($item->category)
                        <span class="inline-block mt-1 text-xs bg-brand-500 text-white px-2 py-0.5 rounded-full">{{ $item->category }}</span>
                        @endif
                    </div>
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                        <div class="w-10 h-10 bg-white/90 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </button>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($items->hasPages())
    <div class="mt-12 flex justify-center">
        <div class="flex items-center gap-2">
            @if($items->onFirstPage())
            <span class="w-10 h-10 rounded-xl bg-gray-100 text-gray-400 flex items-center justify-center cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </span>
            @else
            <a href="{{ $items->previousPageUrl() }}" class="w-10 h-10 rounded-xl bg-white border border-gray-200 text-gray-600 flex items-center justify-center hover:bg-brand-50 hover:border-brand-300 hover:text-brand-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            @endif

            @foreach($items->getUrlRange(max(1, $items->currentPage()-2), min($items->lastPage(), $items->currentPage()+2)) as $page => $url)
            <a href="{{ $url }}" class="w-10 h-10 rounded-xl text-sm font-semibold flex items-center justify-center transition-colors {{ $page == $items->currentPage() ? 'bg-brand-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-brand-50 hover:border-brand-300 hover:text-brand-700' }}">
                {{ $page }}
            </a>
            @endforeach

            @if($items->hasMorePages())
            <a href="{{ $items->nextPageUrl() }}" class="w-10 h-10 rounded-xl bg-white border border-gray-200 text-gray-600 flex items-center justify-center hover:bg-brand-50 hover:border-brand-300 hover:text-brand-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @else
            <span class="w-10 h-10 rounded-xl bg-gray-100 text-gray-400 flex items-center justify-center cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
            @endif
        </div>
    </div>
    @endif
    @endif

    {{-- Lightbox --}}
    <div x-show="lightboxOpen" x-cloak
         x-transition:enter="transition duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black/92 flex items-center justify-center p-4"
         @click.self="lightboxOpen = false"
         @keydown.escape.window="lightboxOpen = false"
         @keydown.arrow-left.window="prevPhoto()"
         @keydown.arrow-right.window="nextPhoto()">

        {{-- Close --}}
        <button @click="lightboxOpen = false"
                class="absolute top-4 right-4 z-10 w-10 h-10 bg-white/10 hover:bg-white/25 text-white rounded-xl flex items-center justify-center transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        {{-- Prev --}}
        <button @click="prevPhoto()"
                class="absolute left-3 top-1/2 -translate-y-1/2 z-10 w-11 h-11 bg-white/10 hover:bg-white/25 text-white rounded-xl flex items-center justify-center transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>

        {{-- Next --}}
        <button @click="nextPhoto()"
                class="absolute right-3 top-1/2 -translate-y-1/2 z-10 w-11 h-11 bg-white/10 hover:bg-white/25 text-white rounded-xl flex items-center justify-center transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>

        {{-- Image --}}
        <div class="max-w-5xl w-full mx-auto">
            <img :src="currentSrc" :alt="currentCaption"
                 class="max-h-[82vh] w-full object-contain rounded-xl shadow-2xl"
                 x-transition:enter="transition duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center justify-between mt-3 px-1">
                <p x-show="currentCaption" x-text="currentCaption" class="text-white/70 text-sm"></p>
                <p class="text-white/40 text-xs ml-auto"><span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span></p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function galleryPage(images) {
    return {
        images: images,
        lightboxOpen: false,
        currentIndex: 0,
        currentSrc: '',
        currentCaption: '',
        openLightbox(index, src, caption) {
            this.currentIndex = index;
            this.currentSrc = src;
            this.currentCaption = caption;
            this.lightboxOpen = true;
        },
        prevPhoto() {
            this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
            this.currentSrc = this.images[this.currentIndex].src;
            this.currentCaption = this.images[this.currentIndex].caption;
        },
        nextPhoto() {
            this.currentIndex = (this.currentIndex + 1) % this.images.length;
            this.currentSrc = this.images[this.currentIndex].src;
            this.currentCaption = this.images[this.currentIndex].caption;
        }
    }
}
</script>
@endpush

@endsection
