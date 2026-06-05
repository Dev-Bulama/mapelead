@extends('layouts.student')

@section('title', 'Create Course')
@section('page_title', 'Create New Course')

@section('content')
<form action="{{ route('instructor.courses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

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
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Course Title <span class="text-red-500">*</span></label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('title') border-red-400 @enderror"
                placeholder="e.g. Complete Web Development Bootcamp">
            @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="short_description" class="block text-sm font-medium text-gray-700 mb-1">Short Description <span class="text-red-500">*</span></label>
            <textarea id="short_description" name="short_description" rows="2" maxlength="500" required
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('short_description') border-red-400 @enderror"
                placeholder="A brief summary shown in course listings (max 500 characters)">{{ old('short_description') }}</textarea>
            @error('short_description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Full Description <span class="text-red-500">*</span></label>
            <textarea id="description" name="description" rows="6" required
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('description') border-red-400 @enderror"
                placeholder="Detailed description of the course content, goals, and outcomes">{{ old('description') }}</textarea>
            @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                <select id="category_id" name="category_id" required
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('category_id') border-red-400 @enderror">
                    <option value="">Select category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                <select id="type" name="type" required
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">Select type</option>
                    @foreach(['online' => 'Online', 'physical' => 'Physical', 'hybrid' => 'Hybrid'] as $val => $label)
                        <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Level <span class="text-red-500">*</span></label>
                <select id="level" name="level" required
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">Select level</option>
                    @foreach(['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced', 'all_levels' => 'All Levels'] as $val => $label)
                        <option value="{{ $val }}" {{ old('level') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                <input type="text" name="language" value="{{ old('language', 'English') }}"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                    placeholder="e.g. English">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration (hours)</label>
                <input type="number" name="duration_hours" value="{{ old('duration_hours') }}" min="1"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                    placeholder="e.g. 40">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration (weeks)</label>
                <input type="number" name="duration_weeks" value="{{ old('duration_weeks') }}" min="1"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                    placeholder="e.g. 12">
            </div>
        </div>

        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="certificate_enabled" value="1" checked
                   class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            <span class="text-sm font-medium text-gray-700">Certificate of completion enabled</span>
        </label>
    </div>

    {{-- Pricing --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5"
         x-data="{ isFree: {{ old('is_free') ? 'true' : 'false' }} }">
        <h2 class="text-base font-semibold text-gray-900">Pricing</h2>

        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_free" value="1" x-model="isFree" {{ old('is_free') ? 'checked' : '' }}
                   class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            <span class="text-sm font-medium text-gray-700">This course is free</span>
        </label>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="!isFree">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price (₦) <span class="text-red-500">*</span></label>
                <input type="number" name="price" value="{{ old('price', 0) }}" min="0" step="0.01" :required="!isFree"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('price') border-red-400 @enderror">
                @error('price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Discount Price (₦)</label>
                <input type="number" name="discount_price" value="{{ old('discount_price') }}" min="0" step="0.01"
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
            <input type="file" name="thumbnail" accept="image/*"
                @change="const f=$event.target.files[0]; thumbPreview=f?URL.createObjectURL(f):null"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-600 hover:file:bg-brand-100">
            <p class="text-xs text-gray-400 mt-1">JPG, PNG or WebP. Max 4MB. Recommended 1280×720px.</p>
            <div x-show="thumbPreview" class="mt-3">
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
                <input type="url" name="promo_video" value="{{ old('promo_video') }}"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                       placeholder="https://youtube.com/watch?v=... or https://vimeo.com/...">
                <p class="text-xs text-gray-400 mt-1">YouTube, Vimeo, or any direct video link.</p>
            </div>
            <div x-show="videoMode==='upload'">
                <input type="file" name="promo_video_file" accept="video/mp4,video/webm,video/ogg"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-600 hover:file:bg-brand-100">
                <p class="text-xs text-gray-400 mt-1">MP4, WebM or OGG. Max 200MB.</p>
            </div>
        </div>
    </div>

    {{-- Course Content Details --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5">
        <h2 class="text-base font-semibold text-gray-900">Course Content Details</h2>
        <p class="text-xs text-gray-400 -mt-2">Enter one item per line. These appear on your public course page.</p>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Requirements</label>
            <textarea name="requirements" rows="4"
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                placeholder="Basic computer skills&#10;Internet connection&#10;Willingness to learn">{{ old('requirements') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">What You'll Learn</label>
            <textarea name="what_you_learn" rows="4"
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                placeholder="Build real-world projects&#10;Understand core concepts&#10;Get industry-ready skills">{{ old('what_you_learn') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Who Is This For</label>
            <textarea name="who_is_this_for" rows="3"
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                placeholder="Beginners with no prior experience&#10;Developers looking to upskill">{{ old('who_is_this_for') }}</textarea>
        </div>
    </div>

    {{-- Curriculum Builder --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6"
         x-data="{
            modules: [],
            addModule() { this.modules.push({ title: '', is_free_preview: false, lessons: [] }); },
            removeModule(i) { this.modules.splice(i, 1); },
            addLesson(mi) { this.modules[mi].lessons.push({ title: '', type: 'video' }); },
            removeLesson(mi, li) { this.modules[mi].lessons.splice(li, 1); }
         }">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Course Curriculum</h2>
                <p class="text-xs text-gray-400 mt-0.5">Add modules and lessons now, or skip and add later.</p>
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
                    <div class="flex items-center gap-3 bg-gray-50 px-4 py-3">
                        <span class="text-xs font-bold text-gray-400 w-5" x-text="'M'+(mi+1)"></span>
                        <input type="text" :name="`modules[${mi}][title]`" x-model="mod.title"
                            placeholder="Module title…"
                            class="flex-1 border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <label class="flex items-center gap-1.5 text-xs text-gray-500 cursor-pointer shrink-0">
                            <input type="checkbox" :name="`modules[${mi}][is_free_preview]`" value="1" x-model="mod.is_free_preview"
                                class="w-3.5 h-3.5 rounded border-gray-300 text-brand-600">
                            Free preview
                        </label>
                        <button type="button" @click="removeModule(mi)" class="text-red-400 hover:text-red-600 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
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
                                <button type="button" @click="removeLesson(mi, li)" class="text-red-400 hover:text-red-600 shrink-0">
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
                 class="text-center py-6 border-2 border-dashed border-gray-200 rounded-xl text-gray-400 text-sm">
                Click <strong>Add Module</strong> to start building your curriculum.
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('instructor.courses.index') }}"
           class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 border border-gray-200 rounded-xl transition-colors">
            Cancel
        </a>
        <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl transition-colors">
            Create Course
        </button>
    </div>
</form>
@endsection
