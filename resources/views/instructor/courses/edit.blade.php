@extends('layouts.student')

@section('title', 'Edit Course: ' . $course->title)
@section('page_title', 'Edit Course: ' . $course->title)

@section('content')
<form action="{{ route('instructor.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5">
        <h2 class="text-base font-semibold text-gray-900">Basic Information</h2>

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Course Title <span class="text-red-500">*</span></label>
            <input type="text" id="title" name="title" value="{{ old('title', $course->title) }}" required
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('title') border-red-400 @enderror">
            @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="short_description" class="block text-sm font-medium text-gray-700 mb-1">Short Description <span class="text-red-500">*</span></label>
            <textarea id="short_description" name="short_description" rows="2" maxlength="500" required
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('short_description') border-red-400 @enderror">{{ old('short_description', $course->short_description) }}</textarea>
            @error('short_description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Full Description <span class="text-red-500">*</span></label>
            <textarea id="description" name="description" rows="6" required
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('description') border-red-400 @enderror">{{ old('description', $course->description) }}</textarea>
            @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                <select id="category_id" name="category_id" required
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('category_id') border-red-400 @enderror">
                    <option value="">Select category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                <select id="type" name="type" required
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('type') border-red-400 @enderror">
                    <option value="">Select type</option>
                    <option value="online" {{ old('type', $course->type) === 'online' ? 'selected' : '' }}>Online</option>
                    <option value="physical" {{ old('type', $course->type) === 'physical' ? 'selected' : '' }}>Physical</option>
                    <option value="hybrid" {{ old('type', $course->type) === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                </select>
                @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Level <span class="text-red-500">*</span></label>
                <select id="level" name="level" required
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('level') border-red-400 @enderror">
                    <option value="">Select level</option>
                    <option value="beginner" {{ old('level', $course->level) === 'beginner' ? 'selected' : '' }}>Beginner</option>
                    <option value="intermediate" {{ old('level', $course->level) === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                    <option value="advanced" {{ old('level', $course->level) === 'advanced' ? 'selected' : '' }}>Advanced</option>
                    <option value="all_levels" {{ old('level', $course->level) === 'all_levels' ? 'selected' : '' }}>All Levels</option>
                </select>
                @error('level') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5"
         x-data="{ isFree: {{ old('is_free', $course->is_free) ? 'true' : 'false' }} }">
        <h2 class="text-base font-semibold text-gray-900">Pricing</h2>

        <div class="flex items-center gap-3">
            <input type="checkbox" id="is_free" name="is_free" value="1"
                x-model="isFree"
                {{ old('is_free', $course->is_free) ? 'checked' : '' }}
                class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            <label for="is_free" class="text-sm font-medium text-gray-700">This course is free</label>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="!isFree">
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price (₦) <span class="text-red-500">*</span></label>
                <input type="number" id="price" name="price" value="{{ old('price', $course->price) }}" min="0" step="0.01"
                    :required="!isFree"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('price') border-red-400 @enderror">
                @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-1">Discount Price (₦)</label>
                <input type="number" id="discount_price" name="discount_price" value="{{ old('discount_price', $course->discount_price) }}" min="0" step="0.01"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('discount_price') border-red-400 @enderror"
                    placeholder="Leave blank for no discount">
                @error('discount_price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5"
         x-data="{ preview: null }">
        <h2 class="text-base font-semibold text-gray-900">Course Details</h2>

        <div>
            <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-1">Duration (hours)</label>
            <input type="number" id="duration_hours" name="duration_hours" value="{{ old('duration_hours', $course->duration_hours) }}" min="0" step="0.5"
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('duration_hours') border-red-400 @enderror">
            @error('duration_hours') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail Image</label>

            @if($course->thumbnail)
            <div class="mb-3">
                <p class="text-xs text-gray-500 mb-1">Current thumbnail:</p>
                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="Current thumbnail" class="w-48 h-28 object-cover rounded-xl border border-gray-200">
            </div>
            @endif

            <input type="file" id="thumbnail" name="thumbnail" accept="image/*"
                @change="const file = $event.target.files[0]; if(file){ const reader = new FileReader(); reader.onload = e => preview = e.target.result; reader.readAsDataURL(file); } else { preview = null; }"
                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-600 hover:file:bg-brand-100 @error('thumbnail') border border-red-400 rounded-xl @enderror">
            @if($course->thumbnail)
                <p class="mt-1 text-xs text-gray-400">Upload a new image to replace the current thumbnail.</p>
            @endif
            @error('thumbnail') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

            <div x-show="preview" class="mt-3">
                <p class="text-xs text-gray-500 mb-1">New thumbnail preview:</p>
                <img :src="preview" alt="New thumbnail preview" class="w-48 h-28 object-cover rounded-xl border border-gray-200">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-3">Requirements</h2>
        <textarea name="requirements" rows="4"
            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
            placeholder="One requirement per line">{{ old('requirements', $course->requirements) }}</textarea>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-3">What You'll Learn</h2>
        <textarea name="what_you_learn" rows="4"
            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
            placeholder="One item per line">{{ old('what_you_learn', $course->what_you_learn) }}</textarea>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-3">Who Is This For</h2>
        <textarea name="who_is_this_for" rows="3"
            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
            placeholder="One audience per line">{{ old('who_is_this_for', $course->who_is_this_for) }}</textarea>
    </div>

    <div class="flex items-center justify-between">
        <a href="{{ route('instructor.courses.show', $course->id) }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 border border-gray-200 hover:border-gray-300 rounded-xl transition-colors">
            Cancel
        </a>
        <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl transition-colors">
            Save Changes
        </button>
    </div>

</form>

<div class="bg-white rounded-2xl border border-red-200 p-6 mt-6">
    <h2 class="text-base font-semibold text-red-700 mb-1">Danger Zone</h2>
    <p class="text-sm text-gray-500 mb-4">Permanently delete this course and all its content. This action cannot be undone.</p>
    <form action="{{ route('instructor.courses.destroy', $course->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit"
            x-on:click.prevent="if(confirm('Are you sure you want to delete this course? All content and enrollments will be permanently removed.')) $el.closest('form').submit()"
            class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-xl transition-colors">
            Delete This Course
        </button>
    </form>
</div>
@endsection
