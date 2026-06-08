@extends('layouts.admin')

@section('title', 'CMS Builder')

@section('content')
<div x-data="{ activeTab: 'hero' }">

    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-2xl">
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl">
        <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/></svg>
        <span class="text-sm font-medium">{{ session('error') }}</span>
    </div>
    @endif

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">CMS Builder</h1>
        <p class="text-sm text-gray-500 mt-1">Manage homepage content, testimonials, FAQs, stats, and partners.</p>
    </div>

    <div class="flex flex-wrap gap-1 bg-gray-100 p-1 rounded-xl w-fit mb-6">
        <button @click="activeTab = 'hero'"
            :class="activeTab === 'hero' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-150">Hero Banners</button>
        <button @click="activeTab = 'testimonials'"
            :class="activeTab === 'testimonials' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-150">Testimonials</button>
        <button @click="activeTab = 'faqs'"
            :class="activeTab === 'faqs' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-150">FAQs</button>
        <button @click="activeTab = 'wcu'"
            :class="activeTab === 'wcu' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-150">Why Choose Us</button>
        <button @click="activeTab = 'stats'"
            :class="activeTab === 'stats' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-150">Stats</button>
        <button @click="activeTab = 'partners'"
            :class="activeTab === 'partners' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-150">Partners</button>
    </div>

    {{-- ─── HERO BANNERS ─────────────────────────────────────────────────────────── --}}
    <div x-show="activeTab === 'hero'" x-data="{ showForm: false }">
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Hero Banners</h2>
                    <p class="text-sm text-gray-500">Control the homepage hero section slides.</p>
                </div>
                <button @click="showForm = !showForm"
                    class="inline-flex items-center gap-2 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors"
                    style="background-color: #4f46e5;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span x-text="showForm ? 'Cancel' : 'Add Banner'"></span>
                </button>
            </div>

            <div x-show="showForm" x-transition class="mb-8 p-5 bg-gray-50 rounded-xl border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">New Hero Banner</h3>
                <form action="{{ route('admin.cms.hero.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Learn Without Limits">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Subtitle</label>
                            <input type="text" name="subtitle" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Short subtitle text">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                            <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Banner description text..."></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Badge Text</label>
                            <input type="text" name="badge_text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. New Course Available">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Primary Button Text</label>
                            <input type="text" name="primary_btn_text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Get Started">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Primary Button URL</label>
                            <input type="url" name="primary_btn_url" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="https://">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Secondary Button Text</label>
                            <input type="text" name="secondary_btn_text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Browse Courses">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Secondary Button URL</label>
                            <input type="url" name="secondary_btn_url" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="https://">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Banner Image</label>
                            <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700">
                        </div>
                        <div class="flex items-center gap-3 pt-4">
                            <input type="checkbox" name="is_active" id="banner_active_new" value="1" checked class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <label for="banner_active_new" class="text-sm text-gray-700">Set as Active</label>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="inline-flex items-center gap-2 text-white text-sm font-medium px-5 py-2 rounded-xl transition-colors" style="background-color: #4f46e5;">Save Banner</button>
                        <button type="button" @click="showForm = false" class="inline-flex items-center gap-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium px-5 py-2 rounded-xl hover:bg-gray-50 transition-colors">Cancel</button>
                    </div>
                </form>
            </div>

            @if($heroBanners->isEmpty())
            <div class="text-center py-16">
                <p class="text-sm text-gray-500">No hero banners yet. Add your first one above.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide pb-3 pr-4 w-16">Sort</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide pb-3 pr-4 w-20">Image</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide pb-3 pr-4">Title</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide pb-3 pr-4 w-28">Status</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide pb-3 w-36">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($heroBanners as $banner)
                        <tr x-data="{ editing: false }" class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 pr-4">
                                <span class="inline-flex items-center justify-center w-7 h-7 bg-gray-100 rounded-lg text-xs font-semibold text-gray-600">{{ $banner->sort_order }}</span>
                            </td>
                            <td class="py-3 pr-4">
                                @if($banner->image)
                                <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}" class="w-16 h-10 object-cover rounded-lg border border-gray-200">
                                @else
                                <div class="w-16 h-10 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909"/></svg>
                                </div>
                                @endif
                            </td>
                            <td class="py-3 pr-4">
                                <div x-show="!editing">
                                    <p class="font-medium text-gray-900">{{ $banner->title }}</p>
                                    @if($banner->subtitle)
                                    <p class="text-xs text-gray-500 mt-0.5">{{ Str::limit($banner->subtitle, 50) }}</p>
                                    @endif
                                </div>
                                <div x-show="editing" x-transition>
                                    <form action="{{ route('admin.cms.hero.update') }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="id" value="{{ $banner->id }}">
                                        <input type="text" name="title" value="{{ $banner->title }}" required class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Title">
                                        <input type="text" name="subtitle" value="{{ $banner->subtitle }}" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Subtitle">
                                        <div class="flex gap-2 pt-1">
                                            <button type="submit" class="text-xs font-medium text-white px-3 py-1.5 rounded-lg transition-colors" style="background-color: #4f46e5;">Save</button>
                                            <button type="button" @click="editing = false" class="text-xs font-medium text-gray-600 bg-gray-100 px-3 py-1.5 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                            <td class="py-3 pr-4">
                                @if($banner->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Active</span>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 text-gray-500 text-xs font-medium rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactive</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="flex items-center gap-1">
                                    <button @click="editing = !editing" class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 px-2.5 py-1.5 rounded-lg hover:bg-indigo-50 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z"/></svg>
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.cms.hero.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Delete this banner?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-xs font-medium text-red-600 hover:text-red-800 px-2.5 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- ─── TESTIMONIALS ─────────────────────────────────────────────────────────── --}}
    <div x-show="activeTab === 'testimonials'" x-data="{ showForm: false, rating: 5 }">
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Testimonials</h2>
                    <p class="text-sm text-gray-500">Manage social proof from your learners.</p>
                </div>
                <button @click="showForm = !showForm"
                    class="inline-flex items-center gap-2 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors" style="background-color: #4f46e5;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span x-text="showForm ? 'Cancel' : 'Add Testimonial'"></span>
                </button>
            </div>

            <div x-show="showForm" x-transition class="mb-8 p-5 bg-gray-50 rounded-xl border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">New Testimonial</h3>
                <form action="{{ route('admin.cms.testimonials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Full name">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Title / Role</label>
                            <input type="text" name="title" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Software Engineer">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Company</label>
                            <input type="text" name="company" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Company name">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Avatar</label>
                            <input type="file" name="avatar" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Content <span class="text-red-500">*</span></label>
                            <textarea name="content" rows="4" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="What did they say about the platform?"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-2">Rating</label>
                            <input type="hidden" name="rating" :value="rating">
                            <div class="flex gap-1">
                                <template x-for="star in [1,2,3,4,5]" :key="star">
                                    <button type="button" @click="rating = star"
                                        :class="star <= rating ? 'text-amber-400' : 'text-gray-300'"
                                        class="text-2xl leading-none hover:text-amber-400 transition-colors focus:outline-none">&#9733;</button>
                                </template>
                            </div>
                            <p class="text-xs text-gray-500 mt-1" x-text="rating + ' / 5 stars'"></p>
                        </div>
                        <div class="flex items-center gap-6 pt-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_featured" value="1" class="w-4 h-4 text-indigo-600 border-gray-300 rounded">
                                <span class="text-sm text-gray-700">Featured</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-indigo-600 border-gray-300 rounded">
                                <span class="text-sm text-gray-700">Active</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="text-white text-sm font-medium px-5 py-2 rounded-xl" style="background-color: #4f46e5;">Save Testimonial</button>
                        <button type="button" @click="showForm = false" class="bg-white border border-gray-300 text-gray-700 text-sm font-medium px-5 py-2 rounded-xl hover:bg-gray-50">Cancel</button>
                    </div>
                </form>
            </div>

            @if($testimonials->isEmpty())
            <div class="text-center py-16">
                <p class="text-sm text-gray-500">No testimonials yet. Add your first one above.</p>
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($testimonials as $testimonial)
                <div class="relative bg-gray-50 border border-gray-200 rounded-xl p-5" x-data="{ editing: false, editRating: {{ $testimonial->rating ?? 5 }} }">
                    @if($testimonial->is_featured)
                    <span class="absolute top-3 right-3 inline-flex items-center px-2 py-0.5 bg-amber-50 text-amber-700 text-xs font-medium rounded-full border border-amber-200">Featured</span>
                    @endif

                    {{-- View mode --}}
                    <div x-show="!editing">
                        <div class="flex items-center gap-3 mb-3">
                            @if($testimonial->avatar)
                            <img src="{{ asset('storage/' . $testimonial->avatar) }}" alt="{{ $testimonial->name }}" class="w-11 h-11 rounded-full object-cover border-2 border-white shadow-sm">
                            @else
                            <div class="w-11 h-11 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold text-sm border-2 border-white shadow-sm">
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
                        <div class="flex gap-0.5 mb-3">
                            @for($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= $testimonial->rating ? 'text-amber-400' : 'text-gray-300' }} text-base leading-none">&#9733;</span>
                            @endfor
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ Str::limit($testimonial->content, 120) }}</p>
                        <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                            @if($testimonial->is_active)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-50 text-green-700 text-xs font-medium rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Active</span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 text-gray-500 text-xs font-medium rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactive</span>
                            @endif
                            <div class="flex items-center gap-1">
                                <button @click="editing = true" class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 px-2 py-1 rounded-lg hover:bg-indigo-50">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z"/></svg>
                                    Edit
                                </button>
                                <form action="{{ route('admin.cms.testimonials.destroy', $testimonial->id) }}" method="POST" onsubmit="return confirm('Delete this testimonial?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 text-xs font-medium text-red-600 px-2 py-1 rounded-lg hover:bg-red-50">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Edit mode --}}
                    <div x-show="editing" x-transition>
                        <form action="{{ route('admin.cms.testimonials.update', $testimonial->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf @method('PUT')
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Name</label>
                                <input type="text" name="name" value="{{ $testimonial->name }}" required class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Title</label>
                                    <input type="text" name="title" value="{{ $testimonial->title }}" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Company</label>
                                    <input type="text" name="company" value="{{ $testimonial->company }}" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Content</label>
                                <textarea name="content" rows="3" required class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $testimonial->content }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Rating</label>
                                <input type="hidden" name="rating" :value="editRating">
                                <div class="flex gap-1">
                                    <template x-for="star in [1,2,3,4,5]" :key="star">
                                        <button type="button" @click="editRating = star"
                                            :class="star <= editRating ? 'text-amber-400' : 'text-gray-300'"
                                            class="text-xl leading-none hover:text-amber-400 transition-colors">&#9733;</button>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Replace Avatar</label>
                                <input type="file" name="avatar" accept="image/*" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-indigo-50 file:text-indigo-700">
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" name="is_featured" value="1" {{ $testimonial->is_featured ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded">
                                    <span class="text-xs text-gray-700">Featured</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" {{ $testimonial->is_active ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded">
                                    <span class="text-xs text-gray-700">Active</span>
                                </label>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="text-xs font-medium text-white px-4 py-1.5 rounded-lg" style="background-color: #4f46e5;">Save</button>
                                <button type="button" @click="editing = false" class="text-xs font-medium text-gray-600 bg-gray-100 px-4 py-1.5 rounded-lg hover:bg-gray-200">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ─── FAQs ─────────────────────────────────────────────────────────────────── --}}
    <div x-show="activeTab === 'faqs'" x-data="{ showForm: false }">
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">FAQs</h2>
                    <p class="text-sm text-gray-500">Manage frequently asked questions.</p>
                </div>
                <button @click="showForm = !showForm"
                    class="inline-flex items-center gap-2 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors" style="background-color: #4f46e5;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span x-text="showForm ? 'Cancel' : 'Add FAQ'"></span>
                </button>
            </div>

            <div x-show="showForm" x-transition class="mb-8 p-5 bg-gray-50 rounded-xl border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">New FAQ</h3>
                <form action="{{ route('admin.cms.faqs.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Question <span class="text-red-500">*</span></label>
                        <input type="text" name="question" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. How do I enroll in a course?">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Answer <span class="text-red-500">*</span></label>
                        <textarea name="answer" rows="4" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Provide a clear, helpful answer..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Category</label>
                        <input type="text" name="category" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Billing, Enrollment, Technical">
                    </div>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" class="w-4 h-4 text-indigo-600 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Featured</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-indigo-600 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Active</span>
                        </label>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="text-white text-sm font-medium px-5 py-2 rounded-xl" style="background-color: #4f46e5;">Save FAQ</button>
                        <button type="button" @click="showForm = false" class="bg-white border border-gray-300 text-gray-700 text-sm font-medium px-5 py-2 rounded-xl hover:bg-gray-50">Cancel</button>
                    </div>
                </form>
            </div>

            @if($faqs->isEmpty())
            <div class="text-center py-16">
                <p class="text-sm text-gray-500">No FAQs yet. Add your first one above.</p>
            </div>
            @else
            <div class="space-y-2">
                @foreach($faqs as $index => $faq)
                <div class="border border-gray-200 rounded-xl overflow-hidden" x-data="{ open: false, editing: false }">
                    <div class="flex items-center gap-4 p-4 hover:bg-gray-50 transition-colors">
                        <span class="inline-flex items-center justify-center w-7 h-7 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-lg shrink-0">{{ $index + 1 }}</span>
                        <div class="flex-1 min-w-0 cursor-pointer" @click="open = !open">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $faq->question }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if($faq->category)
                            <span class="hidden sm:inline-flex px-2.5 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">{{ $faq->category }}</span>
                            @endif
                            @if($faq->is_active)
                            <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 bg-green-50 text-green-700 text-xs font-medium rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Active</span>
                            @else
                            <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 text-gray-500 text-xs font-medium rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactive</span>
                            @endif
                            <button @click="editing = !editing; open = editing" class="inline-flex items-center text-indigo-500 hover:text-indigo-700 p-1 rounded hover:bg-indigo-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z"/></svg>
                            </button>
                            <form action="{{ route('admin.cms.faqs.destroy', $faq->id) }}" method="POST" onsubmit="return confirm('Delete this FAQ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex items-center text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </button>
                            </form>
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 cursor-pointer" :class="open ? 'rotate-180' : ''" @click="open = !open" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </div>
                    </div>
                    <div x-show="open" x-transition class="px-4 pb-4 border-t border-gray-100">
                        <div x-show="!editing" class="pt-3">
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $faq->answer }}</p>
                        </div>
                        <div x-show="editing" x-transition class="pt-3">
                            <form action="{{ route('admin.cms.faqs.update', $faq->id) }}" method="POST" class="space-y-3">
                                @csrf @method('PUT')
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Question</label>
                                    <input type="text" name="question" value="{{ $faq->question }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Answer</label>
                                    <textarea name="answer" rows="4" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $faq->answer }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Category</label>
                                    <input type="text" name="category" value="{{ $faq->category }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div class="flex items-center gap-6">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="is_featured" value="1" {{ $faq->is_featured ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded">
                                        <span class="text-sm text-gray-700">Featured</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="is_active" value="1" {{ $faq->is_active ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded">
                                        <span class="text-sm text-gray-700">Active</span>
                                    </label>
                                </div>
                                <div class="flex gap-3">
                                    <button type="submit" class="text-white text-sm font-medium px-5 py-2 rounded-xl" style="background-color: #4f46e5;">Save Changes</button>
                                    <button type="button" @click="editing = false" class="bg-white border border-gray-300 text-gray-700 text-sm font-medium px-5 py-2 rounded-xl hover:bg-gray-50">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ─── WHY CHOOSE US ────────────────────────────────────────────────────────── --}}
    <div x-show="activeTab === 'wcu'">
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Why Choose Us</h2>
            <p class="text-sm text-gray-500 mb-6">Edit the section heading and feature cards on the homepage.</p>

            <form action="{{ route('admin.cms.features.update') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Badge Text</label>
                        <input type="text" name="badge" value="{{ $wcuSettings['wcu_badge'] ?? 'Why Choose Us' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Section Title</label>
                        <input type="text" name="title" value="{{ $wcuSettings['wcu_title'] ?? 'The Smart Way to Advance Your Career' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Subtitle</label>
                        <input type="text" name="subtitle" value="{{ $wcuSettings['wcu_subtitle'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Feature Cards</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($currentFeatures as $i => $feature)
                        <div class="border border-gray-200 rounded-xl p-4 space-y-2">
                            <div class="flex gap-2">
                                <div class="w-1/4">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Icon / Emoji</label>
                                    <input type="text" name="features[{{ $i }}][icon]" value="{{ $feature['icon'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="🎓">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label>
                                    <input type="text" name="features[{{ $i }}][title]" value="{{ $feature['title'] ?? '' }}" required class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                                <textarea name="features[{{ $i }}][description]" rows="2" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $feature['description'] ?? '' }}</textarea>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <button type="submit" class="text-white text-sm font-medium px-6 py-2.5 rounded-xl" style="background-color: #4f46e5;">Save Why Choose Us</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ─── STATS ────────────────────────────────────────────────────────────────── --}}
    <div x-show="activeTab === 'stats'">
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Numbers That Speak</h2>
            <p class="text-sm text-gray-500 mb-6">Edit the stats shown on the homepage. Use numbers like "10,000" or "92%".</p>

            <form action="{{ route('admin.cms.stats.update') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="border border-gray-200 rounded-xl p-4 space-y-3">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Students Stat</h3>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Value (e.g. 10,000 or 10000+)</label>
                            <input type="text" name="stat_students" value="{{ $stats['students'] }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Label</label>
                            <input type="text" name="stat_students_label" value="{{ $stats['students_label'] }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded-xl p-4 space-y-3">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Courses Stat</h3>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Value</label>
                            <input type="text" name="stat_courses" value="{{ $stats['courses'] }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Label</label>
                            <input type="text" name="stat_courses_label" value="{{ $stats['courses_label'] }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded-xl p-4 space-y-3">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Instructors Stat</h3>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Value</label>
                            <input type="text" name="stat_instructors" value="{{ $stats['instructors'] }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Label</label>
                            <input type="text" name="stat_instructors_label" value="{{ $stats['instructors_label'] }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded-xl p-4 space-y-3">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Placement Stat</h3>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Value (e.g. 92%)</label>
                            <input type="text" name="stat_placement" value="{{ $stats['placement'] }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Label</label>
                            <input type="text" name="stat_placement_label" value="{{ $stats['placement_label'] }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
                <div>
                    <button type="submit" class="text-white text-sm font-medium px-6 py-2.5 rounded-xl" style="background-color: #4f46e5;">Save Stats</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ─── PARTNERS ─────────────────────────────────────────────────────────────── --}}
    <div x-show="activeTab === 'partners'">
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Partner Logos</h2>
            <p class="text-sm text-gray-500 mb-6">Partner logos scroll across the homepage. Upload a logo image or just enter a name.</p>

            {{-- Add logo form --}}
            <div class="mb-8 p-5 bg-gray-50 rounded-xl border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Add Partner</h3>
                <form action="{{ route('admin.cms.partners.add') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Partner Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Google">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Logo Image</label>
                            <input type="file" name="logo" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-indigo-50 file:text-indigo-700">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Link URL</label>
                            <input type="url" name="url" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="https://">
                        </div>
                    </div>
                    <button type="submit" class="text-white text-sm font-medium px-5 py-2 rounded-xl" style="background-color: #4f46e5;">Add Partner</button>
                </form>
            </div>

            {{-- Partner list --}}
            @if(empty($partnerLogos))
            <div class="text-center py-12">
                <p class="text-sm text-gray-500">No partners added yet.</p>
            </div>
            @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($partnerLogos as $i => $partner)
                <div class="border border-gray-200 rounded-xl p-3 text-center relative">
                    @if(!empty($partner['logo']))
                    <img src="{{ asset('storage/' . $partner['logo']) }}" alt="{{ $partner['name'] ?? '' }}" class="h-10 mx-auto object-contain mb-2">
                    @endif
                    <p class="text-xs text-gray-600 font-medium truncate">{{ $partner['name'] ?? 'Unnamed' }}</p>
                    <form action="{{ route('admin.cms.partners.delete', $i) }}" method="POST" class="mt-2" onsubmit="return confirm('Remove this partner?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Remove</button>
                    </form>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
