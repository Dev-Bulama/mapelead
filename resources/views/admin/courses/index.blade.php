@extends('layouts.admin')

@section('title', 'Courses')

@section('content')
<div class="space-y-5">

    {{-- ═══════════════════════════════════════════════════════════════
         PAGE HEADER
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Courses</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ number_format($courses->total()) }} {{ Str::plural('course', $courses->total()) }} total
            </p>
        </div>
        <a href="{{ route('admin.courses.create') }}"
           class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white
                  text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Course
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         FILTER BAR
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.courses.index') }}"
              class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">

            {{-- Search --}}
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search course title…"
                       class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg
                              focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
            </div>

            {{-- Category filter --}}
            <div class="shrink-0">
                <select name="category"
                        class="w-full sm:w-44 text-sm border border-gray-200 rounded-lg px-3 py-2
                               focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none
                               bg-white text-gray-700 transition">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Status filter --}}
            <div class="shrink-0">
                <select name="status"
                        class="w-full sm:w-36 text-sm border border-gray-200 rounded-lg px-3 py-2
                               focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none
                               bg-white text-gray-700 transition">
                    <option value="">All Statuses</option>
                    @foreach(['draft','published','under_review','archived'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $st)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                    class="shrink-0 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium
                           px-4 py-2 rounded-lg transition-colors">
                Filter
            </button>

            @if(request()->hasAny(['search','category','status']))
                <a href="{{ route('admin.courses.index') }}"
                   class="shrink-0 text-sm text-gray-500 hover:text-gray-700 px-3 py-2 rounded-lg
                          border border-gray-200 hover:border-gray-300 transition-colors">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         COURSES TABLE
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="admin-table w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-5 py-3.5 text-left font-medium">Course</th>
                        <th class="px-4 py-3.5 text-left font-medium hidden md:table-cell">Instructor</th>
                        <th class="px-4 py-3.5 text-left font-medium hidden lg:table-cell">Category</th>
                        <th class="px-4 py-3.5 text-center font-medium">Type</th>
                        <th class="px-4 py-3.5 text-center font-medium">Status</th>
                        <th class="px-4 py-3.5 text-right font-medium hidden md:table-cell">Students</th>
                        <th class="px-4 py-3.5 text-right font-medium">Price</th>
                        <th class="px-5 py-3.5 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($courses as $course)
                        <tr class="hover:bg-gray-50 transition-colors">

                            {{-- Thumbnail + Title --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    @if($course->thumbnail)
                                        <img src="{{ asset('storage/'.$course->thumbnail) }}"
                                             class="w-12 h-9 object-cover rounded-lg shrink-0 bg-gray-100"
                                             alt="">
                                    @else
                                        <div class="w-12 h-9 rounded-lg bg-brand-50 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 truncate max-w-[200px]">
                                            {{ $course->title }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ ucfirst($course->level) }}
                                            @if($course->duration_hours)
                                                &middot; {{ $course->duration_hours }}h
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Instructor --}}
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <p class="text-gray-700 truncate max-w-[120px]">
                                    {{ $course->instructor->user->first_name ?? 'N/A' }}
                                    {{ $course->instructor->user->last_name ?? '' }}
                                </p>
                            </td>

                            {{-- Category --}}
                            <td class="px-4 py-3.5 hidden lg:table-cell">
                                <p class="text-gray-600 text-xs truncate max-w-[120px]">
                                    {{ $course->category->name ?? 'Uncategorized' }}
                                </p>
                            </td>

                            {{-- Type badge --}}
                            <td class="px-4 py-3.5 text-center">
                                @php
                                    $typeBadge = match($course->type) {
                                        'online'   => 'bg-blue-100 text-blue-700',
                                        'physical' => 'bg-green-100 text-green-700',
                                        'hybrid'   => 'bg-purple-100 text-purple-700',
                                        default    => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $typeBadge }}">
                                    {{ ucfirst($course->type) }}
                                </span>
                            </td>

                            {{-- Status badge --}}
                            <td class="px-4 py-3.5 text-center">
                                @php
                                    $statusBadge = match($course->status) {
                                        'published'    => 'bg-green-100 text-green-700 ring-1 ring-green-200',
                                        'draft'        => 'bg-gray-100 text-gray-500',
                                        'under_review' => 'bg-yellow-100 text-yellow-700 ring-1 ring-yellow-200',
                                        'archived'     => 'bg-red-100 text-red-700',
                                        default        => 'bg-gray-100 text-gray-500',
                                    };
                                    $statusDot = match($course->status) {
                                        'published'    => 'bg-green-500',
                                        'under_review' => 'bg-yellow-500',
                                        'archived'     => 'bg-red-500',
                                        default        => 'bg-gray-400',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusBadge }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }}"></span>
                                    {{ ucwords(str_replace('_', ' ', $course->status)) }}
                                </span>
                            </td>

                            {{-- Enrollments --}}
                            <td class="px-4 py-3.5 text-right hidden md:table-cell">
                                <span class="font-semibold text-gray-800">
                                    {{ number_format($course->enrollments_count) }}
                                </span>
                                <span class="text-xs text-gray-400"> enrolled</span>
                            </td>

                            {{-- Price --}}
                            <td class="px-4 py-3.5 text-right">
                                @if($course->is_free)
                                    <span class="text-green-600 font-semibold text-xs">Free</span>
                                @else
                                    <div>
                                        <p class="font-semibold text-gray-800 whitespace-nowrap">
                                            ₦{{ number_format($course->discount_price ?? $course->price, 0) }}
                                        </p>
                                        @if($course->discount_price && $course->discount_price < $course->price)
                                            <p class="text-xs text-gray-400 line-through">
                                                ₦{{ number_format($course->price, 0) }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">

                                    {{-- View on site --}}
                                    <a href="{{ route('courses.show', $course->slug) }}"
                                       target="_blank"
                                       class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                       title="View on site">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.courses.edit', $course->id) }}"
                                       class="p-1.5 text-gray-400 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-colors"
                                       title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    {{-- Publish / Unpublish --}}
                                    @if($course->status === 'published')
                                        <form method="POST"
                                              action="{{ route('admin.courses.unpublish', $course->id) }}"
                                              onsubmit="return confirm('Unpublish this course?')">
                                            @csrf
                                            <button type="submit"
                                                    class="p-1.5 text-gray-400 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-colors"
                                                    title="Unpublish">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST"
                                              action="{{ route('admin.courses.publish', $course->id) }}"
                                              onsubmit="return confirm('Publish this course? It will be visible to all students.')">
                                            @csrf
                                            <button type="submit"
                                                    class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                                    title="Publish">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Delete --}}
                                    <form method="POST"
                                          action="{{ route('admin.courses.destroy', $course->id) }}"
                                          onsubmit="return confirm('Delete \'{{ addslashes($course->title) }}\'? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <p class="text-sm font-medium">No courses found</p>
                                    @if(request()->hasAny(['search','category','status']))
                                        <a href="{{ route('admin.courses.index') }}"
                                           class="text-xs text-brand-600 hover:underline">Clear filters</a>
                                    @else
                                        <a href="{{ route('admin.courses.create') }}"
                                           class="text-xs text-brand-600 hover:underline">Create your first course</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($courses->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between gap-4">
                <p class="text-xs text-gray-400">
                    Showing
                    <span class="font-medium text-gray-600">{{ $courses->firstItem() }}</span>
                    to
                    <span class="font-medium text-gray-600">{{ $courses->lastItem() }}</span>
                    of
                    <span class="font-medium text-gray-600">{{ $courses->total() }}</span>
                    courses
                </p>
                <div>
                    {{ $courses->appends(request()->query())->links('vendor.pagination.simple-tailwind') }}
                </div>
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         QUICK STATS BAR
    ═══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $allCourses        = \App\Models\Course::withCount('enrollments');
            $publishedCount    = (clone $allCourses)->where('status', 'published')->count();
            $draftCount        = (clone $allCourses)->where('status', 'draft')->count();
            $reviewCount       = (clone $allCourses)->where('status', 'under_review')->count();
            $archivedCount     = (clone $allCourses)->where('status', 'archived')->count();
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center shrink-0">
                <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
            </div>
            <div>
                <p class="text-lg font-bold text-gray-900">{{ $publishedCount }}</p>
                <p class="text-xs text-gray-400">Published</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-gray-50 flex items-center justify-center shrink-0">
                <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            </div>
            <div>
                <p class="text-lg font-bold text-gray-900">{{ $draftCount }}</p>
                <p class="text-xs text-gray-400">Drafts</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-yellow-50 flex items-center justify-center shrink-0">
                <span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>
            </div>
            <div>
                <p class="text-lg font-bold text-gray-900">{{ $reviewCount }}</p>
                <p class="text-xs text-gray-400">Under Review</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center shrink-0">
                <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
            </div>
            <div>
                <p class="text-lg font-bold text-gray-900">{{ $archivedCount }}</p>
                <p class="text-xs text-gray-400">Archived</p>
            </div>
        </div>
    </div>

</div>
@endsection
