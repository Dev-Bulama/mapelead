@extends('layouts.admin')
@section('title', 'Create Course')

@section('content')
<div class="space-y-6" x-data="{ preview: null, isFree: false }">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Create Course</h2>
            <p class="text-sm text-gray-500 mt-1">Add a new course to the platform</p>
        </div>
        <a href="{{ route('admin.courses.index') }}"
           class="flex items-center gap-2 text-sm text-gray-600 bg-white border border-gray-200 px-4 py-2 rounded-xl hover:text-gray-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Basic Info --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
            <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide border-b border-gray-100 pb-3">Basic Information</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Course Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                       placeholder="e.g. Complete Web Development Bootcamp">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Short Description <span class="text-red-500">*</span></label>
                <textarea name="short_description" rows="2" required maxlength="500"
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"
                          placeholder="A brief one-liner shown in course cards (max 500 chars)">{{ old('short_description') }}</textarea>
                @error('short_description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Description <span class="text-red-500">*</span></label>
                <textarea name="description" rows="6" required
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                          placeholder="Detailed course description...">{{ old('description') }}</textarea>
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Category <span class="text-red-500">*</span></label>
                    <select name="category_id" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">Select category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Instructor <span class="text-red-500">*</span></label>
                    <select name="instructor_id" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">Select instructor</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                {{ $instructor->user?->full_name ?? '(No user — ID '.$instructor->id.')' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        @foreach(['online' => 'Online', 'physical' => 'Physical', 'hybrid' => 'Hybrid'] as $val => $label)
                            <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Level <span class="text-red-500">*</span></label>
                    <select name="level" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        @foreach(['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced', 'all_levels' => 'All Levels'] as $val => $label)
                            <option value="{{ $val }}" {{ old('level') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Language</label>
                    <input type="text" name="language" value="{{ old('language', 'English') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                           placeholder="e.g. English">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Duration (hours)</label>
                    <input type="number" name="duration_hours" value="{{ old('duration_hours') }}" min="1"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Duration (weeks)</label>
                    <input type="number" name="duration_weeks" value="{{ old('duration_weeks') }}" min="1"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <div class="flex flex-wrap gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-brand-600 border-gray-300 focus:ring-brand-500">
                    <span class="text-sm font-medium text-gray-700">Featured course</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="certificate_enabled" value="1" checked
                           class="w-4 h-4 rounded text-brand-600 border-gray-300 focus:ring-brand-500">
                    <span class="text-sm font-medium text-gray-700">Certificate enabled</span>
                </label>
            </div>
        </div>

        {{-- Pricing --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
            <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide border-b border-gray-100 pb-3">Pricing</h3>

            <div class="flex items-center gap-3 p-3 bg-brand-50 rounded-xl">
                <input type="checkbox" name="is_free" id="is_free" value="1" x-model="isFree" class="rounded text-brand-600" {{ old('is_free') ? 'checked' : '' }}>
                <label for="is_free" class="text-sm font-medium text-brand-700 cursor-pointer">Make this course free</label>
            </div>

            <div x-show="!isFree" class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Base Price (₦) <span class="text-red-500">*</span></label>
                        <input type="number" name="price" value="{{ old('price', 0) }}" min="0" step="0.01"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <p class="text-xs text-gray-400 mt-1">Fallback if no mode-specific price is set</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Discount Price (₦)</label>
                        <input type="number" name="discount_price" value="{{ old('discount_price') }}" min="0" step="0.01"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>

                {{-- Per-mode pricing --}}
                <div class="border-t border-gray-100 pt-5">
                    <p class="text-sm font-semibold text-gray-800 mb-1">Training Mode Prices <span class="text-xs font-normal text-gray-400">(optional — leave blank to use base price)</span></p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-3">
                        <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-sm font-semibold text-blue-800">🖥 Online</span>
                            </div>
                            <input type="number" name="price_online" value="{{ old('price_online') }}" min="0" step="0.01" placeholder="e.g. 750000"
                                   class="w-full border border-blue-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white">
                        </div>
                        <div class="bg-orange-50 rounded-xl p-4 border border-orange-100">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-sm font-semibold text-orange-800">📍 Physical · 1 Month</span>
                            </div>
                            <input type="number" name="price_physical_monthly" value="{{ old('price_physical_monthly') }}" min="0" step="0.01" placeholder="e.g. 850000"
                                   class="w-full border border-orange-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                        </div>
                        <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-sm font-semibold text-green-800">🏢 Physical · 3 Months</span>
                            </div>
                            <input type="number" name="price_physical_quarterly" value="{{ old('price_physical_quarterly') }}" min="0" step="0.01" placeholder="e.g. 950000"
                                   class="w-full border border-green-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 bg-white">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Installment Options --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4" x-data="installmentEditor([])">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide">Installment Plans</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Define the payment spread options students can choose. Max 30 days total.</p>
                </div>
                <button type="button" @click="addOption()" class="text-xs font-semibold text-brand-600 hover:text-brand-800 border border-brand-200 px-3 py-1.5 rounded-lg hover:bg-brand-50 transition-colors">
                    + Add Option
                </button>
            </div>

            <div x-show="options.length === 0">
                <p class="text-xs text-gray-400 italic">No custom options — default system options will be used. Add options to override or use a quick preset below.</p>
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
                                   min="2" max="10" @change="autoLabel(i)"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-400">
                        </div>
                        <div class="col-span-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Days between each</label>
                            <input type="number" :name="`installment_options[${i}][period_days]`" x-model.number="opt.period_days"
                                   min="1" max="30" @change="autoLabel(i)"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-400">
                        </div>
                        <div class="col-span-1 flex items-end justify-center">
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
            <div class="flex items-start gap-6">
                <div class="w-40 h-28 bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center border border-gray-200 shrink-0">
                    <img x-show="preview" :src="preview" class="w-full h-full object-cover" x-cloak>
                    <svg x-show="!preview" class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Course Thumbnail</label>
                    <input type="file" name="thumbnail" accept="image/*"
                           @change="preview = URL.createObjectURL($event.target.files[0])"
                           class="block text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                    <p class="text-xs text-gray-400 mt-2">JPG, PNG or WebP. Max 4MB. Recommended: 1280×720px</p>
                </div>
            </div>
            {{-- Promo Video: URL or Upload --}}
            <div x-data="{ videoMode: 'url' }">
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
                    <input type="url" name="promo_video" value="{{ old('promo_video') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                           placeholder="https://youtube.com/watch?v=... or https://vimeo.com/...">
                    <p class="text-xs text-gray-400 mt-1">YouTube, Vimeo, or any direct video link.</p>
                </div>
                <div x-show="videoMode==='upload'">
                    <input type="file" name="promo_video_file" accept="video/mp4,video/webm,video/ogg"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                    <p class="text-xs text-gray-400 mt-1">MP4, WebM or OGG. Max 200MB. Uploaded video will be hosted on your server.</p>
                </div>
            </div>

            {{-- Brochure --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Course Brochure / Prospectus <span class="text-gray-400 font-normal">(optional)</span></label>
                <input type="file" name="brochure" accept=".pdf,.doc,.docx"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                <p class="text-xs text-gray-400 mt-1">PDF, DOC or DOCX. Max 10MB. Students will see a download button on the public course page.</p>
            </div>
        </div>

        {{-- Curriculum Notes --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">
            <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide border-b border-gray-100 pb-3">Course Content</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Requirements</label>
                    <textarea name="requirements" rows="4"
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                              placeholder="One requirement per line">{{ old('requirements') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">What You'll Learn</label>
                    <textarea name="what_you_learn" rows="4"
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                              placeholder="One outcome per line">{{ old('what_you_learn') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Who Is This For</label>
                    <textarea name="who_is_this_for" rows="4"
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                              placeholder="One audience type per line">{{ old('who_is_this_for') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Curriculum Builder --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6"
             x-data="{
                modules: [],
                addModule() {
                    this.modules.push({ title: '', is_free_preview: false, lessons: [] });
                },
                removeModule(i) {
                    this.modules.splice(i, 1);
                },
                addLesson(mi) {
                    this.modules[mi].lessons.push({ title: '', type: 'video' });
                },
                removeLesson(mi, li) {
                    this.modules[mi].lessons.splice(li, 1);
                }
             }">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                <div>
                    <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide">Course Curriculum</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Add modules (sections) and lessons now, or skip and add later from the course page.</p>
                </div>
                <button type="button" @click="addModule()"
                    class="flex items-center gap-1.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Module
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(mod, mi) in modules" :key="mi">
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        {{-- Module header --}}
                        <div class="flex items-center gap-3 bg-gray-50 px-4 py-3">
                            <span class="text-xs font-bold text-gray-400 w-5" x-text="'M'+(mi+1)"></span>
                            <input type="text" :name="`modules[${mi}][title]`" x-model="mod.title"
                                placeholder="Module title (e.g. Introduction, Getting Started…)"
                                class="flex-1 border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                            <label class="flex items-center gap-1.5 text-xs text-gray-500 cursor-pointer shrink-0">
                                <input type="checkbox" :name="`modules[${mi}][is_free_preview]`" value="1" x-model="mod.is_free_preview"
                                    class="w-3.5 h-3.5 rounded border-gray-300 text-brand-600">
                                Free preview
                            </label>
                            <button type="button" @click="removeModule(mi)"
                                class="text-red-400 hover:text-red-600 transition-colors shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Lessons --}}
                        <div class="px-4 py-2 space-y-2">
                            <template x-for="(lesson, li) in mod.lessons" :key="li">
                                <div class="flex items-center gap-2 pl-6">
                                    <span class="text-xs text-gray-300 w-5" x-text="(li+1)+'.'"></span>
                                    <select :name="`modules[${mi}][lessons][${li}][type]`" x-model="lesson.type"
                                        class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-500 shrink-0">
                                        <option value="video">Video</option>
                                        <option value="text">Text / Article</option>
                                        <option value="quiz">Quiz</option>
                                        <option value="assignment">Assignment</option>
                                        <option value="live">Live Session</option>
                                        <option value="download">Download</option>
                                    </select>
                                    <input type="text" :name="`modules[${mi}][lessons][${li}][title]`" x-model="lesson.title"
                                        placeholder="Lesson title…"
                                        class="flex-1 border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                    <button type="button" @click="removeLesson(mi, li)"
                                        class="text-red-400 hover:text-red-600 transition-colors shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>

                            <button type="button" @click="addLesson(mi)"
                                class="ml-11 flex items-center gap-1 text-xs text-brand-600 hover:text-brand-800 font-medium py-1.5 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Lesson
                            </button>
                        </div>
                    </div>
                </template>

                <div x-show="modules.length === 0"
                     class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl text-gray-400 text-sm">
                    No modules yet. Click <strong>Add Module</strong> above to start building your curriculum.
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">
                Create Course
            </button>
            <a href="{{ route('admin.courses.index') }}" class="text-gray-600 hover:text-gray-900 font-medium px-4 py-2.5 rounded-xl hover:bg-gray-100 transition-colors">
                Cancel
            </a>
        </div>
    </form>

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
