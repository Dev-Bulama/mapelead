@extends('layouts.app')

@section('title', $settings['meta_title'] ?? 'MapeLearn — Industry-Leading Tech Training')
@section('meta_description', $settings['meta_description'] ?? 'Join 10,000+ professionals transforming their careers with MapeLearn\'s industry-led tech programs.')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════
     HERO SECTION — Alpine.js Slider
═══════════════════════════════════════════════════════════════════ --}}
@php $bannerCount = isset($heroBanners) && $heroBanners->isNotEmpty() ? $heroBanners->count() : 0; @endphp

@if($bannerCount > 0)
{{-- ── Dynamic Banner Slider ── --}}
<section
    class="relative overflow-hidden min-h-[92vh] flex flex-col"
    x-data="{ current: 0, total: {{ $bannerCount }}, autoplay: true }"
    x-init="setInterval(() => { if(autoplay) current = (current + 1) % total }, 5000)"
    @mouseenter="autoplay = false"
    @mouseleave="autoplay = true"
>
    {{-- Slides --}}
    @foreach($heroBanners as $index => $banner)
    <div
        class="absolute inset-0 transition-all duration-700 ease-in-out"
        :class="{{ $index }} === current ? 'opacity-100 z-10' : 'opacity-0 z-0'"
        x-show="{{ $index }} === current || true"
        style="display: block;"
    >
        {{-- Background: image or gradient --}}
        @if($banner->image)
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
             style="background-image: url('{{ asset('storage/' . $banner->image) }}');">
            <div class="absolute inset-0 bg-[#14215B] bg-opacity-70"></div>
        </div>
        @else
        <div class="absolute inset-0 bg-gradient-to-br from-[#14215B] to-blue-900">
            {{-- Decorative blobs --}}
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-500 rounded-full opacity-10 blur-3xl"></div>
            <div class="absolute top-1/2 -left-32 w-80 h-80 bg-purple-500 rounded-full opacity-10 blur-3xl"></div>
            <div class="absolute bottom-0 right-1/3 w-64 h-64 bg-cyan-500 rounded-full opacity-10 blur-3xl"></div>
            <div class="absolute inset-0 opacity-5" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        @endif
    </div>
    @endforeach

    {{-- Slide Content --}}
    <div class="relative z-20 flex-1 flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32 w-full">
            @foreach($heroBanners as $index => $banner)
            <div
                class="max-w-3xl transition-all duration-700 ease-in-out"
                :class="{{ $index }} === current ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4 pointer-events-none absolute'"
                x-show="{{ $index }} === current || true"
                style="display: block;"
            >
                {{-- Badge --}}
                @if($banner->badge_text)
                <div class="inline-flex items-center gap-2 bg-white bg-opacity-10 border border-white border-opacity-25 rounded-full px-4 py-1.5 mb-6">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    <span class="text-blue-100 text-sm font-medium">{{ $banner->badge_text }}</span>
                </div>
                @endif

                {{-- Title --}}
                <h1 class="font-display text-5xl sm:text-6xl font-bold text-white leading-tight mb-5">
                    {!! nl2br(e($banner->title)) !!}
                </h1>

                {{-- Subtitle --}}
                @if($banner->subtitle)
                <p class="text-xl text-blue-200 font-medium mb-4 max-w-2xl">{{ $banner->subtitle }}</p>
                @endif

                {{-- Description --}}
                @if($banner->description)
                <p class="text-blue-100 leading-relaxed mb-10 max-w-2xl">{{ $banner->description }}</p>
                @endif

                {{-- CTA Buttons --}}
                <div class="flex gap-3">
                    @if($banner->primary_btn_text && $banner->primary_btn_url)
                    <a href="{{ $banner->primary_btn_url }}"
                       class="inline-flex items-center gap-2 bg-white text-[#14215B] hover:bg-blue-50 font-bold px-5 py-3 sm:px-8 sm:py-4 text-sm sm:text-base rounded-2xl transition-all duration-200 shadow-lg hover:-translate-y-0.5">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        {{ $banner->primary_btn_text }}
                    </a>
                    @endif
                    @if($banner->secondary_btn_text && $banner->secondary_btn_url)
                    <a href="{{ $banner->secondary_btn_url }}"
                       class="inline-flex items-center gap-2 bg-transparent border-2 border-white text-white hover:bg-white hover:text-[#14215B] font-semibold px-5 py-3 sm:px-8 sm:py-4 text-sm sm:text-base rounded-2xl transition-all duration-200">
                        {{ $banner->secondary_btn_text }}
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Left / Right Arrow Navigation --}}
    @if($bannerCount > 1)
    <button
        @click="current = (current - 1 + total) % total; autoplay = false"
        class="absolute left-4 top-1/2 -translate-y-1/2 z-30 w-12 h-12 bg-white bg-opacity-20 hover:bg-opacity-40 border border-white border-opacity-30 rounded-full flex items-center justify-center text-white transition-all duration-200 backdrop-blur-sm"
        aria-label="Previous slide"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button
        @click="current = (current + 1) % total; autoplay = false"
        class="absolute right-4 top-1/2 -translate-y-1/2 z-30 w-12 h-12 bg-white bg-opacity-20 hover:bg-opacity-40 border border-white border-opacity-30 rounded-full flex items-center justify-center text-white transition-all duration-200 backdrop-blur-sm"
        aria-label="Next slide"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>
    @endif

    {{-- Dot Indicators --}}
    @if($bannerCount > 1)
    <div class="relative z-30 flex justify-center gap-2 pb-20">
        @foreach($heroBanners as $index => $banner)
        <button
            @click="current = {{ $index }}"
            :class="{{ $index }} === current ? 'bg-white w-7' : 'bg-white bg-opacity-40 w-2.5'"
            class="h-2.5 rounded-full transition-all duration-300"
            aria-label="Go to slide {{ $index + 1 }}"
        ></button>
        @endforeach
    </div>
    @endif

    {{-- Stats Bar --}}
    <div class="absolute bottom-0 left-0 right-0 z-30 bg-black bg-opacity-30 backdrop-blur-sm border-t border-white border-opacity-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-white divide-opacity-10">
                @php
                    $heroStats = [
                        ['value' => $stats['students'] ?? '10,000+', 'label' => 'Active Students', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                        ['value' => $stats['courses'] ?? '150+', 'label' => 'Expert Courses', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                        ['value' => $stats['instructors'] ?? '50+', 'label' => 'Industry Instructors', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                        ['value' => $stats['placement'] ?? '92%', 'label' => 'Placement Rate', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    ];
                @endphp
                @foreach($heroStats as $stat)
                <div class="py-5 px-6 flex items-center gap-3">
                    <div class="w-10 h-10 bg-white bg-opacity-10 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/></svg>
                    </div>
                    <div>
                        <div class="text-white font-bold text-lg leading-none">{{ $stat['value'] }}</div>
                        <div class="text-blue-300 text-xs mt-0.5">{{ $stat['label'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

@else
{{-- ── Static Fallback Hero (no active banners) ── --}}
<section class="hero-gradient relative overflow-hidden min-h-[92vh] flex items-center">
    {{-- Background decorative blobs --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-brand-500 rounded-full opacity-10 blur-3xl"></div>
        <div class="absolute top-1/2 -left-32 w-80 h-80 bg-purple-500 rounded-full opacity-10 blur-3xl"></div>
        <div class="absolute bottom-0 right-1/3 w-64 h-64 bg-cyan-500 rounded-full opacity-10 blur-3xl"></div>
        {{-- Grid pattern overlay --}}
        <div class="absolute inset-0 opacity-5" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
        <div class="max-w-3xl">
            {{-- Badge --}}
            @if(!empty($settings['hero_badge']))
            <div class="inline-flex items-center gap-2 bg-brand-800 bg-opacity-60 border border-brand-500 border-opacity-40 rounded-full px-4 py-1.5 mb-6">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                <span class="text-brand-200 text-sm font-medium">{{ $settings['hero_badge'] }}</span>
            </div>
            @else
            <div class="inline-flex items-center gap-2 bg-brand-800 bg-opacity-60 border border-brand-500 border-opacity-40 rounded-full px-4 py-1.5 mb-6">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                <span class="text-brand-200 text-sm font-medium">Africa's #1 Tech Skills Platform</span>
            </div>
            @endif

            {{-- Headline --}}
            <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
                @if(!empty($settings['hero_title']))
                    {!! nl2br(e($settings['hero_title'])) !!}
                @else
                    Launch Your <span class="gradient-text">Tech Career</span><br>With Industry Experts
                @endif
            </h1>

            {{-- Subheadline --}}
            <p class="text-lg sm:text-xl text-brand-200 leading-relaxed mb-10 max-w-2xl">
                {{ $settings['hero_subtitle'] ?? 'Join thousands of professionals mastering in-demand skills through structured programs designed by industry leaders. From beginner to job-ready in weeks.' }}
            </p>

            {{-- CTA Buttons --}}
            <div class="flex gap-3 mb-16">
                <a href="{{ route('courses.index') }}"
                   class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-400 text-white font-bold px-5 py-3 sm:px-8 sm:py-4 text-sm sm:text-base rounded-2xl transition-all duration-200 shadow-lg shadow-brand-900/40 hover:shadow-brand-500/30 hover:-translate-y-0.5">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    {{ $settings['hero_cta_primary'] ?? 'Explore Courses' }}
                </a>
                <a href="{{ route('auth.register') }}"
                   class="inline-flex items-center gap-2 bg-white bg-opacity-10 hover:bg-opacity-20 border border-white border-opacity-25 text-white font-semibold px-5 py-3 sm:px-8 sm:py-4 text-sm sm:text-base rounded-2xl transition-all duration-200 backdrop-blur-sm">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $settings['hero_cta_secondary'] ?? 'Watch Demo' }}
                </a>
            </div>

            {{-- Trust Indicators --}}
            <div class="flex flex-wrap gap-6 items-center">
                <div class="flex -space-x-2">
                    @for($i = 1; $i <= 5; $i++)
                    <div class="w-9 h-9 rounded-full border-2 border-brand-800 bg-gradient-to-br from-brand-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                        {{ chr(64 + $i) }}
                    </div>
                    @endfor
                    <div class="w-9 h-9 rounded-full border-2 border-brand-800 bg-brand-700 flex items-center justify-center text-brand-200 text-xs font-bold">+</div>
                </div>
                <div class="text-brand-200 text-sm">
                    <span class="text-white font-bold">{{ $stats['students'] ?? '10,000+' }}</span> students already enrolled
                </div>
                <div class="flex items-center gap-1">
                    @for($i = 0; $i < 5; $i++)
                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                    <span class="text-brand-200 text-sm ml-1">4.9/5 rating</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-30 backdrop-blur-sm border-t border-white border-opacity-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-white divide-opacity-10">
                @php
                    $heroStats = [
                        ['value' => $stats['students'] ?? '10,000+', 'label' => 'Active Students', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                        ['value' => $stats['courses'] ?? '150+', 'label' => 'Expert Courses', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                        ['value' => $stats['instructors'] ?? '50+', 'label' => 'Industry Instructors', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                        ['value' => $stats['placement'] ?? '92%', 'label' => 'Placement Rate', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    ];
                @endphp
                @foreach($heroStats as $stat)
                <div class="py-5 px-6 flex items-center gap-3">
                    <div class="w-10 h-10 bg-brand-600 bg-opacity-50 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/></svg>
                    </div>
                    <div>
                        <div class="text-white font-bold text-lg leading-none">{{ $stat['value'] }}</div>
                        <div class="text-brand-300 text-xs mt-0.5">{{ $stat['label'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════════════
     NEWS TICKER / ANNOUNCEMENTS
═══════════════════════════════════════════════════════════════════ --}}
@if(isset($newsTickers) && $newsTickers->isNotEmpty())
<div class="bg-[#14215B] text-white py-2 overflow-hidden relative" x-data="newsTicker()">
    <div class="flex items-center">
        <div class="shrink-0 bg-yellow-400 text-[#14215B] font-bold text-xs px-3 py-1 mr-4 uppercase tracking-wide z-10 relative">
            LIVE
        </div>
        <div class="flex-1 overflow-hidden">
            <div class="flex whitespace-nowrap" :style="'transform: translateX(-' + offset + 'px); transition: none;'" x-ref="ticker">
                @foreach($newsTickers as $ticker)
                <span class="inline-flex items-center gap-3 mr-16 text-sm font-medium">
                    <span class="w-1.5 h-1.5 bg-yellow-400 rounded-full shrink-0"></span>
                    {{ $ticker->message }}
                    @if($ticker->url)
                    <a href="{{ $ticker->url }}" class="underline text-yellow-300 hover:text-yellow-200 text-xs">{{ $ticker->url_text ?? 'Learn More' }}</a>
                    @endif
                </span>
                @endforeach
                {{-- Duplicate for seamless loop --}}
                @foreach($newsTickers as $ticker)
                <span class="inline-flex items-center gap-3 mr-16 text-sm font-medium">
                    <span class="w-1.5 h-1.5 bg-yellow-400 rounded-full shrink-0"></span>
                    {{ $ticker->message }}
                    @if($ticker->url)
                    <a href="{{ $ticker->url }}" class="underline text-yellow-300 hover:text-yellow-200 text-xs">{{ $ticker->url_text ?? 'Learn More' }}</a>
                    @endif
                </span>
                @endforeach
            </div>
        </div>
    </div>
</div>
<script>
function newsTicker() {
    return {
        offset: 0,
        speed: 0.5,
        animFrame: null,
        init() {
            this.$nextTick(() => {
                const el = this.$refs.ticker;
                const halfWidth = el.scrollWidth / 2;
                const step = () => {
                    this.offset += this.speed;
                    if (this.offset >= halfWidth) this.offset = 0;
                    el.style.transform = 'translateX(-' + this.offset + 'px)';
                    this.animFrame = requestAnimationFrame(step);
                };
                this.animFrame = requestAnimationFrame(step);
            });
        },
        destroy() { cancelAnimationFrame(this.animFrame); }
    }
}
</script>
@endif

{{-- ═══════════════════════════════════════════════════════════════════
     CATEGORIES SECTION
═══════════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-14">
            <span class="inline-block text-brand-600 font-semibold text-sm uppercase tracking-widest mb-3">Programs</span>
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Explore Our Programs</h2>
            <p class="text-gray-500 text-lg max-w-xl mx-auto">Choose from our curated selection of in-demand technology tracks designed to fast-track your career.</p>
        </div>

        @if(isset($categories) && $categories->count())
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @foreach($categories as $cat)
            <a href="{{ route('courses.category', $cat->slug) }}"
               class="group card-hover bg-gray-50 hover:bg-brand-50 border border-gray-200 hover:border-brand-200 rounded-2xl p-6 text-center transition-all duration-200">
                {{-- Icon placeholder --}}
                <div class="w-14 h-14 mx-auto mb-4 bg-brand-100 group-hover:bg-brand-200 rounded-2xl flex items-center justify-center transition-colors">
                    @if($cat->icon)
                        @if(str_contains($cat->icon, '.') || str_contains($cat->icon, '/'))
                            <img src="{{ asset('storage/' . $cat->icon) }}" alt="{{ $cat->name }}" class="w-8 h-8 object-contain">
                        @else
                            <span class="text-2xl leading-none">{{ $cat->icon }}</span>
                        @endif
                    @else
                        <svg class="w-7 h-7 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                        </svg>
                    @endif
                </div>
                <h3 class="font-semibold text-gray-800 group-hover:text-brand-700 text-sm leading-snug mb-1 transition-colors">{{ $cat->name }}</h3>
                <p class="text-xs text-gray-500">{{ $cat->courses_count ?? $cat->courses->count() }} {{ Str::plural('course', $cat->courses_count ?? 0) }}</p>
            </a>
            @endforeach

            {{-- View all card --}}
            <a href="{{ route('courses.index') }}"
               class="group card-hover bg-brand-600 hover:bg-brand-700 rounded-2xl p-6 text-center transition-all duration-200">
                <div class="w-14 h-14 mx-auto mb-4 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                </div>
                <h3 class="font-semibold text-white text-sm leading-snug mb-1">Browse All</h3>
                <p class="text-xs text-brand-200">{{ $categories->sum('courses_count') ?? '' }} Courses</p>
            </a>
        </div>
        @else
        <div class="text-center py-10 text-gray-400">No categories available yet.</div>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     FEATURED COURSES SECTION
═══════════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-14">
            <div>
                <span class="inline-block text-brand-600 font-semibold text-sm uppercase tracking-widest mb-3">Top Picks</span>
                <h2 class="font-display text-3xl sm:text-4xl font-bold text-gray-900">Popular Courses</h2>
            </div>
            <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-2 text-brand-600 hover:text-brand-700 font-semibold transition-colors shrink-0">
                View all courses
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        @if(isset($featuredCourses) && $featuredCourses->count())
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredCourses as $course)
            <article class="card-hover bg-white rounded-2xl border border-gray-200 overflow-hidden group">
                {{-- Thumbnail --}}
                <div class="relative overflow-hidden aspect-video bg-gray-200">
                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @php
                        $hasActiveBatch = isset($activeBatches) && $activeBatches->where('course_id', $course->id)->isNotEmpty();
                        $hasUpcomingBatch = !$hasActiveBatch && isset($upcomingBatches) && $upcomingBatches->where('course_id', $course->id)->isNotEmpty();
                    @endphp
                    @if($hasActiveBatch)
                    <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 bg-green-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-lg">
                        <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                        Class Active
                    </span>
                    @elseif($hasUpcomingBatch)
                    <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 bg-orange-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-lg">
                        <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                        Starting Soon
                    </span>
                    @elseif($course->is_free)
                    <span class="absolute top-3 left-3 bg-green-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">FREE</span>
                    @elseif($course->discount_price && $course->discount_price < $course->price)
                    @php $discount = round((($course->price - $course->discount_price) / $course->price) * 100); @endphp
                    <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">{{ $discount }}% OFF</span>
                    @endif
                    {{-- Category badge --}}
                    @if($course->category)
                    <span class="absolute top-3 right-3 bg-black bg-opacity-60 backdrop-blur-sm text-white text-xs font-medium px-2.5 py-1 rounded-full">
                        {{ $course->category->name }}
                    </span>
                    @endif
                    {{-- Play button hover --}}
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black bg-opacity-20">
                        <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-brand-600 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-5">
                    {{-- Level badge --}}
                    <span class="inline-block text-xs font-semibold px-2.5 py-0.5 rounded-full mb-3
                        {{ $course->level === 'beginner' ? 'bg-green-100 text-green-700' : ($course->level === 'intermediate' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                        {{ ucfirst($course->level ?? 'All Levels') }}
                    </span>

                    <h3 class="font-display font-bold text-gray-900 text-base leading-snug mb-2 group-hover:text-brand-700 transition-colors line-clamp-2">
                        <a href="{{ route('courses.show', $course->slug) }}">{{ $course->title }}</a>
                    </h3>

                    {{-- Instructor --}}
                    @if($course->instructor)
                    <div class="flex items-center gap-2 mb-3">
                        <img src="{{ $course->instructor->avatar_url }}" alt="{{ $course->instructor->full_name }}"
                             class="w-6 h-6 rounded-full object-cover border border-gray-200">
                        <span class="text-sm text-gray-500">{{ $course->instructor->full_name }}</span>
                    </div>
                    @endif

                    {{-- Rating --}}
                    <div class="flex items-center gap-2 mb-3">
                        <div class="flex items-center gap-0.5">
                            @for($s = 1; $s <= 5; $s++)
                            <svg class="w-3.5 h-3.5 {{ $s <= round($course->average_rating ?? 0) ? 'text-yellow-400 fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <span class="text-sm font-semibold text-gray-700">{{ number_format($course->average_rating ?? 0, 1) }}</span>
                        <span class="text-xs text-gray-400">({{ number_format($course->total_reviews ?? 0) }})</span>
                    </div>

                    {{-- Meta: duration + type --}}
                    <div class="flex items-center gap-3 text-xs text-gray-500 mb-4">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $course->duration_hours ?? 0 }}h
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            {{ ucfirst($course->type ?? 'online') }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                            {{ number_format($course->total_students ?? 0) }}
                        </span>
                    </div>

                    {{-- Footer: Price + CTA --}}
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div>
                            @if($course->is_free)
                                <span class="text-xl font-bold text-green-600">Free</span>
                            @elseif($course->discount_price && $course->discount_price < $course->price)
                                <span class="text-xl font-bold text-gray-900">{{ $course->currency ?? 'NGN' }} {{ number_format($course->discount_price) }}</span>
                                <span class="text-sm text-gray-400 line-through ml-1">{{ number_format($course->price) }}</span>
                            @else
                                <span class="text-xl font-bold text-gray-900">{{ $course->currency ?? 'NGN' }} {{ number_format($course->price) }}</span>
                            @endif
                        </div>
                        <a href="{{ route('courses.show', $course->slug) }}"
                           class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                            Enroll Now
                        </a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
        @else
        <div class="text-center py-16 text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <p class="font-medium">Courses coming soon</p>
        </div>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     WHY CHOOSE US SECTION
═══════════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block text-brand-600 font-semibold text-sm uppercase tracking-widest mb-3">Our Difference</span>
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Why 10,000+ Students Choose MapeLearn</h2>
            <p class="text-gray-500 text-lg max-w-2xl mx-auto">We've built every aspect of our platform with one goal: getting you hired and growing your career faster.</p>
        </div>

        @php
        $features = [
            [
                'title' => 'Industry-Led Curriculum',
                'desc'  => 'Our programs are designed in collaboration with tech leaders at top companies. Every course maps directly to real job requirements.',
                'icon'  => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
                'color' => 'bg-blue-50 text-blue-600',
            ],
            [
                'title' => 'Expert Instructors',
                'desc'  => 'Learn from senior engineers, CTOs, and product leaders with 10+ years of real-world experience at top African and global tech companies.',
                'icon'  => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
                'color' => 'bg-purple-50 text-purple-600',
            ],
            [
                'title' => 'Flexible Learning',
                'desc'  => 'Study at your own pace with on-demand videos, or join our live cohorts. Access content on any device, anytime — even offline.',
                'icon'  => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'color' => 'bg-green-50 text-green-600',
            ],
            [
                'title' => 'Job Placement Support',
                'desc'  => 'Dedicated career coaches, resume reviews, mock interviews, and direct connections to our 200+ hiring partner companies.',
                'icon'  => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                'color' => 'bg-orange-50 text-orange-600',
            ],
            [
                'title' => 'Certificate Programs',
                'desc'  => 'Earn verifiable blockchain-backed certificates recognized by top employers. Showcase your credentials on LinkedIn with one click.',
                'icon'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'color' => 'bg-yellow-50 text-yellow-600',
            ],
            [
                'title' => 'Community & Networking',
                'desc'  => 'Join a vibrant community of 10,000+ peers. Collaborate on projects, attend events, and build a professional network that opens doors.',
                'icon'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                'color' => 'bg-pink-50 text-pink-600',
            ],
        ];
        @endphp

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($features as $feature)
            <div class="group card-hover bg-gray-50 hover:bg-white border border-gray-200 hover:border-brand-200 rounded-2xl p-7 transition-all duration-200">
                <div class="w-13 h-13 w-12 h-12 {{ $feature['color'] }} rounded-2xl flex items-center justify-center mb-5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="font-display font-bold text-gray-900 text-lg mb-2 group-hover:text-brand-700 transition-colors">{{ $feature['title'] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $feature['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     SERVICES SECTION
═══════════════════════════════════════════════════════════════════ --}}
@if(isset($services) && $services->count())
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block text-brand-600 font-semibold text-sm uppercase tracking-widest mb-3">What We Offer</span>
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Our Services</h2>
            <p class="text-gray-500 text-lg max-w-xl mx-auto">Practical hands-on training and career services designed to transform your professional trajectory.</p>
        </div>
        @php
            $accentColors = [
                ['bg' => '#14215B', 'light' => '#e8eaf6'],
                ['bg' => '#1d6fa4', 'light' => '#e1f0fa'],
                ['bg' => '#7c3aed', 'light' => '#ede9fe'],
                ['bg' => '#0f766e', 'light' => '#d1faf5'],
                ['bg' => '#b45309', 'light' => '#fef3c7'],
                ['bg' => '#be185d', 'light' => '#fce7f3'],
            ];
        @endphp
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-7">
            @foreach($services as $i => $service)
            @php $accent = $accentColors[$i % count($accentColors)]; @endphp
            <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 flex flex-col">

                {{-- Top image or colored header --}}
                @if($service->image_url)
                <div class="relative h-44 overflow-hidden">
                    <img src="{{ $service->image_url }}" alt="{{ $service->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0" style="background: linear-gradient(to bottom, transparent 40%, rgba(0,0,0,0.55) 100%)"></div>
                    {{-- Number badge --}}
                    <span class="absolute top-4 left-4 w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background-color: {{ $accent['bg'] }}">
                        {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
                @else
                <div class="h-44 flex items-center justify-center relative overflow-hidden" style="background-color: {{ $accent['light'] }}">
                    <div class="absolute -bottom-6 -right-6 w-28 h-28 rounded-full opacity-20" style="background-color: {{ $accent['bg'] }}"></div>
                    <div class="absolute -top-4 -left-4 w-20 h-20 rounded-full opacity-10" style="background-color: {{ $accent['bg'] }}"></div>
                    <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-white text-3xl font-extrabold shadow-lg z-10" style="background-color: {{ $accent['bg'] }}">
                        {{ mb_substr($service->title, 0, 1) }}
                    </div>
                    <span class="absolute top-4 left-4 w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background-color: {{ $accent['bg'] }}">
                        {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
                @endif

                {{-- Content --}}
                <div class="p-6 flex flex-col flex-1">
                    {{-- Colored accent line --}}
                    <div class="w-10 h-1 rounded-full mb-4" style="background-color: {{ $accent['bg'] }}"></div>
                    <h3 class="font-display font-bold text-gray-900 text-lg mb-3 group-hover:text-brand-700 transition-colors leading-snug">
                        {{ $service->title }}
                    </h3>
                    <p class="text-gray-500 text-sm leading-relaxed flex-1">{{ $service->description }}</p>

                    @if($service->link_url)
                    <div class="mt-5 pt-4 border-t border-gray-100">
                        <a href="{{ $service->link_url }}"
                           class="inline-flex items-center gap-2 text-sm font-semibold transition-colors"
                           style="color: {{ $accent['bg'] }}">
                            {{ $service->link_text ?? 'Learn More' }}
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    </div>
                    @else
                    <div class="mt-5 pt-4 border-t border-gray-100">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-widest" style="color: {{ $accent['bg'] }}">
                            Professional Service
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════════════
     PARTNER LOGOS MARQUEE
═══════════════════════════════════════════════════════════════════ --}}
@php
    $partnerLogosJson = \App\Models\SiteSetting::get('partner_logos', null);
    $partnerLogos = $partnerLogosJson ? json_decode($partnerLogosJson, true) : [];
@endphp
@if(!empty($partnerLogos))
<section class="py-12 bg-white border-y border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-center text-xs font-semibold uppercase tracking-widest text-gray-400 mb-8">Trusted by leading organisations</p>
    </div>
    <div class="w-full overflow-hidden">
        <div class="flex gap-12 items-center partner-marquee">
            @php $doubled = array_merge($partnerLogos, $partnerLogos); @endphp
            @foreach($doubled as $partner)
            <a href="{{ $partner['url'] ?? '#' }}" target="_blank" rel="noopener"
               class="flex-shrink-0 grayscale hover:grayscale-0 opacity-60 hover:opacity-100 transition-all duration-300">
                @if(!empty($partner['logo']))
                    <img src="{{ asset('storage/' . $partner['logo']) }}"
                         alt="{{ $partner['name'] ?? '' }}"
                         class="h-10 max-w-[140px] w-auto object-contain">
                @else
                    <span class="text-gray-400 font-semibold text-sm px-4">{{ $partner['name'] ?? '' }}</span>
                @endif
            </a>
            @endforeach
        </div>
    </div>
    <style>
        .partner-marquee {
            animation: marquee 30s linear infinite;
            width: max-content;
            will-change: transform;
        }
        .partner-marquee:hover { animation-play-state: paused; }
        @keyframes marquee {
            from { transform: translateX(0); }
            to   { transform: translateX(-50%); }
        }
    </style>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════════════
     STATS COUNTER SECTION
═══════════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-brand-950 relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-brand-700 rounded-full opacity-10 blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-purple-700 rounded-full opacity-10 blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-white mb-3">Numbers That Speak for Themselves</h2>
            <p class="text-brand-300 text-lg">Real impact, real results — backed by data.</p>
        </div>

        @php
            $statStudents    = (int) preg_replace('/[^0-9]/', '', $stats['students']    ?? '10000') ?: 10000;
            $statCourses     = (int) preg_replace('/[^0-9]/', '', $stats['courses']     ?? '50')    ?: 50;
            $statInstructors = (int) preg_replace('/[^0-9]/', '', $stats['instructors'] ?? '25')    ?: 25;
            $statPlacement   = (int) preg_replace('/[^0-9]/', '', $stats['placement']   ?? '92')    ?: 92;
            $statSufStudents = preg_match('/%/', $stats['students']    ?? '') ? '%' : '+';
            $statSufCourses  = preg_match('/%/', $stats['courses']     ?? '') ? '%' : '+';
            $statSufInst     = preg_match('/%/', $stats['instructors'] ?? '') ? '%' : '+';
            $statSufPlace    = preg_match('/%/', $stats['placement']   ?? '+') ? '%' : '+';
            $statLabelStudents    = $stats['students_label']    ?? 'Students Trained';
            $statLabelCourses     = $stats['courses_label']     ?? 'Expert Courses';
            $statLabelInstructors = $stats['instructors_label'] ?? 'Industry Instructors';
            $statLabelPlacement   = $stats['placement_label']   ?? 'Job Placement Rate';
        @endphp
        <div x-data="{
            animated: false,
            counters: [
                { label: '{{ addslashes($statLabelStudents) }}',    value: {{ $statStudents }},    suffix: '{{ $statSufStudents }}', prefix: '', current: 0, icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
                { label: '{{ addslashes($statLabelCourses) }}',     value: {{ $statCourses }},     suffix: '{{ $statSufCourses }}',  prefix: '', current: 0, icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' },
                { label: '{{ addslashes($statLabelInstructors) }}', value: {{ $statInstructors }}, suffix: '{{ $statSufInst }}',     prefix: '', current: 0, icon: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z' },
                { label: '{{ addslashes($statLabelPlacement) }}',   value: {{ $statPlacement }},   suffix: '{{ $statSufPlace }}',    prefix: '', current: 0, icon: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
            ],
            animate() {
                if (this.animated) return;
                this.animated = true;
                this.counters.forEach((counter, i) => {
                    const duration = 2000;
                    const steps = 60;
                    const increment = counter.value / steps;
                    let step = 0;
                    const timer = setInterval(() => {
                        step++;
                        this.counters[i].current = Math.min(Math.round(increment * step), counter.value);
                        if (step >= steps) clearInterval(timer);
                    }, duration / steps);
                });
            }
        }"
        x-intersect.once="animate()"
        x-init="setTimeout(() => animate(), 800)"
        class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <template x-for="counter in counters" :key="counter.label">
                <div class="bg-white bg-opacity-5 border border-white border-opacity-10 rounded-2xl p-8 text-center">
                    <div class="w-14 h-14 mx-auto mb-5 bg-brand-800 rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="counter.icon"/>
                        </svg>
                    </div>
                    <div class="font-display text-4xl font-extrabold text-white mb-2">
                        <span x-text="counter.prefix + counter.current.toLocaleString() + counter.suffix"></span>
                    </div>
                    <p class="text-brand-300 text-sm font-medium" x-text="counter.label"></p>
                </div>
            </template>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     TESTIMONIALS SECTION
═══════════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block text-brand-600 font-semibold text-sm uppercase tracking-widest mb-3">Success Stories</span>
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-4">What Our Students Say</h2>
            <p class="text-gray-500 text-lg max-w-xl mx-auto">Real stories from real people who transformed their careers with MapeLearn.</p>
        </div>

        @if(isset($testimonials) && count($testimonials))
        <div x-data="{
            active: 0,
            total: {{ count($testimonials) }},
            next() { this.active = (this.active + 1) % this.total; },
            prev() { this.active = (this.active - 1 + this.total) % this.total; }
        }" class="relative">
            {{-- Testimonial Grid (visible on lg+) --}}
            <div class="hidden lg:grid grid-cols-3 gap-6">
                @foreach($testimonials as $i => $testimonial)
                <div class="bg-white border border-gray-200 rounded-2xl p-7 card-hover">
                    {{-- Stars --}}
                    <div class="flex items-center gap-1 mb-4">
                        @for($s = 1; $s <= 5; $s++)
                        <svg class="w-4 h-4 {{ $s <= ($testimonial->rating ?? 5) ? 'text-yellow-400 fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    {{-- Quote --}}
                    <blockquote class="text-gray-700 text-sm leading-relaxed mb-6 italic">
                        "{{ $testimonial->content ?? '' }}"
                    </blockquote>
                    {{-- Author --}}
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full overflow-hidden bg-brand-100 shrink-0">
                            @if($testimonial->avatar)
                                <img src="{{ asset('storage/' . $testimonial->avatar) }}" alt="{{ $testimonial->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-brand-600 font-bold text-sm">
                                    {{ strtoupper(substr($testimonial->name ?? 'A', 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 text-sm">{{ $testimonial->name ?? 'Anonymous' }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $testimonial->title ?? '' }}{{ $testimonial->company ? ', ' . $testimonial->company : '' }}
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Mobile Carousel --}}
            <div class="lg:hidden">
                @foreach($testimonials as $i => $testimonial)
                <div x-show="active === {{ $i }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="bg-white border border-gray-200 rounded-2xl p-7">
                        <div class="flex items-center gap-1 mb-4">
                            @for($s = 1; $s <= 5; $s++)
                            <svg class="w-4 h-4 {{ $s <= ($testimonial->rating ?? 5) ? 'text-yellow-400 fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <blockquote class="text-gray-700 text-sm leading-relaxed mb-6 italic">
                            "{{ $testimonial->content ?? '' }}"
                        </blockquote>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full overflow-hidden bg-brand-100 shrink-0">
                                @if($testimonial->avatar)
                                    <img src="{{ asset('storage/' . $testimonial->avatar) }}" alt="{{ $testimonial->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-brand-600 font-bold text-sm">
                                        {{ strtoupper(substr($testimonial->name ?? 'A', 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 text-sm">{{ $testimonial->name ?? 'Anonymous' }}</div>
                                <div class="text-xs text-gray-500">{{ $testimonial->title ?? '' }}{{ $testimonial->company ? ', ' . $testimonial->company : '' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                <div class="flex items-center justify-center gap-4 mt-6">
                    <button @click="prev()" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center hover:bg-brand-50 hover:border-brand-300 transition-colors">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <div class="flex gap-2">
                        @foreach($testimonials as $i => $t)
                        <button @click="active = {{ $i }}" :class="active === {{ $i }} ? 'bg-brand-600 w-6' : 'bg-gray-300 w-2'" class="h-2 rounded-full transition-all duration-300"></button>
                        @endforeach
                    </div>
                    <button @click="next()" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center hover:bg-brand-50 hover:border-brand-300 transition-colors">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-12 text-gray-400">No testimonials yet.</div>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     SHARE YOUR STORY — Public Review Form
═══════════════════════════════════════════════════════════════════ --}}
<section class="py-16 bg-gray-50 border-y border-gray-100">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <span class="inline-block text-brand-600 font-semibold text-sm uppercase tracking-widest mb-3">Your Voice Matters</span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold text-gray-900 mb-3">Share Your Story</h2>
            <p class="text-gray-500">Completed a course? We'd love to hear about your experience.</p>
        </div>

        @if(session('review_success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-2xl px-5 py-4 text-sm text-center">
            {{ session('review_success') }}
        </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-200 p-7 shadow-sm" x-data="{ rating: 5 }">
            <form action="{{ route('review.submit') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Your Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="{{ old('name') }}"
                               class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                               placeholder="Full name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Title / Role</label>
                        <input type="text" name="title" value="{{ old('title') }}"
                               class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                               placeholder="e.g. Software Engineer">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Company / Organisation</label>
                    <input type="text" name="company" value="{{ old('company') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                           placeholder="Where do you work?">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Rating</label>
                    <input type="hidden" name="rating" :value="rating">
                    <div class="flex gap-2">
                        <template x-for="star in [1,2,3,4,5]" :key="star">
                            <button type="button" @click="rating = star"
                                :class="star <= rating ? 'text-amber-400' : 'text-gray-300'"
                                class="text-3xl leading-none hover:text-amber-400 transition-colors">&#9733;</button>
                        </template>
                        <span class="self-center text-sm text-gray-500 ml-1" x-text="rating + ' / 5'"></span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Your Review <span class="text-red-500">*</span></label>
                    <textarea name="content" rows="4" required minlength="20" maxlength="1000"
                              class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none"
                              placeholder="Tell us about your experience with our courses... (min. 20 characters)">{{ old('content') }}</textarea>
                </div>
                <button type="submit"
                        class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 rounded-xl transition-colors text-sm">
                    Submit My Review
                </button>
                <p class="text-xs text-gray-400 text-center">Reviews are reviewed before being published on the site.</p>
            </form>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     FAQ SECTION
═══════════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block text-brand-600 font-semibold text-sm uppercase tracking-widest mb-3">FAQ</span>
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
            <p class="text-gray-500 text-lg">Everything you need to know before enrolling.</p>
        </div>

        @if(isset($faqs) && count($faqs))
        <div class="space-y-3" x-data="{ openFaq: null }">
            @foreach($faqs as $i => $faq)
            <div class="border border-gray-200 rounded-2xl overflow-hidden" x-data>
                <button
                    @click="openFaq === {{ $i }} ? openFaq = null : openFaq = {{ $i }}"
                    class="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-gray-50 transition-colors"
                    :class="openFaq === {{ $i }} ? 'bg-brand-50' : ''"
                >
                    <span class="font-semibold text-gray-900 pr-4">{{ $faq['question'] ?? $faq->question ?? '' }}</span>
                    <div class="shrink-0 w-7 h-7 bg-brand-100 rounded-full flex items-center justify-center transition-transform duration-200"
                         :class="openFaq === {{ $i }} ? 'rotate-45 bg-brand-600' : ''">
                        <svg class="w-3.5 h-3.5 transition-colors" :class="openFaq === {{ $i }} ? 'text-white' : 'text-brand-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                </button>
                <div x-show="openFaq === {{ $i }}" x-collapse x-cloak>
                    <div class="px-6 pb-5 text-gray-600 text-sm leading-relaxed border-t border-gray-100 pt-4">
                        {{ $faq['answer'] ?? $faq->answer ?? '' }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div x-data="{ openFaq: null }" class="space-y-3">
            @php
            $defaultFaqs = [
                ['q' => 'Do I need prior experience to enroll?', 'a' => 'No prior experience is required for beginner courses. We have tracks for all levels — from complete beginners to experienced professionals looking to upskill.'],
                ['q' => 'How long do I have access to the course materials?', 'a' => 'Once enrolled, you have lifetime access to all course materials, including any future updates made to the curriculum.'],
                ['q' => 'Will I receive a certificate upon completion?', 'a' => 'Yes! Upon successfully completing a course, you will receive a verifiable digital certificate that you can share on LinkedIn and with employers.'],
                ['q' => 'What payment methods are accepted?', 'a' => 'We accept card payments (Visa, Mastercard), bank transfers, and mobile money. Installment payment plans are available for select programs.'],
                ['q' => 'Can I switch between online and physical classes?', 'a' => 'For hybrid programs, yes. You can attend live sessions in person or join remotely via our virtual classroom — whichever works best for you.'],
                ['q' => 'Is there a refund policy?', 'a' => 'We offer a 7-day money-back guarantee if you are not satisfied with a course. Physical and cohort-based programs have specific refund terms outlined during enrollment.'],
            ];
            @endphp
            @foreach($defaultFaqs as $i => $faq)
            <div class="border border-gray-200 rounded-2xl overflow-hidden">
                <button @click="openFaq === {{ $i }} ? openFaq = null : openFaq = {{ $i }}"
                        class="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-gray-50 transition-colors"
                        :class="openFaq === {{ $i }} ? 'bg-brand-50' : ''">
                    <span class="font-semibold text-gray-900 pr-4">{{ $faq['q'] }}</span>
                    <div class="shrink-0 w-7 h-7 bg-brand-100 rounded-full flex items-center justify-center transition-transform duration-200"
                         :class="openFaq === {{ $i }} ? 'rotate-45 bg-brand-600' : ''">
                        <svg class="w-3.5 h-3.5 transition-colors" :class="openFaq === {{ $i }} ? 'text-white' : 'text-brand-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                </button>
                <div x-show="openFaq === {{ $i }}" x-cloak>
                    <div class="px-6 pb-5 text-gray-600 text-sm leading-relaxed border-t border-gray-100 pt-4">{{ $faq['a'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <div class="mt-10 text-center">
            <p class="text-gray-500 text-sm mb-4">Still have questions? We're here to help.</p>
            <a href="/contact" class="inline-flex items-center gap-2 text-brand-600 hover:text-brand-700 font-semibold transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Contact Support
            </a>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     BLOG PREVIEW SECTION
═══════════════════════════════════════════════════════════════════ --}}
@if(isset($blogPosts) && $blogPosts->count())
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-14">
            <div>
                <span class="inline-block text-brand-600 font-semibold text-sm uppercase tracking-widest mb-3">Insights</span>
                <h2 class="font-display text-3xl sm:text-4xl font-bold text-gray-900">Latest Insights</h2>
            </div>
            <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 text-brand-600 hover:text-brand-700 font-semibold transition-colors shrink-0">
                View all articles
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($blogPosts->take(3) as $post)
            <article class="group card-hover bg-white rounded-2xl border border-gray-200 overflow-hidden">
                {{-- Image --}}
                <div class="relative aspect-video overflow-hidden bg-gray-200">
                    @if($post->featured_image)
                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-brand-100 to-purple-100 flex items-center justify-center">
                        <svg class="w-12 h-12 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    </div>
                    @endif
                    @if($post->category)
                    <span class="absolute top-3 left-3 bg-brand-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                        {{ $post->category->name }}
                    </span>
                    @endif
                </div>

                {{-- Content --}}
                <div class="p-6">
                    <div class="flex items-center gap-3 text-xs text-gray-400 mb-3">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}
                        </span>
                        @if($post->read_time)
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $post->read_time }} min read
                        </span>
                        @endif
                    </div>
                    <h3 class="font-display font-bold text-gray-900 text-lg leading-snug mb-3 group-hover:text-brand-700 transition-colors line-clamp-2">
                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                    </h3>
                    <p class="text-gray-500 text-sm leading-relaxed line-clamp-3 mb-4">{{ $post->excerpt ?? Str::limit(strip_tags($post->content ?? ''), 120) }}</p>
                    <a href="{{ route('blog.show', $post->slug) }}"
                       class="inline-flex items-center gap-1.5 text-brand-600 hover:text-brand-700 text-sm font-semibold transition-colors">
                        Read Article
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════════════
     GALLERY SECTION
═══════════════════════════════════════════════════════════════════ --}}
@if(isset($galleryItems) && $galleryItems->count())
<section class="py-20 bg-white" x-data="galleryLightbox()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block text-brand-600 font-semibold text-sm uppercase tracking-widest mb-3">Gallery</span>
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Life at MapeLearn</h2>
            <p class="text-gray-500 text-lg max-w-xl mx-auto">Glimpses of our campus, training sessions, and the vibrant community you'll be part of.</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            @foreach($galleryItems->take(12) as $index => $item)
            <button @click="open({{ $index }}, '{{ addslashes($item->image_url) }}', '{{ addslashes($item->caption ?? $item->title ?? '') }}')"
                class="relative group overflow-hidden rounded-2xl aspect-square bg-gray-100 focus:outline-none focus:ring-2 focus:ring-brand-500">
                <img src="{{ $item->image_url }}" alt="{{ $item->alt_text ?? $item->title ?? 'Gallery' }}"
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                @if($item->caption || $item->title)
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                    <p class="text-white text-xs font-medium line-clamp-2">{{ $item->caption ?? $item->title }}</p>
                </div>
                @else
                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <svg class="w-8 h-8 text-white drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                </div>
                @endif
            </button>
            @endforeach
        </div>
    </div>

    {{-- Lightbox --}}
    <div x-show="isOpen" x-cloak x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4"
         @click.self="isOpen = false"
         @keydown.escape.window="isOpen = false"
         @keydown.arrow-left.window="prev()"
         @keydown.arrow-right.window="next()">
        <button @click="isOpen = false" class="absolute top-4 right-4 text-white/70 hover:text-white p-2 rounded-lg hover:bg-white/10 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white p-2 rounded-lg hover:bg-white/10 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white p-2 rounded-lg hover:bg-white/10 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
        <div class="max-w-4xl w-full">
            <img :src="currentSrc" :alt="currentCaption" class="max-h-[80vh] w-full object-contain rounded-xl">
            <p x-show="currentCaption" class="text-white/70 text-sm text-center mt-3" x-text="currentCaption"></p>
        </div>
    </div>
</section>

@push('scripts')
<script>
function galleryLightbox() {
    const images = @json($galleryItems->take(12)->map(fn($i) => ['src' => $i->image_url, 'caption' => $i->caption ?? $i->title ?? ''])->values());
    return {
        isOpen: false,
        currentIndex: 0,
        currentSrc: '',
        currentCaption: '',
        open(index, src, caption) {
            this.currentIndex = index;
            this.currentSrc = src;
            this.currentCaption = caption;
            this.isOpen = true;
        },
        prev() {
            this.currentIndex = (this.currentIndex - 1 + images.length) % images.length;
            this.currentSrc = images[this.currentIndex].src;
            this.currentCaption = images[this.currentIndex].caption;
        },
        next() {
            this.currentIndex = (this.currentIndex + 1) % images.length;
            this.currentSrc = images[this.currentIndex].src;
            this.currentCaption = images[this.currentIndex].caption;
        }
    }
}
</script>
@endpush
@endif

{{-- ═══════════════════════════════════════════════════════════════════
     TEAM SECTION
═══════════════════════════════════════════════════════════════════ --}}
@if(isset($teamMembers) && $teamMembers->count())
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block text-brand-600 font-semibold text-sm uppercase tracking-widest mb-3">Our People</span>
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Meet the Team</h2>
            <p class="text-gray-500 text-lg max-w-xl mx-auto">Industry practitioners and career coaches dedicated to your success.</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($teamMembers as $member)
            <div class="group text-center">
                <div class="relative mb-4 overflow-hidden rounded-2xl aspect-square bg-gray-200">
                    <img src="{{ $member->photo_url }}" alt="{{ $member->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @if($member->linkedin_url || $member->twitter_url)
                    <div class="absolute inset-0 bg-[#14215B]/80 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                        @if($member->linkedin_url)
                        <a href="{{ $member->linkedin_url }}" target="_blank" rel="noopener"
                           class="w-10 h-10 bg-white rounded-xl flex items-center justify-center hover:bg-blue-50 transition-colors">
                            <svg class="w-5 h-5 text-blue-700 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        </a>
                        @endif
                        @if($member->twitter_url)
                        <a href="{{ $member->twitter_url }}" target="_blank" rel="noopener"
                           class="w-10 h-10 bg-white rounded-xl flex items-center justify-center hover:bg-sky-50 transition-colors">
                            <svg class="w-5 h-5 text-sky-500 fill-current" viewBox="0 0 24 24"><path d="M23.954 4.569a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.691 8.094 4.066 6.13 1.64 3.161a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.061a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.937 4.937 0 004.604 3.417 9.868 9.868 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.054 0 13.999-7.496 13.999-13.986 0-.209 0-.42-.015-.63a9.936 9.936 0 002.46-2.548l-.047-.02z"/></svg>
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
                <h3 class="font-display font-bold text-gray-900 text-sm mb-0.5">{{ $member->name }}</h3>
                <p class="text-brand-600 text-xs font-medium">{{ $member->position }}</p>
                @if($member->department)
                <p class="text-gray-400 text-xs mt-0.5">{{ $member->department }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════════════
     ENROLLMENT CTA SECTION
═══════════════════════════════════════════════════════════════════ --}}
<section class="py-24 relative overflow-hidden bg-gradient-to-br from-brand-700 via-brand-600 to-purple-700">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-20 -right-20 w-80 h-80 bg-white rounded-full opacity-5 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-white rounded-full opacity-5 blur-3xl"></div>
        <div class="absolute inset-0 opacity-5" style="background-image: url('data:image/svg+xml,%3Csvg width=\'30\' height=\'30\' viewBox=\'0 0 30 30\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M15 0C6.716 0 0 6.716 0 15c0 8.284 6.716 15 15 15 8.284 0 15-6.716 15-15 0-8.284-6.716-15-15-15zm0 2c7.18 0 13 5.82 13 13S22.18 28 15 28 2 22.18 2 15 7.82 2 15 2z\' fill=\'%23ffffff\' fill-opacity=\'1\' fill-rule=\'evenodd\'/%3E%3C/svg%3E');"></div>
    </div>
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-full px-4 py-1.5 mb-6">
            <svg class="w-4 h-4 text-yellow-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <span class="text-white text-sm font-medium">Join 10,000+ Career Changers</span>
        </div>
        <h2 class="font-display text-4xl sm:text-5xl font-extrabold text-white mb-5 leading-tight">
            Ready to Transform<br>Your Career?
        </h2>
        <p class="text-brand-100 text-xl mb-10 max-w-2xl mx-auto leading-relaxed">
            Start your learning journey today. Enroll in a course, build real skills, and land the job you deserve.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('courses.index') }}"
               class="inline-flex items-center gap-2 bg-white text-brand-700 hover:bg-brand-50 font-bold px-8 py-4 rounded-2xl transition-all duration-200 shadow-lg hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Browse All Courses
            </a>
            <a href="{{ route('auth.register') }}"
               class="inline-flex items-center gap-2 bg-transparent border-2 border-white text-white hover:bg-white hover:text-brand-700 font-bold px-8 py-4 rounded-2xl transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Create Free Account
            </a>
        </div>
        <p class="text-brand-200 text-sm mt-6">No credit card required. 7-day money-back guarantee.</p>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     CONTACT MINI SECTION
═══════════════════════════════════════════════════════════════════ --}}
<section class="py-12 bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
            $contactItems = [
                ['icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'label' => 'Call Us', 'value' => \App\Models\SiteSetting::get('contact_phone', '+234 800 000 0000'), 'href' => 'tel:' . preg_replace('/\s+/', '', \App\Models\SiteSetting::get('contact_phone', '+2348000000000'))],
                ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label' => 'Email Us', 'value' => \App\Models\SiteSetting::get('contact_email', 'hello@mapelearn.com'), 'href' => 'mailto:' . \App\Models\SiteSetting::get('contact_email', 'hello@mapelearn.com')],
                ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Visit Us', 'value' => \App\Models\SiteSetting::get('contact_address', 'Lagos, Nigeria'), 'href' => '#'],
                ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Office Hours', 'value' => 'Mon – Sat, 8am – 6pm', 'href' => '#'],
            ];
            @endphp
            @foreach($contactItems as $item)
            <a href="{{ $item['href'] }}" class="group flex items-center gap-4 p-5 rounded-2xl border border-gray-100 hover:border-brand-200 hover:bg-brand-50 transition-all duration-200">
                <div class="w-11 h-11 bg-brand-100 group-hover:bg-brand-200 rounded-xl flex items-center justify-center shrink-0 transition-colors">
                    <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-0.5">{{ $item['label'] }}</div>
                    <div class="text-sm font-semibold text-gray-800 group-hover:text-brand-700 transition-colors">{{ $item['value'] }}</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Alpine x-intersect polyfill for stats counter
    document.addEventListener('alpine:init', () => {
        if (!window.Alpine.directive('intersect')) {
            Alpine.directive('intersect', (el, { expression }, { evaluateLater, cleanup }) => {
                const evaluate = evaluateLater(expression);
                const observer = new IntersectionObserver(entries => {
                    entries.forEach(entry => { if (entry.isIntersecting) evaluate(); });
                }, { threshold: 0.3 });
                observer.observe(el);
                cleanup(() => observer.disconnect());
            });
        }
    });
</script>
@endpush
