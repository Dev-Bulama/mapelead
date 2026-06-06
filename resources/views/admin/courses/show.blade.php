@extends('layouts.admin')
@section('title', 'Course: ' . $course->title)

@section('content')
<div class="space-y-6" x-data="{ openModule: null }">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="w-20 h-14 rounded-xl overflow-hidden bg-gray-100 shrink-0">
                <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $course->title }}</h2>
                    @php
                        $statusColors = ['published'=>'bg-green-100 text-green-700','draft'=>'bg-gray-100 text-gray-600','rejected'=>'bg-red-100 text-red-700','pending_review'=>'bg-yellow-100 text-yellow-700'];
                    @endphp
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$course->status] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ ucwords(str_replace('_', ' ', $course->status)) }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-1">{{ $course->category->name ?? '—' }} • {{ ucfirst($course->type) }} • {{ ucwords(str_replace('_', ' ', $course->level)) }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            @if($course->status !== 'published')
                <form action="{{ route('admin.courses.publish', $course->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">Publish</button>
                </form>
            @else
                <form action="{{ route('admin.courses.unpublish', $course->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">Unpublish</button>
                </form>
            @endif
            <a href="{{ route('admin.courses.edit', $course->id) }}"
               class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">Edit</a>
            <a href="{{ route('admin.courses.index') }}"
               class="border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-xl hover:text-gray-900 transition-colors">← Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Stats --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-200 p-5 space-y-4">
                <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide">Course Details</h3>
                @php
                    $totalLessons = $course->modules->sum(fn($m) => $m->lessons->count());
                    $totalDuration = $course->modules->flatMap(fn($m) => $m->lessons)->sum('duration_minutes');
                @endphp
                @foreach([
                    ['label' => 'Lead Instructor', 'value' => $course->instructor->user->full_name ?? '—'],
                    ['label' => 'Enrollments', 'value' => $course->enrollments->count()],
                    ['label' => 'Modules', 'value' => $course->modules->count()],
                    ['label' => 'Lessons', 'value' => $totalLessons],
                    ['label' => 'Duration', 'value' => $totalDuration ? round($totalDuration / 60, 1) . ' hrs' : ($course->duration_hours ? $course->duration_hours . ' hrs' : '—')],
                    ['label' => 'Price', 'value' => $course->is_free ? 'Free' : '₦' . number_format($course->effective_price, 2)],
                    ['label' => 'Published', 'value' => $course->published_at?->format('M d, Y') ?? 'Not yet'],
                    ['label' => 'Created', 'value' => $course->created_at->format('M d, Y')],
                ] as $row)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 font-medium">{{ $row['label'] }}</span>
                        <span class="text-gray-900 font-medium">{{ $row['value'] }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Session Instructors --}}
            @if($course->courseInstructors->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900 text-sm">Session Instructors</h3>
                    <a href="{{ route('admin.courses.edit', $course->id) }}" class="text-xs text-indigo-600 hover:underline">Edit</a>
                </div>
                @php
                    $sessionIcons = ['morning' => '🌅', 'afternoon' => '☀️', 'evening' => '🌙'];
                    $sessionColors = [
                        'morning'   => 'bg-orange-50 border-orange-200 text-orange-800',
                        'afternoon' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
                        'evening'   => 'bg-indigo-50 border-indigo-200 text-indigo-800',
                    ];
                @endphp
                <div class="space-y-2">
                    @foreach($course->courseInstructors as $si)
                    <div class="flex items-center gap-3 p-3 rounded-xl border {{ $sessionColors[$si->session] ?? 'bg-gray-50 border-gray-200 text-gray-700' }}">
                        <span class="text-lg">{{ $sessionIcons[$si->session] ?? '⏰' }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-wide">{{ ucfirst($si->session) }} Session</p>
                            <p class="text-sm font-medium text-gray-900">{{ $si->instructor->full_name }}</p>
                        </div>
                        @if($si->session_time)
                        <span class="text-xs font-mono font-semibold text-gray-600 shrink-0">{{ $si->formatted_time }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="bg-gray-50 border border-dashed border-gray-300 rounded-2xl p-4 text-center">
                <p class="text-xs text-gray-500">No session instructors assigned.</p>
                <a href="{{ route('admin.courses.edit', $course->id) }}" class="text-xs text-indigo-600 hover:underline mt-1 inline-block">Assign Instructors →</a>
            </div>
            @endif
        </div>

        {{-- Curriculum --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl border border-gray-200">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Curriculum ({{ $course->modules->count() }} modules · {{ $totalLessons }} lessons)</h3>
                    <a href="{{ route('admin.courses.modules.create', $course->id) }}"
                       class="text-xs bg-brand-600 hover:bg-brand-700 text-white font-semibold px-3 py-1.5 rounded-lg transition-colors">
                        + Add Module
                    </a>
                </div>

                @if($course->modules->isEmpty())
                    <div class="p-10 text-center text-gray-500 text-sm">No curriculum yet. Add a module to get started.</div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($course->modules->sortBy('sort_order') as $module)
                            <div>
                                <button @click="openModule === {{ $module->id }} ? openModule = null : openModule = {{ $module->id }}"
                                        class="w-full flex items-center justify-between px-5 py-3.5 hover:bg-gray-50 transition-colors text-left">
                                    <div class="flex items-center gap-3">
                                        <span class="w-7 h-7 bg-brand-100 text-brand-700 rounded-lg flex items-center justify-center text-xs font-bold">
                                            {{ $loop->iteration }}
                                        </span>
                                        <span class="font-medium text-gray-900 text-sm">{{ $module->title }}</span>
                                        <span class="text-xs text-gray-400">({{ $module->lessons->count() }} lessons)</span>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="openModule === {{ $module->id }} ? 'rotate-180' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="openModule === {{ $module->id }}" x-cloak class="bg-gray-50">
                                    @foreach($module->lessons->sortBy('sort_order') as $lesson)
                                        <div class="flex items-center gap-3 px-8 py-2.5 border-t border-gray-100">
                                            @php
                                                $typeIcon = match($lesson->type) {
                                                    'video' => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                                    'quiz'  => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                                    default => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                                };
                                            @endphp
                                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $typeIcon }}"/>
                                            </svg>
                                            <span class="text-sm text-gray-700 flex-1">{{ $lesson->title }}</span>
                                            @if($lesson->is_free)
                                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">Free</span>
                                            @endif
                                            @if($lesson->duration_minutes)
                                                <span class="text-xs text-gray-400">{{ $lesson->duration_minutes }}m</span>
                                            @endif
                                        </div>
                                    @endforeach
                                    <div class="px-8 py-2.5 border-t border-gray-100">
                                        <a href="{{ route('admin.modules.lessons.create', $module->id) }}"
                                           class="text-xs text-brand-600 hover:text-brand-700 font-medium">+ Add Lesson</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Recent Enrollments --}}
            <div class="bg-white rounded-2xl border border-gray-200">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Recent Enrollments ({{ $course->enrollments->count() }})</h3>
                </div>
                @if($course->enrollments->isEmpty())
                    <div class="p-8 text-center text-gray-500 text-sm">No enrollments yet.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left font-semibold text-gray-500 px-5 py-3">Student</th>
                                    <th class="text-left font-semibold text-gray-500 px-5 py-3">Progress</th>
                                    <th class="text-left font-semibold text-gray-500 px-5 py-3">Status</th>
                                    <th class="text-left font-semibold text-gray-500 px-5 py-3">Enrolled</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($course->enrollments->take(8) as $enrollment)
                                    <tr>
                                        <td class="px-5 py-3 font-medium text-gray-900">{{ $enrollment->user->full_name ?? 'N/A' }}</td>
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="w-20 bg-gray-100 rounded-full h-1.5">
                                                    <div class="bg-brand-600 rounded-full h-1.5" style="width: {{ $enrollment->progress_percent }}%"></div>
                                                </div>
                                                <span class="text-xs text-gray-500">{{ $enrollment->progress_percent }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $enrollment->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                                {{ ucfirst($enrollment->status) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-gray-500">{{ $enrollment->enrolled_at?->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
