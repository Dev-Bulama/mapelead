@extends('layouts.admin')
@section('title', 'Testimonials')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Testimonials</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage social proof from your learners.</p>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm p-4">{{ session('success') }}</div>
    @endif

    {{-- Add Form --}}
    <div class="bg-white rounded-xl border p-5" x-data="{ open: false }">
        <button @click="open = !open"
                class="flex items-center gap-2 text-sm font-medium text-white px-4 py-2 rounded-lg hover:opacity-90 transition"
                style="background-color:#14215B;">
            <span x-text="open ? 'Cancel' : '+ Add Testimonial'"></span>
        </button>

        <div x-show="open" x-transition class="mt-5 pt-5 border-t border-gray-100">
            <form action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
                               placeholder="Full name">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Title / Role</label>
                        <input type="text" name="title"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none"
                               placeholder="e.g. Software Engineer">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Company</label>
                        <input type="text" name="company"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none"
                               placeholder="Company name">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Content <span class="text-red-500">*</span></label>
                        <textarea name="content" rows="3" required
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none"
                                  placeholder="What did they say?"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Avatar</label>
                        <input type="file" name="avatar" accept="image/*"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-medium">
                    </div>
                    <div x-data="{ rating: 5 }">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Rating</label>
                        <input type="hidden" name="rating" :value="rating">
                        <div class="flex gap-0.5">
                            <template x-for="star in [1,2,3,4,5]" :key="star">
                                <button type="button" @click="rating = star"
                                        :class="star <= rating ? 'text-amber-400' : 'text-gray-300'"
                                        class="text-2xl leading-none hover:text-amber-400 transition focus:outline-none">&#9733;</button>
                            </template>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 pt-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" class="w-4 h-4 rounded border-gray-300">
                            <span class="text-xs text-gray-600">Featured</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded border-gray-300">
                            <span class="text-xs text-gray-600">Active</span>
                        </label>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-white hover:opacity-90 transition"
                            style="background-color:#14215B;">
                        Add Testimonial
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Cards Grid --}}
    @if($testimonials->isEmpty())
    <div class="bg-white rounded-xl border p-12 text-center">
        <p class="text-sm text-gray-500">No testimonials yet. Add your first one above.</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($testimonials as $testimonial)
        <div class="relative bg-white rounded-xl border p-5">
            @if($testimonial->is_featured)
            <span class="absolute top-3 right-3 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                Featured
            </span>
            @endif

            <div class="flex items-center gap-3 mb-3">
                @if($testimonial->avatar)
                <img src="{{ asset('storage/' . $testimonial->avatar) }}" alt="{{ $testimonial->name }}"
                     class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm">
                @else
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold border-2 border-white shadow-sm"
                     style="background-color:#14215B">
                    {{ strtoupper(substr($testimonial->name, 0, 2)) }}
                </div>
                @endif
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ $testimonial->name }}</p>
                    <p class="text-xs text-gray-500">
                        @if($testimonial->title){{ $testimonial->title }}@endif
                        @if($testimonial->company) &middot; {{ $testimonial->company }}@endif
                    </p>
                </div>
            </div>

            <div class="flex gap-0.5 mb-2">
                @for($i = 1; $i <= 5; $i++)
                <span class="{{ $i <= $testimonial->rating ? 'text-amber-400' : 'text-gray-300' }} text-base leading-none">&#9733;</span>
                @endfor
            </div>

            <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ \Str::limit($testimonial->content, 120) }}</p>

            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                @if($testimonial->is_active)
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Active</span>
                @else
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Inactive</span>
                @endif

                <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST"
                      onsubmit="return confirm('Delete this testimonial?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="text-xs font-medium text-red-600 hover:text-red-800 px-2 py-1 rounded hover:bg-red-50 transition">
                        Delete
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
