@extends('layouts.app')

@section('title', isset($category) ? $category->name . ' Courses — MapeLearn' : 'All Courses — MapeLearn')
@section('meta_description', isset($category) ? 'Browse ' . $category->name . ' courses on MapeLearn.' : 'Explore all courses on MapeLearn. Filter by category, level, type and price.')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════
     PAGE HERO
═══════════════════════════════════════════════════════════════ --}}
<section class="bg-gradient-to-br from-brand-950 via-brand-900 to-brand-800 py-14 relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none opacity-10"
         style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-brand-300 mb-5">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
            <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            @if(isset($category))
                <a href="{{ route('courses.index') }}" class="hover:text-white transition-colors">Courses</a>
                <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-white">{{ $category->name }}</span>
            @else
                <span class="text-white">All Courses</span>
            @endif
        </nav>

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div>
                @if(isset($category) && $category->icon)
                <div class="w-14 h-14 bg-brand-700 rounded-2xl flex items-center justify-center mb-4">
                    <img src="{{ asset('storage/' . $category->icon) }}" alt="" class="w-8 h-8 object-contain">
                </div>
                @endif
                <h1 class="font-display text-3xl sm:text-4xl font-extrabold text-white">
                    {{ isset($category) ? $category->name . ' Courses' : 'All Courses' }}
                </h1>
                @if(isset($category) && $category->description)
                <p class="text-brand-200 mt-2 max-w-xl">{{ $category->description }}</p>
                @else
                <p class="text-brand-200 mt-2 max-w-xl">Explore our full library of expert-led courses. Find the perfect program to advance your career.</p>
                @endif
                <div class="flex items-center gap-2 mt-4 text-brand-300 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span><strong class="text-white">{{ $courses->total() }}</strong> {{ Str::plural('course', $courses->total()) }} available</span>
                </div>
            </div>

            {{-- Search bar --}}
            <form method="GET" action="{{ route('courses.index') }}" class="w-full lg:w-96">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search courses..."
                           class="w-full pl-11 pr-4 py-3.5 rounded-2xl bg-white bg-opacity-10 border border-white border-opacity-20 text-white placeholder-brand-300 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-30 focus:bg-opacity-20 transition-all text-sm backdrop-blur-sm">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    @foreach(request()->except(['search', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </div>
            </form>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     MAIN CONTENT
═══════════════════════════════════════════════════════════════ --}}
<section class="py-10 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8" x-data="{ filtersOpen: false }">

            {{-- ─── SIDEBAR ─────────────────────────────────────────── --}}
            {{-- Mobile filter toggle --}}
            <div class="lg:hidden flex items-center justify-between bg-white border border-gray-200 rounded-2xl px-5 py-3">
                <span class="font-semibold text-gray-800 text-sm">Filters
                    @if(request()->hasAny(['category', 'type', 'level', 'price_min', 'price_max']))
                    <span class="ml-2 inline-flex items-center justify-center w-5 h-5 bg-brand-600 text-white text-xs rounded-full">!</span>
                    @endif
                </span>
                <button @click="filtersOpen = !filtersOpen" class="flex items-center gap-2 text-brand-600 font-medium text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span x-text="filtersOpen ? 'Hide' : 'Show Filters'"></span>
                </button>
            </div>

            <aside class="lg:w-72 shrink-0" :class="{ 'hidden lg:block': !filtersOpen, 'block': filtersOpen }">
                <form method="GET" action="{{ route('courses.index') }}" id="filter-form">
                    {{-- Preserve search --}}
                    @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-7">
                        {{-- Header --}}
                        <div class="flex items-center justify-between">
                            <h3 class="font-display font-bold text-gray-900">Filter Courses</h3>
                            @if(request()->hasAny(['category', 'type', 'level', 'price_min', 'price_max']))
                            <a href="{{ route('courses.index', array_filter(['search' => request('search'), 'sort' => request('sort')])) }}"
                               class="text-xs text-red-500 hover:text-red-600 font-medium flex items-center gap-1 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Clear All
                            </a>
                            @endif
                        </div>

                        {{-- Categories Filter --}}
                        <div x-data="{ open: true }">
                            <button type="button" @click="open = !open" class="flex items-center justify-between w-full mb-3">
                                <span class="font-semibold text-gray-800 text-sm">Category</span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" class="space-y-2">
                                @foreach($categories as $cat)
                                <label class="flex items-center justify-between cursor-pointer group py-1">
                                    <div class="flex items-center gap-2.5">
                                        <input type="radio" name="category" value="{{ $cat->slug }}"
                                               {{ request('category') === $cat->slug ? 'checked' : '' }}
                                               onchange="document.getElementById('filter-form').submit()"
                                               class="w-4 h-4 text-brand-600 border-gray-300 focus:ring-brand-500 cursor-pointer">
                                        <span class="text-sm text-gray-700 group-hover:text-brand-700 transition-colors">{{ $cat->name }}</span>
                                    </div>
                                    <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-2 py-0.5">{{ $cat->courses_count ?? 0 }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        {{-- Course Type Filter --}}
                        <div x-data="{ open: true }">
                            <button type="button" @click="open = !open" class="flex items-center justify-between w-full mb-3">
                                <span class="font-semibold text-gray-800 text-sm">Course Type</span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" class="space-y-2">
                                @foreach(['online' => 'Online / Self-Paced', 'physical' => 'Physical / In-Person', 'hybrid' => 'Hybrid (Online + Physical)'] as $value => $label)
                                <label class="flex items-center gap-2.5 cursor-pointer group py-1">
                                    <input type="radio" name="type" value="{{ $value }}"
                                           {{ request('type') === $value ? 'checked' : '' }}
                                           onchange="document.getElementById('filter-form').submit()"
                                           class="w-4 h-4 text-brand-600 border-gray-300 focus:ring-brand-500 cursor-pointer">
                                    <span class="text-sm text-gray-700 group-hover:text-brand-700 transition-colors">{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        {{-- Level Filter --}}
                        <div x-data="{ open: true }">
                            <button type="button" @click="open = !open" class="flex items-center justify-between w-full mb-3">
                                <span class="font-semibold text-gray-800 text-sm">Level</span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" class="space-y-2">
                                @foreach(['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'] as $value => $label)
                                <label class="flex items-center gap-2.5 cursor-pointer group py-1">
                                    <input type="radio" name="level" value="{{ $value }}"
                                           {{ request('level') === $value ? 'checked' : '' }}
                                           onchange="document.getElementById('filter-form').submit()"
                                           class="w-4 h-4 text-brand-600 border-gray-300 focus:ring-brand-500 cursor-pointer">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full {{ $value === 'beginner' ? 'bg-green-500' : ($value === 'intermediate' ? 'bg-yellow-500' : 'bg-red-500') }}"></span>
                                        <span class="text-sm text-gray-700 group-hover:text-brand-700 transition-colors">{{ $label }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        {{-- Price Range Filter --}}
                        <div x-data="{
                            open: true,
                            min: {{ request('price_min', 0) }},
                            max: {{ request('price_max', 500000) }},
                            formatPrice(n) { return new Intl.NumberFormat('en-NG').format(n); }
                        }">
                            <button type="button" @click="open = !open" class="flex items-center justify-between w-full mb-3">
                                <span class="font-semibold text-gray-800 text-sm">Price Range</span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open">
                                <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                    <span>NGN <span x-text="formatPrice(min)"></span></span>
                                    <span>NGN <span x-text="formatPrice(max)"></span></span>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-xs text-gray-500 mb-1 block">Min Price</label>
                                        <input type="range" name="price_min" x-model="min" min="0" max="500000" step="5000"
                                               onchange="document.getElementById('filter-form').submit()"
                                               class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer accent-brand-600">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500 mb-1 block">Max Price</label>
                                        <input type="range" name="price_max" x-model="max" min="0" max="500000" step="5000"
                                               onchange="document.getElementById('filter-form').submit()"
                                               class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer accent-brand-600">
                                    </div>
                                </div>
                                <label class="flex items-center gap-2.5 cursor-pointer group mt-4">
                                    <input type="checkbox" name="free_only" value="1"
                                           {{ request('free_only') ? 'checked' : '' }}
                                           onchange="document.getElementById('filter-form').submit()"
                                           class="w-4 h-4 text-brand-600 border-gray-300 rounded focus:ring-brand-500 cursor-pointer">
                                    <span class="text-sm text-gray-700 group-hover:text-brand-700 transition-colors">Free courses only</span>
                                </label>
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        {{-- Certificate Filter --}}
                        <div>
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" name="certificate" value="1"
                                       {{ request('certificate') ? 'checked' : '' }}
                                       onchange="document.getElementById('filter-form').submit()"
                                       class="w-4 h-4 text-brand-600 border-gray-300 rounded focus:ring-brand-500 cursor-pointer">
                                <div>
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-brand-700 transition-colors block">Certificate Included</span>
                                    <span class="text-xs text-gray-400">Courses with verifiable cert</span>
                                </div>
                            </label>
                        </div>

                        {{-- Apply button (mobile) --}}
                        <button type="submit" class="lg:hidden w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 rounded-xl transition-colors text-sm">
                            Apply Filters
                        </button>
                    </div>
                </form>
            </aside>

            {{-- ─── MAIN COURSES AREA ─────────────────────────────── --}}
            <div class="flex-1 min-w-0">
                {{-- Toolbar: results count + sort --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div class="text-sm text-gray-600">
                        @if($courses->total() > 0)
                            Showing <span class="font-semibold text-gray-900">{{ $courses->firstItem() }}–{{ $courses->lastItem() }}</span> of <span class="font-semibold text-gray-900">{{ $courses->total() }}</span> courses
                        @else
                            No courses found
                        @endif
                        @if(request('search'))
                            for "<span class="font-semibold text-brand-700">{{ request('search') }}</span>"
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- View toggle --}}
                        <div x-data="{ view: localStorage.getItem('coursesView') || 'grid' }" class="flex bg-white border border-gray-200 rounded-xl overflow-hidden">
                            <button @click="view = 'grid'; localStorage.setItem('coursesView', 'grid')"
                                    :class="view === 'grid' ? 'bg-brand-600 text-white' : 'text-gray-500 hover:text-gray-700'"
                                    class="p-2.5 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            </button>
                            <button @click="view = 'list'; localStorage.setItem('coursesView', 'list')"
                                    :class="view === 'list' ? 'bg-brand-600 text-white' : 'text-gray-500 hover:text-gray-700'"
                                    class="p-2.5 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            </button>
                        </div>

                        {{-- Sort dropdown --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.away="open = false"
                                    class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-700 hover:border-brand-300 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/></svg>
                                @php
                                $sortLabels = ['popular' => 'Most Popular', 'newest' => 'Newest First', 'price_asc' => 'Price: Low to High', 'price_desc' => 'Price: High to Low', 'rating' => 'Highest Rated'];
                                @endphp
                                {{ $sortLabels[request('sort', 'popular')] ?? 'Most Popular' }}
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak
                                 class="absolute right-0 top-full mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-20">
                                @foreach($sortLabels as $value => $label)
                                <a href="{{ request()->fullUrlWithQuery(['sort' => $value, 'page' => 1]) }}"
                                   class="flex items-center justify-between px-4 py-2.5 text-sm hover:bg-brand-50 hover:text-brand-700 transition-colors {{ request('sort', 'popular') === $value ? 'text-brand-700 bg-brand-50 font-semibold' : 'text-gray-700' }}">
                                    {{ $label }}
                                    @if(request('sort', 'popular') === $value)
                                    <svg class="w-3.5 h-3.5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Active filter pills --}}
                @if(request()->hasAny(['category', 'type', 'level', 'free_only', 'certificate']))
                <div class="flex flex-wrap gap-2 mb-5">
                    @if(request('category'))
                    <span class="inline-flex items-center gap-1.5 bg-brand-100 text-brand-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                        Category: {{ request('category') }}
                        <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => 1]) }}" class="hover:text-brand-900">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    </span>
                    @endif
                    @if(request('type'))
                    <span class="inline-flex items-center gap-1.5 bg-purple-100 text-purple-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                        Type: {{ ucfirst(request('type')) }}
                        <a href="{{ request()->fullUrlWithQuery(['type' => null, 'page' => 1]) }}" class="hover:text-purple-900">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    </span>
                    @endif
                    @if(request('level'))
                    <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                        Level: {{ ucfirst(request('level')) }}
                        <a href="{{ request()->fullUrlWithQuery(['level' => null, 'page' => 1]) }}" class="hover:text-green-900">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    </span>
                    @endif
                    @if(request('free_only'))
                    <span class="inline-flex items-center gap-1.5 bg-yellow-100 text-yellow-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                        Free Only
                        <a href="{{ request()->fullUrlWithQuery(['free_only' => null, 'page' => 1]) }}" class="hover:text-yellow-900">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    </span>
                    @endif
                    @if(request('certificate'))
                    <span class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                        With Certificate
                        <a href="{{ request()->fullUrlWithQuery(['certificate' => null, 'page' => 1]) }}" class="hover:text-blue-900">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    </span>
                    @endif
                </div>
                @endif

                {{-- Courses Grid / List --}}
                @if($courses->count() > 0)
                <div x-data="{ view: localStorage.getItem('coursesView') || 'grid' }">

                    {{-- GRID VIEW --}}
                    <div x-show="view === 'grid'" class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach($courses as $course)
                        <article class="card-hover bg-white rounded-2xl border border-gray-200 overflow-hidden group animate-fade-in">
                            {{-- Thumbnail --}}
                            <div class="relative aspect-video overflow-hidden bg-gray-100">
                                <a href="{{ route('courses.show', $course->slug) }}">
                                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                </a>
                                {{-- Badges --}}
                                <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                                    @if($course->is_free)
                                    <span class="bg-green-500 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">FREE</span>
                                    @elseif($course->discount_price && $course->discount_price < $course->price)
                                    @php $disc = round((($course->price - $course->discount_price) / $course->price) * 100); @endphp
                                    <span class="bg-red-500 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $disc }}% OFF</span>
                                    @endif
                                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full
                                        {{ $course->level === 'beginner' ? 'bg-green-100 text-green-700' : ($course->level === 'intermediate' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                        {{ ucfirst($course->level ?? 'All') }}
                                    </span>
                                </div>
                                @if($course->category)
                                <span class="absolute top-3 right-3 bg-black bg-opacity-60 backdrop-blur-sm text-white text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ $course->category->name }}
                                </span>
                                @endif
                                @if($course->type === 'physical' || $course->type === 'hybrid')
                                <span class="absolute bottom-3 right-3 bg-brand-600 text-white text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                    {{ ucfirst($course->type) }}
                                </span>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="p-5">
                                <h3 class="font-display font-bold text-gray-900 text-sm leading-snug mb-2 group-hover:text-brand-700 transition-colors line-clamp-2">
                                    <a href="{{ route('courses.show', $course->slug) }}">{{ $course->title }}</a>
                                </h3>

                                @if($course->instructor)
                                <div class="flex items-center gap-2 mb-3">
                                    <img src="{{ $course->instructor->avatar_url }}" alt="{{ $course->instructor->full_name }}"
                                         class="w-5 h-5 rounded-full object-cover border border-gray-200">
                                    <span class="text-xs text-gray-500 truncate">{{ $course->instructor->full_name }}</span>
                                </div>
                                @endif

                                <div class="flex items-center gap-2 mb-3">
                                    <div class="flex items-center gap-0.5">
                                        @for($s = 1; $s <= 5; $s++)
                                        <svg class="w-3 h-3 {{ $s <= round($course->average_rating ?? 0) ? 'text-yellow-400 fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                    <span class="text-xs font-semibold text-gray-700">{{ number_format($course->average_rating ?? 0, 1) }}</span>
                                    <span class="text-xs text-gray-400">({{ number_format($course->total_reviews ?? 0) }})</span>
                                </div>

                                <div class="flex items-center gap-3 text-xs text-gray-500 mb-4">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $course->duration_hours ?? 0 }}h
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        {{ number_format($course->total_students ?? 0) }} students
                                    </span>
                                </div>

                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <div>
                                        @if($course->is_free)
                                        <span class="text-lg font-bold text-green-600">Free</span>
                                        @elseif($course->discount_price && $course->discount_price < $course->price)
                                        <span class="text-lg font-bold text-gray-900">{{ $course->currency ?? 'NGN' }} {{ number_format($course->discount_price) }}</span>
                                        <span class="text-xs text-gray-400 line-through ml-1">{{ number_format($course->price) }}</span>
                                        @else
                                        <span class="text-lg font-bold text-gray-900">{{ $course->currency ?? 'NGN' }} {{ number_format($course->price) }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('courses.show', $course->slug) }}"
                                       class="text-brand-600 hover:text-brand-700 border border-brand-200 hover:border-brand-400 hover:bg-brand-50 text-xs font-semibold px-3 py-1.5 rounded-xl transition-all">
                                        View Course
                                    </a>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    </div>

                    {{-- LIST VIEW --}}
                    <div x-show="view === 'list'" x-cloak class="space-y-4">
                        @foreach($courses as $course)
                        <article class="card-hover bg-white rounded-2xl border border-gray-200 overflow-hidden group animate-fade-in">
                            <div class="flex flex-col sm:flex-row">
                                {{-- Thumbnail --}}
                                <div class="relative sm:w-56 lg:w-64 shrink-0 aspect-video sm:aspect-auto overflow-hidden bg-gray-100">
                                    <a href="{{ route('courses.show', $course->slug) }}">
                                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    </a>
                                    @if($course->is_free)
                                    <span class="absolute top-2 left-2 bg-green-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">FREE</span>
                                    @elseif($course->discount_price && $course->discount_price < $course->price)
                                    @php $disc = round((($course->price - $course->discount_price) / $course->price) * 100); @endphp
                                    <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $disc }}% OFF</span>
                                    @endif
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 p-5 flex flex-col justify-between">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            @if($course->category)
                                            <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-2.5 py-0.5 rounded-full">{{ $course->category->name }}</span>
                                            @endif
                                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full
                                                {{ $course->level === 'beginner' ? 'bg-green-100 text-green-700' : ($course->level === 'intermediate' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                {{ ucfirst($course->level ?? 'All Levels') }}
                                            </span>
                                            <span class="text-xs text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-full">{{ ucfirst($course->type ?? 'online') }}</span>
                                        </div>
                                        <h3 class="font-display font-bold text-gray-900 text-base leading-snug mb-2 group-hover:text-brand-700 transition-colors">
                                            <a href="{{ route('courses.show', $course->slug) }}">{{ $course->title }}</a>
                                        </h3>
                                        @if($course->short_description)
                                        <p class="text-gray-500 text-xs leading-relaxed mb-3 line-clamp-2">{{ $course->short_description }}</p>
                                        @endif
                                        @if($course->instructor)
                                        <div class="flex items-center gap-2 mb-3">
                                            <img src="{{ $course->instructor->avatar_url }}" alt="{{ $course->instructor->full_name }}" class="w-5 h-5 rounded-full object-cover">
                                            <span class="text-xs text-gray-500">{{ $course->instructor->full_name }}</span>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div class="flex items-center gap-4 text-xs text-gray-500">
                                            <span class="flex items-center gap-1">
                                                <div class="flex items-center gap-0.5">
                                                    @for($s = 1; $s <= 5; $s++)
                                                    <svg class="w-3 h-3 {{ $s <= round($course->average_rating ?? 0) ? 'text-yellow-400 fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                    @endfor
                                                </div>
                                                <span class="font-semibold text-gray-700">{{ number_format($course->average_rating ?? 0, 1) }}</span>
                                            </span>
                                            <span>{{ $course->duration_hours ?? 0 }}h</span>
                                            <span>{{ number_format($course->total_students ?? 0) }} students</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div>
                                                @if($course->is_free)
                                                <span class="text-base font-bold text-green-600">Free</span>
                                                @elseif($course->discount_price && $course->discount_price < $course->price)
                                                <span class="text-base font-bold text-gray-900">{{ $course->currency ?? 'NGN' }} {{ number_format($course->discount_price) }}</span>
                                                <span class="text-xs text-gray-400 line-through ml-1">{{ number_format($course->price) }}</span>
                                                @else
                                                <span class="text-base font-bold text-gray-900">{{ $course->currency ?? 'NGN' }} {{ number_format($course->price) }}</span>
                                                @endif
                                            </div>
                                            <a href="{{ route('courses.show', $course->slug) }}"
                                               class="bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition-colors whitespace-nowrap">
                                                View Course
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    </div>
                </div>

                {{-- Pagination --}}
                @if($courses->hasPages())
                <div class="mt-10 flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Page {{ $courses->currentPage() }} of {{ $courses->lastPage() }}
                    </p>
                    <div class="flex items-center gap-1">
                        {{-- Previous --}}
                        @if($courses->onFirstPage())
                        <span class="px-3 py-2 text-gray-300 bg-white border border-gray-200 rounded-xl cursor-not-allowed text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </span>
                        @else
                        <a href="{{ $courses->previousPageUrl() }}" class="px-3 py-2 text-gray-600 bg-white border border-gray-200 hover:border-brand-300 hover:text-brand-600 rounded-xl transition-colors text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                        @endif

                        {{-- Page numbers --}}
                        @foreach($courses->getUrlRange(max(1, $courses->currentPage() - 2), min($courses->lastPage(), $courses->currentPage() + 2)) as $page => $url)
                        @if($page == $courses->currentPage())
                        <span class="px-4 py-2 bg-brand-600 text-white rounded-xl text-sm font-semibold">{{ $page }}</span>
                        @else
                        <a href="{{ $url }}" class="px-4 py-2 text-gray-600 bg-white border border-gray-200 hover:border-brand-300 hover:text-brand-600 rounded-xl transition-colors text-sm">{{ $page }}</a>
                        @endif
                        @endforeach

                        {{-- Next --}}
                        @if($courses->hasMorePages())
                        <a href="{{ $courses->nextPageUrl() }}" class="px-3 py-2 text-gray-600 bg-white border border-gray-200 hover:border-brand-300 hover:text-brand-600 rounded-xl transition-colors text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        @else
                        <span class="px-3 py-2 text-gray-300 bg-white border border-gray-200 rounded-xl cursor-not-allowed text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </span>
                        @endif
                    </div>
                </div>
                @endif

                @else
                {{-- Empty state --}}
                <div class="bg-white rounded-2xl border border-gray-200 py-20 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-5">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-display font-bold text-gray-900 text-xl mb-2">No courses found</h3>
                    <p class="text-gray-500 text-sm mb-6 max-w-sm mx-auto">
                        @if(request('search'))
                            We couldn't find any courses matching "{{ request('search') }}". Try different keywords or clear your filters.
                        @else
                            No courses match your current filters. Try broadening your search.
                        @endif
                    </p>
                    <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-3 rounded-xl transition-colors text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Clear All Filters
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection
