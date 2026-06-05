@extends('layouts.student')

@section('title', 'Edit Course: ' . $course->title)
@section('page_title', 'Edit Course: ' . $course->title)

@section('content')
<form action="{{ route('instructor.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf @method('PUT')

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Basic Info --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5">
        <h2 class="text-base font-semibold text-gray-900">Basic Information</h2>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Course Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $course->title) }}" required
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('title') border-red-400 @enderror">
            @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Short Description <span class="text-red-500">*</span></label>
            <textarea name="short_description" rows="2" maxlength="500" required
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('short_description') border-red-400 @enderror">{{ old('short_description', $course->short_description) }}</textarea>
            @error('short_description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Description <span class="text-red-500">*</span></label>
            <textarea name="description" rows="6" required
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('description') border-red-400 @enderror">{{ old('description', $course->description) }}</textarea>
            @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                <select name="category_id" required
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">Select category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                <select name="type" required
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    @foreach(['online' => 'Online', 'physical' => 'Physical', 'hybrid' => 'Hybrid'] as $val => $label)
                        <option value="{{ $val }}" {{ old('type', $course->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Level <span class="text-red-500">*</span></label>
                <select name="level" required
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    @foreach(['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced', 'all_levels' => 'All Levels'] as $val => $label)
                        <option value="{{ $val }}" {{ old('level', $course->level) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                <input type="text" name="language" value="{{ old('language', $course->language ?? 'English') }}"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration (hours)</label>
                <input type="number" name="duration_hours" value="{{ old('duration_hours', $course->duration_hours) }}" min="1"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration (weeks)</label>
                <input type="number" name="duration_weeks" value="{{ old('duration_weeks', $course->duration_weeks) }}" min="1"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
        </div>

        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="certificate_enabled" value="1"
                   {{ old('certificate_enabled', $course->certificate_enabled ?? true) ? 'checked' : '' }}
                   class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            <span class="text-sm font-medium text-gray-700">Certificate of completion enabled</span>
        </label>
    </div>

    {{-- Pricing --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5"
         x-data="{ isFree: {{ old('is_free', $course->is_free) ? 'true' : 'false' }} }">
        <h2 class="text-base font-semibold text-gray-900">Pricing</h2>

        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_free" value="1" x-model="isFree"
                   {{ old('is_free', $course->is_free) ? 'checked' : '' }}
                   class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            <span class="text-sm font-medium text-gray-700">This course is free</span>
        </label>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="!isFree">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price (₦) <span class="text-red-500">*</span></label>
                <input type="number" name="price" value="{{ old('price', $course->price) }}" min="0" step="0.01" :required="!isFree"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('price') border-red-400 @enderror">
                @error('price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Discount Price (₦)</label>
                <input type="number" name="discount_price" value="{{ old('discount_price', $course->discount_price) }}" min="0" step="0.01"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                    placeholder="Leave blank for no discount">
            </div>
        </div>
    </div>

    {{-- Media --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5"
         x-data="{ thumbPreview: null, videoMode: 'url' }">
        <h2 class="text-base font-semibold text-gray-900">Media</h2>

        {{-- Thumbnail --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Course Thumbnail</label>
            @if($course->thumbnail)
            <div class="mb-3">
                <p class="text-xs text-gray-500 mb-1">Current:</p>
                <img src="{{ asset('storage/' . $course->thumbnail) }}" class="w-48 h-28 object-cover rounded-xl border border-gray-200">
            </div>
            @endif
            <input type="file" name="thumbnail" accept="image/*"
                @change="const f=$event.target.files[0]; thumbPreview=f?URL.createObjectURL(f):null"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-600 hover:file:bg-brand-100">
            <p class="text-xs text-gray-400 mt-1">Leave blank to keep current. Max 4MB.</p>
            <div x-show="thumbPreview" class="mt-2">
                <img :src="thumbPreview" class="w-48 h-28 object-cover rounded-xl border border-gray-200">
            </div>
        </div>

        {{-- Promo Video --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Promo Video</label>
            <div class="flex gap-2 mb-3">
                <button type="button" @click="videoMode='url'"
                        :class="videoMode==='url' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="px-4 py-1.5 text-xs font-semibold rounded-lg transition-colors">Paste URL</button>
                <button type="button" @click="videoMode='upload'"
                        :class="videoMode==='upload' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="px-4 py-1.5 text-xs font-semibold rounded-lg transition-colors">Upload Video</button>
            </div>
            <div x-show="videoMode==='url'">
                <input type="url" name="promo_video" value="{{ old('promo_video', $course->promo_video) }}"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                       placeholder="https://youtube.com/watch?v=... or https://vimeo.com/...">
                <p class="text-xs text-gray-400 mt-1">YouTube, Vimeo, or any direct video link.</p>
                @if($course->promo_video)
                    <p class="text-xs text-brand-600 mt-1">Current: <a href="{{ $course->promo_video }}" target="_blank" class="underline break-all">{{ $course->promo_video }}</a></p>
                @endif
            </div>
            <div x-show="videoMode==='upload'">
                <input type="file" name="promo_video_file" accept="video/mp4,video/webm,video/ogg"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-600 hover:file:bg-brand-100">
                <p class="text-xs text-gray-400 mt-1">MP4, WebM or OGG. Max 200MB. Replaces current video.</p>
            </div>
        </div>
    </div>

    {{-- Course Content Details --}}
    @php
        $req  = is_array($course->requirements)   ? implode("\n", $course->requirements)   : ($course->requirements ?? '');
        $wyl  = is_array($course->what_you_learn)  ? implode("\n", $course->what_you_learn)  : ($course->what_you_learn ?? '');
        $witf = is_array($course->who_is_this_for) ? implode("\n", $course->who_is_this_for) : ($course->who_is_this_for ?? '');
    @endphp
    <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5">
        <h2 class="text-base font-semibold text-gray-900">Course Content Details</h2>
        <p class="text-xs text-gray-400 -mt-2">One item per line. These appear on your public course page.</p>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Requirements</label>
            <textarea name="requirements" rows="4"
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                placeholder="One requirement per line">{{ old('requirements', $req) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">What You'll Learn</label>
            <textarea name="what_you_learn" rows="4"
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                placeholder="One item per line">{{ old('what_you_learn', $wyl) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Who Is This For</label>
            <textarea name="who_is_this_for" rows="3"
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                placeholder="One audience per line">{{ old('who_is_this_for', $witf) }}</textarea>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <a href="{{ route('instructor.courses.show', $course->id) }}"
           class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 border border-gray-200 rounded-xl transition-colors">
            Cancel
        </a>
        <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl transition-colors">
            Save Changes
        </button>
    </div>
</form>

<div class="bg-white rounded-2xl border border-red-200 p-6 mt-6">
    <h2 class="text-base font-semibold text-red-700 mb-1">Danger Zone</h2>
    <p class="text-sm text-gray-500 mb-4">Permanently delete this course and all its content. This cannot be undone.</p>
    <form action="{{ route('instructor.courses.destroy', $course->id) }}" method="POST">
        @csrf @method('DELETE')
        <button type="submit"
            onclick="return confirm('Delete this course permanently?')"
            class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-xl transition-colors">
            Delete This Course
        </button>
    </form>
</div>
@endsection
