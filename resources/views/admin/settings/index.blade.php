@extends('layouts.admin')

@section('title', 'Settings')

@section('content')

@php $activeTab = old('_tab', request('tab', $groups[0] ?? 'general')); @endphp
<div class="space-y-5">

    {{-- ═══════════════════════════════════════════════════════════════
         PAGE HEADER
    ═══════════════════════════════════════════════════════════════ --}}
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Settings</h2>
        <p class="text-sm text-gray-500 mt-0.5">Configure site-wide settings by group.</p>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         TAB NAVIGATION
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex overflow-x-auto scrollbar-hide px-2 pt-2 gap-1"
                 aria-label="Settings tabs">
                @php
                    $tabIcons = [
                        'general'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
                        'homepage' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
                        'social'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>',
                        'seo'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>',
                        'footer'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>',
                        'integrations'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
                    ];
                    $tabLabels = [
                        'general'      => 'General',
                        'homepage'     => 'Homepage',
                        'social'       => 'Social',
                        'seo'          => 'SEO',
                        'footer'       => 'Footer',
                        'integrations' => 'Integrations',
                    ];
                @endphp

                @foreach($groups as $group)
                    <button type="button"
                            id="settings-tab-btn-{{ $group }}"
                            onclick="settingsSwitchTab('{{ $group }}')"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 border-b-2 text-sm font-medium whitespace-nowrap transition-colors rounded-t-lg -mb-px
                                   {{ $activeTab === $group ? 'border-[#14215B] text-[#14215B] bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $tabIcons[$group] ?? $tabIcons['general'] !!}
                        </svg>
                        {{ $tabLabels[$group] ?? ucfirst($group) }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             TAB PANELS
        ═══════════════════════════════════════════════════════════ --}}

        {{-- ─── GENERAL ─────────────────────────────────────────── --}}
        <div id="settings-panel-general" style="{{ $activeTab === 'general' ? 'display:block' : 'display:none' }}">
            <form method="POST"
                  action="{{ route('admin.settings.update', 'general') }}"
                  enctype="multipart/form-data"
                  class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="_tab" value="general">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Site Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Site Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="site_name"
                               value="{{ old('site_name', $all['general']['site_name'] ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                               placeholder="MapeLearn">
                    </div>

                    {{-- Site Tagline --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tagline</label>
                        <input type="text"
                               name="site_tagline"
                               value="{{ old('site_tagline', $all['general']['site_tagline'] ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                               placeholder="Africa's Premier Tech Training Platform">
                    </div>

                    {{-- Contact Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Contact Email</label>
                        <input type="email"
                               name="site_email"
                               value="{{ old('site_email', $all['general']['site_email'] ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                               placeholder="hello@mapelead.org">
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                        <input type="text"
                               name="site_phone"
                               value="{{ old('site_phone', $all['general']['site_phone'] ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                               placeholder="+234 800 000 0000">
                    </div>

                    {{-- Address --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
                        <input type="text"
                               name="site_address"
                               value="{{ old('site_address', $all['general']['site_address'] ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                               placeholder="Lagos, Nigeria">
                    </div>

                    {{-- Contact Info (used in homepage contact section) --}}
                    <div class="md:col-span-2">
                        <div class="border-t border-gray-100 pt-4 mb-2">
                            <p class="text-sm font-semibold text-gray-700">Contact Information <span class="text-xs font-normal text-gray-400">(displayed on homepage contact section)</span></p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Contact Phone</label>
                        <input type="text"
                               name="contact_phone"
                               value="{{ old('contact_phone', $all['general']['contact_phone'] ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                               placeholder="+234 800 000 0000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Contact Email</label>
                        <input type="email"
                               name="contact_email"
                               value="{{ old('contact_email', $all['general']['contact_email'] ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                               placeholder="hello@mapelearn.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Contact Address</label>
                        <input type="text"
                               name="contact_address"
                               value="{{ old('contact_address', $all['general']['contact_address'] ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                               placeholder="Lagos, Nigeria">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Office Hours</label>
                        <input type="text"
                               name="contact_office_hours"
                               value="{{ old('contact_office_hours', $all['general']['contact_office_hours'] ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                               placeholder="Mon – Sat, 8am – 6pm">
                    </div>

                    {{-- Currency --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Currency Code</label>
                        <input type="text"
                               name="site_currency"
                               value="{{ old('site_currency', $all['general']['site_currency'] ?? 'NGN') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                               placeholder="NGN">
                    </div>

                    {{-- Currency Symbol --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Currency Symbol</label>
                        <input type="text"
                               name="currency_symbol"
                               value="{{ old('currency_symbol', $all['general']['currency_symbol'] ?? '₦') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                               placeholder="₦">
                    </div>

                    {{-- Logo Upload --}}
                    <div x-data="imagePreview('{{ $all['general']['site_logo'] ?? '' }}')">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Site Logo</label>
                        <div class="flex items-start gap-4">
                            <div class="w-20 h-20 rounded-xl border-2 border-dashed border-gray-200 bg-gray-50
                                        flex items-center justify-center overflow-hidden shrink-0 relative">
                                <template x-if="preview">
                                    <img :src="preview" class="w-full h-full object-contain p-1" alt="Logo preview">
                                </template>
                                <template x-if="!preview">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </template>
                            </div>
                            <div class="flex-1">
                                <input type="file"
                                       name="site_logo"
                                       accept="image/*"
                                       @change="onFileChange($event)"
                                       class="block w-full text-xs text-gray-500
                                              file:mr-3 file:py-1.5 file:px-3
                                              file:rounded-lg file:border-0
                                              file:text-xs file:font-medium
                                              file:bg-brand-50 file:text-brand-700
                                              hover:file:bg-brand-100 transition">
                                <p class="text-xs text-gray-400 mt-1">PNG, SVG or JPG, max 2MB. Recommended: 200×60px.</p>
                                @if(!empty($all['general']['site_logo']))
                                    <p class="text-xs text-gray-400 mt-1">
                                        Current: <span class="text-gray-500">{{ basename($all['general']['site_logo']) }}</span>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Favicon Upload --}}
                    <div x-data="imagePreview('{{ $all['general']['site_favicon'] ?? '' }}')">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Favicon</label>
                        <div class="flex items-start gap-4">
                            <div class="w-20 h-20 rounded-xl border-2 border-dashed border-gray-200 bg-gray-50
                                        flex items-center justify-center overflow-hidden shrink-0">
                                <template x-if="preview">
                                    <img :src="preview" class="w-10 h-10 object-contain" alt="Favicon preview">
                                </template>
                                <template x-if="!preview">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                                    </svg>
                                </template>
                            </div>
                            <div class="flex-1">
                                <input type="file"
                                       name="site_favicon"
                                       accept="image/*,.ico"
                                       @change="onFileChange($event)"
                                       class="block w-full text-xs text-gray-500
                                              file:mr-3 file:py-1.5 file:px-3
                                              file:rounded-lg file:border-0
                                              file:text-xs file:font-medium
                                              file:bg-brand-50 file:text-brand-700
                                              hover:file:bg-brand-100 transition">
                                <p class="text-xs text-gray-400 mt-1">ICO or PNG, max 512KB. Recommended: 32×32px.</p>
                                @if(!empty($all['general']['site_favicon']))
                                    <p class="text-xs text-gray-400 mt-1">
                                        Current: <span class="text-gray-500">{{ basename($all['general']['site_favicon']) }}</span>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                <div class="flex justify-end pt-2 border-t border-gray-100">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white
                                   text-sm font-medium px-5 py-2 rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save General Settings
                    </button>
                </div>
            </form>
        </div>

        {{-- ─── HOMEPAGE ────────────────────────────────────────── --}}
        <div id="settings-panel-homepage" style="{{ $activeTab === 'homepage' ? 'display:block' : 'display:none' }}">
            <form method="POST"
                  action="{{ route('admin.settings.update', 'homepage') }}"
                  class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="_tab" value="homepage">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Hero Badge Text</label>
                        <input type="text"
                               name="hero_badge"
                               value="{{ old('hero_badge', $all['homepage']['hero_badge'] ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                               placeholder="#1 Tech Training in Africa">
                        <p class="text-xs text-gray-400 mt-1">Displayed as a badge above the hero title.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Hero Primary CTA</label>
                        <input type="text"
                               name="hero_cta_primary"
                               value="{{ old('hero_cta_primary', $all['homepage']['hero_cta_primary'] ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                               placeholder="Explore Courses">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Hero Title</label>
                        <input type="text"
                               name="hero_title"
                               value="{{ old('hero_title', $all['homepage']['hero_title'] ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                               placeholder="Build In-Demand Tech Skills That Get You Hired">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Hero Subtitle</label>
                        <textarea name="hero_subtitle"
                                  rows="3"
                                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                         focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none
                                         transition resize-none"
                                  placeholder="Join 10,000+ graduates who transformed their careers…">{{ old('hero_subtitle', $all['homepage']['hero_subtitle'] ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Hero Secondary CTA</label>
                        <input type="text"
                               name="hero_cta_secondary"
                               value="{{ old('hero_cta_secondary', $all['homepage']['hero_cta_secondary'] ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                               placeholder="Download Brochure">
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Hero Stats</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach(['stat_students' => 'Students Count', 'stat_courses' => 'Courses Count', 'stat_instructors' => 'Instructors Count', 'stat_placement' => 'Job Placement Rate'] as $key => $label)
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ $label }}</label>
                                    <input type="text"
                                           name="{{ $key }}"
                                           value="{{ old($key, $all['homepage'][$key] ?? '') }}"
                                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                                  focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                           placeholder="e.g. 10,000+">
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="flex justify-end pt-2 border-t border-gray-100">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white
                                   text-sm font-medium px-5 py-2 rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Homepage Settings
                    </button>
                </div>
            </form>
        </div>

        {{-- ─── SOCIAL ──────────────────────────────────────────── --}}
        <div id="settings-panel-social" style="{{ $activeTab === 'social' ? 'display:block' : 'display:none' }}">
            <form method="POST"
                  action="{{ route('admin.settings.update', 'social') }}"
                  class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="_tab" value="social">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    @php
                        $socialFields = [
                            'facebook_url'  => ['label' => 'Facebook URL',    'placeholder' => 'https://facebook.com/mapelead',   'color' => 'text-blue-600',   'icon' => 'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z'],
                            'twitter_url'   => ['label' => 'Twitter / X URL', 'placeholder' => 'https://twitter.com/mapelead',    'color' => 'text-sky-500',    'icon' => 'M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84'],
                            'instagram_url' => ['label' => 'Instagram URL',   'placeholder' => 'https://instagram.com/mapelead',  'color' => 'text-pink-600',   'icon' => 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z'],
                            'linkedin_url'  => ['label' => 'LinkedIn URL',    'placeholder' => 'https://linkedin.com/company/mapelead', 'color' => 'text-blue-700', 'icon' => 'M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z M4 6a2 2 0 100-4 2 2 0 000 4z'],
                            'youtube_url'   => ['label' => 'YouTube URL',     'placeholder' => 'https://youtube.com/@mapelead',    'color' => 'text-red-600',    'icon' => 'M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58 2.78 2.78 0 001.95 1.95C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.95A29 29 0 0023 12a29 29 0 00-.46-5.58zM9.75 15.02V8.98L15.5 12l-5.75 3.02z'],
                            'whatsapp_number' => ['label' => 'WhatsApp Number', 'placeholder' => '2348000000000', 'color' => 'text-green-600', 'icon' => 'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z M11.999 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.975-1.418A9.949 9.949 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z'],
                        ];
                    @endphp

                    @foreach($socialFields as $key => $field)
                        <div>
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-1.5">
                                <svg class="w-4 h-4 {{ $field['color'] }}" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="{{ $field['icon'] }}"/>
                                </svg>
                                {{ $field['label'] }}
                            </label>
                            <input type="url"
                                   name="{{ $key }}"
                                   value="{{ old($key, $all['social'][$key] ?? '') }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                          focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                   placeholder="{{ $field['placeholder'] }}">
                        </div>
                    @endforeach

                </div>

                <div class="flex justify-end pt-2 border-t border-gray-100">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white
                                   text-sm font-medium px-5 py-2 rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Social Settings
                    </button>
                </div>
            </form>
        </div>

        {{-- ─── SEO ─────────────────────────────────────────────── --}}
        <div id="settings-panel-seo" style="{{ $activeTab === 'seo' ? 'display:block' : 'display:none' }}">
            <form method="POST"
                  action="{{ route('admin.settings.update', 'seo') }}"
                  class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="_tab" value="seo">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Default Meta Title --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Default Meta Title
                        </label>
                        <input type="text"
                               name="meta_title"
                               value="{{ old('meta_title', $all['seo']['meta_title'] ?? '') }}"
                               maxlength="70"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                               placeholder="MapeLearn – Africa's Premier Tech Training Platform">
                        <p class="text-xs text-gray-400 mt-1">Recommended: 50–60 characters. Used on pages without a specific title.</p>
                    </div>

                    {{-- Default Meta Description --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Default Meta Description
                        </label>
                        <textarea name="meta_description"
                                  rows="3"
                                  maxlength="165"
                                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                         focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none
                                         transition resize-none"
                                  placeholder="Join 10,000+ students learning Data Science, Cybersecurity…">{{ old('meta_description', $all['seo']['meta_description'] ?? '') }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Recommended: 120–155 characters.</p>
                    </div>

                    {{-- Google Analytics --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Google Analytics ID
                            <span class="text-xs text-gray-400 font-normal">(GA4)</span>
                        </label>
                        <input type="text"
                               name="google_analytics"
                               value="{{ old('google_analytics', $all['seo']['google_analytics'] ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition
                                      font-mono"
                               placeholder="G-XXXXXXXXXX">
                        <p class="text-xs text-gray-400 mt-1">Format: G-XXXXXXXXXX</p>
                    </div>

                    {{-- Meta Pixel --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Meta Pixel ID
                            <span class="text-xs text-gray-400 font-normal">(Facebook)</span>
                        </label>
                        <input type="text"
                               name="meta_pixel"
                               value="{{ old('meta_pixel', $all['seo']['meta_pixel'] ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition
                                      font-mono"
                               placeholder="123456789012345">
                        <p class="text-xs text-gray-400 mt-1">Numeric Pixel ID only.</p>
                    </div>

                    {{-- Render any extra SEO settings dynamically --}}
                    @foreach($all['seo'] as $key => $value)
                        @if(!in_array($key, ['meta_title','meta_description','google_analytics','meta_pixel']))
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    {{ ucwords(str_replace('_', ' ', $key)) }}
                                </label>
                                <input type="text"
                                       name="{{ $key }}"
                                       value="{{ old($key, $value) }}"
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                              focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
                            </div>
                        @endif
                    @endforeach

                </div>

                <div class="flex justify-end pt-2 border-t border-gray-100">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white
                                   text-sm font-medium px-5 py-2 rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save SEO Settings
                    </button>
                </div>
            </form>
        </div>

        {{-- ─── FOOTER ──────────────────────────────────────────── --}}
        <div id="settings-panel-footer" style="{{ $activeTab === 'footer' ? 'display:block' : 'display:none' }}">

            {{-- Logo upload form (saves to general group, key=site_logo) --}}
            <form method="POST"
                  action="{{ route('admin.settings.update', 'general') }}"
                  enctype="multipart/form-data"
                  class="p-6 pb-4 border-b border-gray-100 space-y-4">
                @csrf
                <input type="hidden" name="_tab" value="footer">
                <h3 class="text-sm font-semibold text-gray-700">Footer Logo</h3>
                <div x-data="imagePreview('{{ !empty($all['general']['site_logo']) ? asset(\'storage/\' . $all[\'general\'][\'site_logo\']) : \'\' }}')" class="flex items-start gap-6">
                    <div class="shrink-0 w-40 h-16 rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden">
                        <template x-if="preview">
                            <img :src="preview" class="max-w-full max-h-full object-contain p-2" alt="Logo preview">
                        </template>
                        <template x-if="!preview">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </template>
                    </div>
                    <div class="flex-1 space-y-2">
                        <input type="file" name="site_logo" accept="image/*" @change="onFileChange($event)"
                               class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 transition">
                        <p class="text-xs text-gray-400">PNG, SVG or JPG, max 2 MB. Recommended: 200×60 px.</p>
                        @if(!empty($all['general']['site_logo']))
                            <p class="text-xs text-gray-400">Current: <span class="text-gray-500">{{ basename($all['general']['site_logo']) }}</span></p>
                        @endif
                        <button type="submit" class="inline-flex items-center gap-1.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-medium px-4 py-1.5 rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Upload Logo
                        </button>
                    </div>
                </div>
            </form>

            <form method="POST"
                  action="{{ route('admin.settings.update', 'general') }}"
                  enctype="multipart/form-data"
                  class="p-6 pb-0 space-y-6 border-b border-gray-100">
                @csrf
                <input type="hidden" name="_tab" value="footer">

                <div>
                    <h3 class="text-sm font-semibold text-gray-800 mb-1">Footer Logo</h3>
                    <p class="text-xs text-gray-500 mb-4">This logo appears in the footer of every page. Recommended size: 200×60px, PNG or SVG with transparent background.</p>
                </div>

                <div x-data="imagePreview('{{ $all['general']['site_logo'] ?? '' }}')" class="flex items-start gap-5 pb-6">
                    <div class="w-40 h-16 rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden shrink-0">
                        <template x-if="preview">
                            <img :src="preview" class="max-w-full max-h-full object-contain p-2" alt="Logo preview">
                        </template>
                        <template x-if="!preview">
                            <div class="text-center">
                                <svg class="w-8 h-8 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-xs text-gray-400 mt-1">No logo</p>
                            </div>
                        </template>
                    </div>
                    <div class="flex-1">
                        <input type="file"
                               name="site_logo"
                               accept="image/*"
                               @change="onFileChange($event)"
                               class="block w-full text-sm text-gray-500
                                      file:mr-3 file:py-1.5 file:px-4
                                      file:rounded-lg file:border-0
                                      file:text-sm file:font-medium
                                      file:bg-brand-50 file:text-brand-700
                                      hover:file:bg-brand-100 transition">
                        <p class="text-xs text-gray-400 mt-2">PNG, SVG or JPG, max 2MB.</p>
                        @if(!empty($all['general']['site_logo']))
                        <p class="text-xs text-gray-500 mt-1">Current file: <span class="font-mono">{{ basename($all['general']['site_logo']) }}</span></p>
                        @endif
                        <button type="submit"
                                class="mt-3 inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Upload Logo
                        </button>
                    </div>
                </div>
            </form>

            <form method="POST"
                  action="{{ route('admin.settings.update', 'footer') }}"
                  class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="_tab" value="footer">

                {{-- Logo height slider --}}
                <div x-data="{ logoHeight: {{ $all['footer']['footer_logo_height'] ?? 48 }} }">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Footer Logo Height
                        <span class="text-gray-400 font-normal text-xs ml-1">(<span x-text="logoHeight"></span> px)</span>
                    </label>
                    <div class="flex items-center gap-4">
                        <span class="text-xs text-gray-400 shrink-0">24 px</span>
                        <input type="range" name="footer_logo_height"
                               min="24" max="120" step="4"
                               :value="logoHeight"
                               @input="logoHeight = $event.target.value"
                               class="flex-1 accent-brand-600">
                        <span class="text-xs text-gray-400 shrink-0">120 px</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Drag the slider to resize the footer logo. Default is 48 px.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Footer About Text</label>
                        <textarea name="footer_about"
                                  rows="3"
                                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                         focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none
                                         transition resize-none"
                                  placeholder="Short description displayed in the footer…">{{ old('footer_about', $all['footer']['footer_about'] ?? '') }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Copyright Text</label>
                        <input type="text"
                               name="footer_copyright"
                               value="{{ old('footer_copyright', $all['footer']['footer_copyright'] ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                               placeholder="© 2025 MapeLearn. All rights reserved.">
                    </div>

                    {{-- Render remaining footer settings dynamically --}}
                    @foreach($all['footer'] as $key => $value)
                        @if(!in_array($key, ['footer_about','footer_copyright','footer_logo_height']))
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    {{ ucwords(str_replace('_', ' ', $key)) }}
                                </label>
                                @php $isLong = strlen($value) > 100; @endphp
                                @if($isLong)
                                    <textarea name="{{ $key }}"
                                              rows="3"
                                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                                     focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none
                                                     transition resize-none">{{ old($key, $value) }}</textarea>
                                @else
                                    <input type="text"
                                           name="{{ $key }}"
                                           value="{{ old($key, $value) }}"
                                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                                  focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
                                @endif
                            </div>
                        @endif
                    @endforeach

                </div>

                <div class="flex justify-end pt-2 border-t border-gray-100">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white
                                   text-sm font-medium px-5 py-2 rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Footer Settings
                    </button>
                </div>
            </form>
        </div>

        {{-- ─── INTEGRATIONS ────────────────────────────────────── --}}
        <div id="settings-panel-integrations" style="{{ $activeTab === 'integrations' ? 'display:block' : 'display:none' }}">
            <form method="POST"
                  action="{{ route('admin.settings.integrations') }}"
                  class="p-6 space-y-8">
                @csrf
                <input type="hidden" name="_tab" value="integrations">

                {{-- Warning Banner --}}
                <div class="flex items-start gap-3 bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <svg class="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-sm text-yellow-700">
                        <span class="font-semibold">Warning:</span> These settings write directly to your <code class="font-mono bg-yellow-100 px-1 rounded">.env</code> file. Changes take effect immediately. Keep a backup before editing.
                    </p>
                </div>

                {{-- ── Paystack Section ────────────────────────────────── --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-md bg-[#14215B] flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M2 6h20v4H2zm0 6h20v6H2z"/></svg>
                        </span>
                        Paystack Payments
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Public Key</label>
                            <input type="text"
                                   name="paystack_public_key"
                                   value="{{ old('paystack_public_key', env('PAYSTACK_PUBLIC_KEY', '')) }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono
                                          focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                   placeholder="pk_live_... or pk_test_...">
                            <p class="text-xs text-gray-400 mt-1">Your Paystack public key (safe to expose in frontend).</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Secret Key</label>
                            <input type="password"
                                   name="paystack_secret_key"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono
                                          focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                   placeholder="sk_live_... or sk_test_...">
                            <p class="text-xs text-gray-400 mt-1">Leave blank to keep current value unchanged.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Webhook Secret</label>
                            <input type="text"
                                   name="paystack_webhook_secret"
                                   value="{{ old('paystack_webhook_secret', env('PAYSTACK_WEBHOOK_SECRET', '')) }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono
                                          focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                   placeholder="Paystack webhook secret">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- ── Email / SMTP Section ────────────────────────────── --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-md bg-[#14215B] flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        Email / SMTP
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Mail Driver</label>
                            <select name="mail_mailer"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                           focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition bg-white">
                                @foreach(['log' => 'Log (local dev)', 'smtp' => 'SMTP', 'mailgun' => 'Mailgun', 'ses' => 'Amazon SES'] as $val => $label)
                                    <option value="{{ $val }}" {{ env('MAIL_MAILER') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">SMTP Host</label>
                            <input type="text"
                                   name="mail_host"
                                   value="{{ old('mail_host', env('MAIL_HOST', '')) }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                          focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                   placeholder="smtp.mailgun.org">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">SMTP Port</label>
                            <input type="number"
                                   name="mail_port"
                                   value="{{ old('mail_port', env('MAIL_PORT', '587')) }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                          focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                   placeholder="587">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Encryption</label>
                            <select name="mail_encryption"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                           focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition bg-white">
                                @foreach(['null' => 'None', 'tls' => 'TLS', 'ssl' => 'SSL'] as $val => $label)
                                    <option value="{{ $val }}" {{ env('MAIL_ENCRYPTION') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">SMTP Username</label>
                            <input type="text"
                                   name="mail_username"
                                   value="{{ old('mail_username', env('MAIL_USERNAME', '')) }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                          focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                   placeholder="SMTP username or API key">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">SMTP Password</label>
                            <input type="password"
                                   name="mail_password"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                          focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                   placeholder="Leave blank to keep current value">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">From Address</label>
                            <input type="email"
                                   name="mail_from_address"
                                   value="{{ old('mail_from_address', env('MAIL_FROM_ADDRESS', '')) }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                          focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                   placeholder="noreply@mapelead.org">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">From Name</label>
                            <input type="text"
                                   name="mail_from_name"
                                   value="{{ old('mail_from_name', env('MAIL_FROM_NAME', '')) }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                          focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition"
                                   placeholder="MapeLearn">
                        </div>

                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-gray-100">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white
                                   text-sm font-medium px-5 py-2 rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Integration Settings
                    </button>
                </div>
            </form>
        </div>

        {{-- ─── ANY EXTRA GROUPS (dynamic fallback) ─────────────── --}}
        @foreach($groups as $group)
            @if(!in_array($group, ['general','homepage','social','seo','footer','integrations']))
                <div id="settings-panel-{{ $group }}" style="{{ $activeTab === $group ? 'display:block' : 'display:none' }}">
                    <form method="POST"
                          action="{{ route('admin.settings.update', $group) }}"
                          class="p-6 space-y-6">
                        @csrf
                        <input type="hidden" name="_tab" value="{{ $group }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($all[$group] ?? [] as $key => $value)
                                <div @if(strlen($value) > 100) class="md:col-span-2" @endif>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                        {{ ucwords(str_replace('_', ' ', $key)) }}
                                    </label>
                                    @if(strlen($value) > 100)
                                        <textarea name="{{ $key }}"
                                                  rows="3"
                                                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                                         focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none
                                                         transition resize-none">{{ old($key, $value) }}</textarea>
                                    @else
                                        <input type="text"
                                               name="{{ $key }}"
                                               value="{{ old($key, $value) }}"
                                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-end pt-2 border-t border-gray-100">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white
                                           text-sm font-medium px-5 py-2 rounded-lg transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save {{ ucfirst($group) }} Settings
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        @endforeach

    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         QUICK LINKS
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('admin.settings.scripts') }}"
           class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm hover:border-brand-300 hover:shadow-md transition group flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-gray-100 group-hover:bg-brand-50 flex items-center justify-center shrink-0 transition-colors">
                <svg class="w-5 h-5 text-gray-500 group-hover:text-brand-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-800">Script Injections</p>
                <p class="text-xs text-gray-400">GA4, Meta Pixel, Tawk.to…</p>
            </div>
        </a>
        <a href="{{ route('admin.settings.seo') }}"
           class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm hover:border-brand-300 hover:shadow-md transition group flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-gray-100 group-hover:bg-brand-50 flex items-center justify-center shrink-0 transition-colors">
                <svg class="w-5 h-5 text-gray-500 group-hover:text-brand-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-800">SEO Settings</p>
                <p class="text-xs text-gray-400">Meta tags, sitemaps…</p>
            </div>
        </a>
        <a href="{{ route('admin.settings.menus') }}"
           class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm hover:border-brand-300 hover:shadow-md transition group flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-gray-100 group-hover:bg-brand-50 flex items-center justify-center shrink-0 transition-colors">
                <svg class="w-5 h-5 text-gray-500 group-hover:text-brand-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-800">Navigation Menus</p>
                <p class="text-xs text-gray-400">Header, footer menus…</p>
            </div>
        </a>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Settings tab switcher — no Alpine dependency needed
    function settingsSwitchTab(tab) {
        document.querySelectorAll('[id^="settings-panel-"]').forEach(function(el) {
            el.style.display = 'none';
        });
        var panel = document.getElementById('settings-panel-' + tab);
        if (panel) panel.style.display = 'block';

        document.querySelectorAll('[id^="settings-tab-btn-"]').forEach(function(btn) {
            btn.classList.remove('border-[#14215B]', 'text-[#14215B]', 'bg-blue-50');
            btn.classList.add('border-transparent', 'text-gray-500');
        });
        var btn = document.getElementById('settings-tab-btn-' + tab);
        if (btn) {
            btn.classList.remove('border-transparent', 'text-gray-500');
            btn.classList.add('border-[#14215B]', 'text-[#14215B]', 'bg-blue-50');
        }
        var url = new URL(window.location.href);
        url.searchParams.set('tab', tab);
        window.history.replaceState({}, '', url);
    }

    // Image upload preview (Alpine component)
    function imagePreview(existingPath) {
        return {
            preview: existingPath ? `/storage/${existingPath}` : null,
            onFileChange(event) {
                const file = event.target.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = (e) => { this.preview = e.target.result; };
                reader.readAsDataURL(file);
            }
        };
    }
</script>
@endpush
