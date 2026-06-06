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
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Price (₦) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', $course->price) }}" min="0" step="0.01"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
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
@endsection
