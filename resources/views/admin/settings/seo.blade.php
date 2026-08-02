@extends('layouts.admin')
@section('title', 'SEO Settings')

@section('content')
<div class="space-y-6">

    <div>
        <h2 class="text-2xl font-bold text-gray-900">SEO Settings</h2>
        <p class="text-sm text-gray-500 mt-1">Control how your site appears in search engines and social media</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.settings.seo.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Meta Tags --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
            <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide border-b border-gray-100 pb-3">Meta Tags</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Default Meta Title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $seo['meta_title'] ?? '') }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                       placeholder="MapeLeads — Tech Training Platform">
                <p class="text-xs text-gray-400 mt-1">Recommended: 50-60 characters. Used when pages don't have their own title.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Default Meta Description</label>
                <textarea name="meta_description" rows="3"
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                          placeholder="Learn in-demand tech skills from industry experts. Enroll in online courses, get certified, and advance your career.">{{ old('meta_description', $seo['meta_description'] ?? '') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Recommended: 150-160 characters.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Meta Keywords</label>
                <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $seo['meta_keywords'] ?? '') }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                       placeholder="tech training, online courses, programming, Nigeria">
            </div>
        </div>

        {{-- Open Graph --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
            <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide border-b border-gray-100 pb-3">Open Graph (Social Sharing)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">OG Title</label>
                    <input type="text" name="og_title" value="{{ old('og_title', $seo['og_title'] ?? '') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">OG Image URL</label>
                    <input type="text" name="og_image" value="{{ old('og_image', $seo['og_image'] ?? '') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                           placeholder="https://...">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">OG Description</label>
                <textarea name="og_description" rows="2"
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">{{ old('og_description', $seo['og_description'] ?? '') }}</textarea>
            </div>
        </div>

        {{-- Analytics --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
            <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide border-b border-gray-100 pb-3">Analytics & Tracking</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Google Analytics ID</label>
                    <input type="text" name="google_analytics_id" value="{{ old('google_analytics_id', $seo['google_analytics_id'] ?? '') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                           placeholder="G-XXXXXXXXXX">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Meta Pixel ID</label>
                    <input type="text" name="meta_pixel_id" value="{{ old('meta_pixel_id', $seo['meta_pixel_id'] ?? '') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                           placeholder="1234567890">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Google Search Console Verification</label>
                <input type="text" name="google_site_verification" value="{{ old('google_site_verification', $seo['google_site_verification'] ?? '') }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                       placeholder="Paste verification meta content value">
            </div>
        </div>

        <div>
            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">
                Save SEO Settings
            </button>
        </div>
    </form>

</div>
@endsection
