@extends('layouts.admin')

@section('title', 'Mobile App Settings')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-display font-bold text-gray-900">Mobile App Settings</h1>
        <p class="mt-1 text-sm text-gray-500">Configure the branding and identity of the MapeLead mobile app.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-4 py-3">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <form method="POST"
          action="{{ route('admin.mobile-settings.update') }}"
          enctype="multipart/form-data"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 divide-y divide-gray-100">
        @csrf

        {{-- App Logo --}}
        <div class="p-6 space-y-4">
            <div>
                <h2 class="text-sm font-semibold text-gray-800">App Logo</h2>
                <p class="text-xs text-gray-500 mt-0.5">Shown on the app splash screen and login screen. PNG with transparent background recommended.</p>
            </div>

            @php $logoUrl = $settings->get('logo_url'); @endphp
            <div x-data="imagePreview('{{ $logoUrl ?? '' }}')" class="flex items-start gap-5">
                <div class="w-32 h-32 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden shrink-0">
                    <template x-if="preview">
                        <img :src="preview" class="max-w-full max-h-full object-contain p-3" alt="App logo preview">
                    </template>
                    <template x-if="!preview">
                        <div class="text-center p-4">
                            <svg class="w-10 h-10 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18.5A6.5 6.5 0 1112 5.5a6.5 6.5 0 010 13zm0 0v2m0 0H9m3 0h3M9 9l3-3 3 3m-3-3v6"/>
                            </svg>
                            <p class="text-xs text-gray-400 mt-1">No logo</p>
                        </div>
                    </template>
                </div>
                <div class="flex-1 space-y-2">
                    <input type="file"
                           name="logo"
                           accept="image/*"
                           @change="onFileChange($event)"
                           class="block w-full text-sm text-gray-500
                                  file:mr-3 file:py-1.5 file:px-4
                                  file:rounded-lg file:border-0
                                  file:text-sm file:font-medium
                                  file:bg-brand-50 file:text-brand-700
                                  hover:file:bg-brand-100 transition">
                    <p class="text-xs text-gray-400">PNG, JPG or SVG. Max 2 MB. Recommended: 512×512 px.</p>
                    @if($logoUrl)
                        <p class="text-xs text-gray-500">Current logo is set.</p>
                    @endif
                    @error('logo')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- App Name --}}
        <div class="p-6 space-y-3">
            <div>
                <label for="app_name" class="block text-sm font-semibold text-gray-800">App Name</label>
                <p class="text-xs text-gray-500 mt-0.5">Displayed in the app header and splash screen.</p>
            </div>
            <input type="text"
                   id="app_name"
                   name="app_name"
                   value="{{ old('app_name', $settings->get('app_name', 'MapeLead')) }}"
                   placeholder="e.g. MapeLead"
                   class="block w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
            @error('app_name')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tagline --}}
        <div class="p-6 space-y-3">
            <div>
                <label for="tagline" class="block text-sm font-semibold text-gray-800">Tagline</label>
                <p class="text-xs text-gray-500 mt-0.5">Short subtitle shown on the splash/login screen.</p>
            </div>
            <input type="text"
                   id="tagline"
                   name="tagline"
                   value="{{ old('tagline', $settings->get('tagline', 'Learn Today, Build Tomorrow')) }}"
                   placeholder="e.g. Learn Today, Build Tomorrow"
                   class="block w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
            @error('tagline')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Save --}}
        <div class="p-6 flex justify-end">
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-6 py-2.5 rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Save Settings
            </button>
        </div>
    </form>

    {{-- API Info --}}
    <div class="mt-6 bg-blue-50 border border-blue-100 rounded-xl p-4">
        <p class="text-xs text-blue-700">
            <span class="font-semibold">API endpoint:</span>
            <code class="ml-1 bg-blue-100 px-1.5 py-0.5 rounded text-blue-800">GET {{ url('/api/v1/app-settings') }}</code>
            — The mobile app fetches these settings at startup.
        </p>
    </div>

</div>
@endsection
