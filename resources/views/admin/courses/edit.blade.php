@extends('layouts.admin')
@section('title', 'Edit Course')

@section('content')
<div class="space-y-6" x-data="{ preview: null }">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Course</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $course->title }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.courses.modules.index', $course) }}"
               class="flex items-center gap-2 text-sm bg-indigo-600 text-white px-4 py-2 rounded-xl hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                Curriculum
            </a>
            <a href="{{ route('admin.courses.show', $course->id) }}"
               class="flex items-center gap-2 text-sm text-gray-600 bg-white border border-gray-200 px-4 py-2 rounded-xl hover:text-gray-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')

        {{-- Basic Info --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
            <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide border-b border-gray-100 pb-3">Basic Information</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Course Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $course->title) }}" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Short Description <span class="text-red-500">*</span></label>
                <textarea name="short_description" rows="2" required maxlength="500"
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none">{{ old('short_description', $course->short_description) }}</textarea>
                @error('short_description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Description <span class="text-red-500">*</span></label>
                <textarea name="description" rows="6" required
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">{{ old('description', $course->description) }}</textarea>
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Category <span class="text-red-500">*</span></label>
                    <select name="category_id" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $course->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Instructor <span class="text-red-500">*</span></label>
                    <select name="instructor_id" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" {{ old('instructor_id', $course->instructor_id) == $instructor->id ? 'selected' : '' }}>
                                {{ $instructor->user?->full_name ?? '(No user — ID '.$instructor->id.')' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        @foreach(['online' => 'Online', 'physical' => 'Physical', 'hybrid' => 'Hybrid'] as $val => $label)
                            <option value="{{ $val }}" {{ old('type', $course->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Level <span class="text-red-500">*</span></label>
                    <select name="level" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        @foreach(['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced', 'all_levels' => 'All Levels'] as $val => $label)
                            <option value="{{ $val }}" {{ old('level', $course->level) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Language</label>
                    <input type="text" name="language" value="{{ old('language', $course->language ?? 'English') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                           placeholder="e.g. English">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Duration (hours)</label>
                    <input type="number" name="duration_hours" value="{{ old('duration_hours', $course->duration_hours) }}" min="1"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Duration (weeks)</label>
                    <input type="number" name="duration_weeks" value="{{ old('duration_weeks', $course->duration_weeks) }}" min="1"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <div class="flex flex-wrap gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $course->is_featured) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-brand-600 border-gray-300 focus:ring-brand-500">
                    <span class="text-sm font-medium text-gray-700">Featured course</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="certificate_enabled" value="1" {{ old('certificate_enabled', $course->certificate_enabled ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-brand-600 border-gray-300 focus:ring-brand-500">
                    <span class="text-sm font-medium text-gray-700">Certificate enabled</span>
                </label>
            </div>
        </div>

        {{-- Pricing --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
            <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide border-b border-gray-100 pb-3">Pricing & Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Base Price (₦) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', $course->price) }}" min="0" step="0.01"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <p class="text-xs text-gray-400 mt-1">Fallback when no mode-specific price is set</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Discount Price (₦)</label>
                    <input type="number" name="discount_price" value="{{ old('discount_price', $course->discount_price) }}" min="0" step="0.01"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div class="flex items-end pb-2.5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_free" value="1" {{ old('is_free', $course->is_free) ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-brand-600 border-gray-300 focus:ring-brand-500">
                        <span class="text-sm font-medium text-gray-700">Free course</span>
                    </label>
                </div>
            </div>

            {{-- Per-mode pricing --}}
            <div class="border-t border-gray-100 pt-5">
                <p class="text-sm font-semibold text-gray-800 mb-1">Training Mode Prices <span class="text-xs font-normal text-gray-400">(leave blank to use base price above)</span></p>
                <p class="text-xs text-gray-400 mb-4">Set a different price per training mode. Students will see the price update live when they switch mode on the enrollment page.</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <span class="text-sm font-semibold text-blue-800">Online</span>
                        </div>
                        <input type="number" name="price_online" value="{{ old('price_online', $course->price_online) }}" min="0" step="0.01" placeholder="e.g. 750000"
                               class="w-full border border-blue-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white">
                    </div>
                    <div class="bg-orange-50 rounded-xl p-4 border border-orange-100">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-7 h-7 rounded-lg bg-orange-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <span class="text-sm font-semibold text-orange-800">Physical · 1 Month</span>
                        </div>
                        <input type="number" name="price_physical_monthly" value="{{ old('price_physical_monthly', $course->price_physical_monthly) }}" min="0" step="0.01" placeholder="e.g. 850000"
                               class="w-full border border-orange-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                    </div>
                    <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-7 h-7 rounded-lg bg-green-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <span class="text-sm font-semibold text-green-800">Physical · 3 Months</span>
                        </div>
                        <input type="number" name="price_physical_quarterly" value="{{ old('price_physical_quarterly', $course->price_physical_quarterly) }}" min="0" step="0.01" placeholder="e.g. 950000"
                               class="w-full border border-green-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 bg-white">
                    </div>
                </div>
            </div>
        </div>

        {{-- Installment Options --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4" x-data="installmentEditor({{ json_encode($course->installment_options ?? []) }})">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide">Installment Plans</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Define the payment spread options students can choose. Max 30 days total.</p>
                </div>
                <button type="button" @click="addOption()" class="text-xs font-semibold text-brand-600 hover:text-brand-800 border border-brand-200 px-3 py-1.5 rounded-lg hover:bg-brand-50 transition-colors">
                    + Add Option
                </button>
            </div>

            <div class="space-y-2" x-show="options.length === 0">
                <p class="text-xs text-gray-400 italic">No custom options — default system options will be used. Add options to override.</p>
            </div>

            <div class="space-y-2">
                <template x-for="(opt, i) in options" :key="i">
                    <div class="grid grid-cols-12 gap-2 items-center bg-gray-50 rounded-xl p-3">
                        <div class="col-span-5">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Label shown to student</label>
                            <input type="text" :name="`installment_options[${i}][label]`" x-model="opt.label"
                                   placeholder="e.g. 2 payments · every 2 weeks"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-400">
                        </div>
                        <div class="col-span-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1">No. of payments</label>
                            <input type="number" :name="`installment_options[${i}][count]`" x-model.number="opt.count"
                                   min="2" max="10" placeholder="2"
                                   @change="autoLabel(i)"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-400">
                        </div>
                        <div class="col-span-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Days between each</label>
                            <input type="number" :name="`installment_options[${i}][period_days]`" x-model.number="opt.period_days"
                                   min="1" max="30" placeholder="14"
                                   @change="autoLabel(i)"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-400">
                        </div>
                        <div class="col-span-1 flex items-end justify-center pb-0.5">
                            <button type="button" @click="options.splice(i,1)" class="text-red-400 hover:text-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="col-span-11 text-xs text-gray-400" x-show="opt.count >= 2 && opt.period_days >= 1">
                            Total span: <strong x-text="((opt.count - 1) * opt.period_days) + ' days'"></strong>
                            <span x-show="(opt.count - 1) * opt.period_days > 30" class="text-red-500 font-semibold ml-2">⚠ Exceeds 30 days</span>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Sample presets --}}
            <div class="border-t border-gray-100 pt-3">
                <p class="text-xs font-medium text-gray-500 mb-2">Quick presets:</p>
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="addPreset(2,14)" class="text-xs px-3 py-1 bg-gray-100 hover:bg-brand-50 hover:text-brand-700 rounded-full transition-colors">2 × every 14 days</button>
                    <button type="button" @click="addPreset(3,10)" class="text-xs px-3 py-1 bg-gray-100 hover:bg-brand-50 hover:text-brand-700 rounded-full transition-colors">3 × every 10 days</button>
                    <button type="button" @click="addPreset(4,7)" class="text-xs px-3 py-1 bg-gray-100 hover:bg-brand-50 hover:text-brand-700 rounded-full transition-colors">4 × every 7 days</button>
                    <button type="button" @click="addPreset(2,21)" class="text-xs px-3 py-1 bg-gray-100 hover:bg-brand-50 hover:text-brand-700 rounded-full transition-colors">2 × every 21 days</button>
                    <button type="button" @click="addPreset(3,7)" class="text-xs px-3 py-1 bg-gray-100 hover:bg-brand-50 hover:text-brand-700 rounded-full transition-colors">3 × every 7 days</button>
                </div>
            </div>
        </div>

        {{-- Media --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
            <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide border-b border-gray-100 pb-3">Media</h3>

            {{-- Thumbnail --}}
            <div class="flex items-start gap-6">
                <div class="w-40 h-28 bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center border border-gray-200 shrink-0">
                    <img x-show="preview" :src="preview" class="w-full h-full object-cover" x-cloak>
                    @if($course->thumbnail)
                        <img x-show="!preview" src="{{ $course->thumbnail_url }}" class="w-full h-full object-cover">
                    @else
                        <svg x-show="!preview" class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Course Thumbnail</label>
                    <input type="file" name="thumbnail" accept="image/*"
                           @change="preview = URL.createObjectURL($event.target.files[0])"
                           class="block text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                    <p class="text-xs text-gray-400 mt-2">Leave blank to keep current. JPG, PNG or WebP, max 4MB.</p>
                </div>
            </div>

            {{-- Promo Video: URL or Upload --}}
            <div x-data="{ videoMode: '{{ $course->promo_video ? 'url' : 'url' }}' }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Promo Video</label>
                <div class="flex gap-2 mb-3">
                    <button type="button" @click="videoMode='url'"
                            :class="videoMode==='url' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                            class="px-4 py-1.5 text-xs font-semibold rounded-lg transition-colors">
                        Paste URL
                    </button>
                    <button type="button" @click="videoMode='upload'"
                            :class="videoMode==='upload' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                            class="px-4 py-1.5 text-xs font-semibold rounded-lg transition-colors">
                        Upload Video
                    </button>
                </div>
                <div x-show="videoMode==='url'">
                    <input type="url" name="promo_video" value="{{ old('promo_video', $course->promo_video) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                           placeholder="https://youtube.com/watch?v=... or https://vimeo.com/...">
                    <p class="text-xs text-gray-400 mt-1">YouTube, Vimeo, or any direct video link.</p>
                    @if($course->promo_video)
                        <p class="text-xs text-brand-600 mt-1">Current: <a href="{{ $course->promo_video }}" target="_blank" class="underline break-all">{{ $course->promo_video }}</a></p>
                    @endif
                </div>
                <div x-show="videoMode==='upload'">
                    <input type="file" name="promo_video_file" accept="video/mp4,video/webm,video/ogg"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                    <p class="text-xs text-gray-400 mt-1">MP4, WebM or OGG. Max 200MB. Replaces current video if one exists.</p>
                </div>
            </div>

            {{-- Brochure --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Course Brochure / Prospectus</label>
                @if($course->brochure)
                <div class="mb-2 flex items-center gap-3 p-3 bg-brand-50 border border-brand-100 rounded-xl">
                    <svg class="w-5 h-5 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500">Current brochure:</p>
                        <a href="{{ $course->brochure_url }}" target="_blank" class="text-sm text-brand-700 font-medium hover:underline truncate block">{{ basename($course->brochure) }}</a>
                    </div>
                    <a href="{{ $course->brochure_url }}" target="_blank"
                       class="text-xs text-brand-600 font-semibold px-3 py-1.5 border border-brand-200 rounded-lg hover:bg-brand-100 transition-colors">
                        Preview
                    </a>
                </div>
                @endif
                <input type="file" name="brochure" accept=".pdf,.doc,.docx"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                <p class="text-xs text-gray-400 mt-1">PDF, DOC or DOCX. Max 10MB. Students will see a download button on the course page.{{ $course->brochure ? ' Upload a new file to replace the current one.' : '' }}</p>
            </div>
        </div>

        {{-- Course Content Details --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
            <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide border-b border-gray-100 pb-3">Course Content Details</h3>
            <p class="text-xs text-gray-400 -mt-2">Enter one item per line. These appear on the public course page.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Requirements</label>
                    <textarea name="requirements" rows="5"
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                              placeholder="Basic computer skills&#10;Internet connection&#10;Willingness to learn">{{ old('requirements', is_array($course->requirements) ? implode("\n", $course->requirements) : $course->requirements) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">What You'll Learn</label>
                    <textarea name="what_you_learn" rows="5"
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                              placeholder="Build real-world projects&#10;Understand core concepts&#10;Get industry-ready skills">{{ old('what_you_learn', is_array($course->what_you_learn) ? implode("\n", $course->what_you_learn) : $course->what_you_learn) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Who Is This For</label>
                    <textarea name="who_is_this_for" rows="5"
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                              placeholder="Beginners with no experience&#10;Career changers&#10;Professionals upskilling">{{ old('who_is_this_for', is_array($course->who_is_this_for) ? implode("\n", $course->who_is_this_for) : $course->who_is_this_for) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Curriculum shortcut --}}
        <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-5 flex items-center justify-between gap-4">
            <div>
                <p class="font-semibold text-indigo-900 text-sm">Course Curriculum</p>
                <p class="text-indigo-700 text-xs mt-0.5">
                    {{ $course->modules->count() }} module(s) · {{ $course->modules->sum(fn($m) => $m->lessons->count()) }} lesson(s) total.
                    Add or edit modules and lessons using the Curriculum builder.
                </p>
            </div>
            <a href="{{ route('admin.courses.modules.index', $course) }}"
               class="shrink-0 bg-indigo-600 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-indigo-700 transition-colors">
                Manage Curriculum →
            </a>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">
                Save Changes
            </button>
            <a href="{{ route('admin.courses.show', $course->id) }}" class="text-gray-600 hover:text-gray-900 font-medium px-4 py-2.5 rounded-xl hover:bg-gray-100 transition-colors">
                Cancel
            </a>
        </div>
    </form>

    {{-- Session Instructor Assignment --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide">Session Instructors</h3>
                <p class="text-xs text-gray-500 mt-1">Assign up to 3 instructors — one per session slot — with their scheduled class time.</p>
            </div>
        </div>

        @if(session('success') && str_contains(session('success'), 'Session'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.courses.assign-instructors', $course->id) }}" method="POST" class="space-y-4">
            @csrf

            @php
                $sessionConfig = [
                    'morning'   => ['label' => 'Morning Session',   'icon' => '🌅', 'bg' => 'bg-orange-50 border-orange-200',  'time_range' => '05:00 – 11:59'],
                    'afternoon' => ['label' => 'Afternoon Session',  'icon' => '☀️', 'bg' => 'bg-yellow-50 border-yellow-200',  'time_range' => '12:00 – 16:59'],
                    'evening'   => ['label' => 'Evening Session',    'icon' => '🌙', 'bg' => 'bg-indigo-50 border-indigo-200',  'time_range' => '17:00 – 23:59'],
                ];
            @endphp

            @foreach($sessionConfig as $sessionKey => $cfg)
            @php $existing = $sessionMap[$sessionKey] ?? null; @endphp
            <div class="rounded-xl border {{ $cfg['bg'] }} p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-lg">{{ $cfg['icon'] }}</span>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $cfg['label'] }}</p>
                        <p class="text-xs text-gray-500">Typical hours: {{ $cfg['time_range'] }}</p>
                    </div>
                    @if($existing)
                    <span class="ml-auto inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>Assigned
                    </span>
                    @endif
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Instructor</label>
                        <select name="sessions[{{ $sessionKey }}][instructor_id]"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
                            <option value="">— None (leave slot empty) —</option>
                            @foreach($instructors as $inst)
                            <option value="{{ $inst->id }}" {{ $existing && $existing->instructor_id == $inst->id ? 'selected' : '' }}>
                                {{ $inst->user?->full_name ?? '(ID '.$inst->id.')' }}
                                @if($inst->title) — {{ $inst->title }} @endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Class Time</label>
                        <input type="time" name="sessions[{{ $sessionKey }}][session_time]"
                               value="{{ $existing?->session_time ? substr($existing->session_time, 0, 5) : '' }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white"
                               placeholder="e.g. 09:00">
                        @if($existing && $existing->session_time)
                        <p class="text-xs text-gray-400 mt-1">Current: {{ $existing->formatted_time }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors text-sm">
                    Save Session Assignments
                </button>
                <p class="text-xs text-gray-400">Setting an instructor to "None" will remove that session slot.</p>
            </div>
        </form>
    </div>

    {{-- Danger Zone --}}
    <div class="bg-white rounded-2xl border border-red-200 p-6">
        <h3 class="font-semibold text-red-700 text-sm uppercase tracking-wide mb-3">Danger Zone</h3>
        <p class="text-sm text-gray-600 mb-4">Deleting this course is permanent. All enrollments and progress data will also be removed.</p>
        <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST"
              onsubmit="return confirm('Are you sure you want to permanently delete this course?')">
            @csrf @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors text-sm">
                Delete Course
            </button>
        </form>
    </div>

</div>

@push('scripts')
<script>
function installmentEditor(existing) {
    return {
        options: existing.length ? existing : [],
        addOption() {
            this.options.push({ label: '', count: 2, period_days: 14 });
        },
        addPreset(count, days) {
            const label = `${count} payments · every ${days} days (${(count - 1) * days} days total)`;
            const exists = this.options.some(o => o.count === count && o.period_days === days);
            if (!exists) this.options.push({ label, count, period_days: days });
        },
        autoLabel(i) {
            const o = this.options[i];
            if (o.count >= 2 && o.period_days >= 1) {
                o.label = `${o.count} payments · every ${o.period_days} days (${(o.count - 1) * o.period_days} days total)`;
            }
        },
    };
}
</script>
@endpush
@endsection
