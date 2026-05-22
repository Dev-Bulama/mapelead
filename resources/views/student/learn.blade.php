@extends('layouts.student')
@section('title', 'Learning: ' . $course->title)
@section('page_title', $course->title)

@section('content')
<div class="flex flex-col lg:flex-row gap-0 -m-4 sm:-m-6 h-full">

    {{-- Curriculum Sidebar --}}
    <div x-data="{ open: true }" class="lg:w-80 xl:w-96 shrink-0 bg-white border-r border-gray-200 overflow-y-auto" style="max-height: calc(100vh - 64px)">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-900 text-sm">Course Content</h2>
            <button @click="open = !open" class="lg:hidden text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Progress Bar --}}
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
            <div class="flex justify-between text-xs text-gray-600 mb-1.5">
                <span>Your Progress</span>
                <span class="font-semibold">{{ $enrollment->progress_percent }}%</span>
            </div>
            <div class="bg-gray-200 rounded-full h-2">
                <div class="bg-brand-600 h-2 rounded-full transition-all duration-500" style="width: {{ $enrollment->progress_percent }}%"></div>
            </div>
            <p class="text-xs text-gray-400 mt-1">{{ count($completedLessons) }} of {{ $course->modules->sum(fn($m) => $m->lessons->count()) }} lessons completed</p>
        </div>

        {{-- Modules & Lessons --}}
        <div class="divide-y divide-gray-50">
            @foreach($course->modules as $moduleIndex => $module)
                @php
                    $moduleLessons = $module->lessons;
                    $moduleCompleted = $moduleLessons->filter(fn($l) => in_array($l->id, $completedLessons))->count();
                @endphp
                <div x-data="{ expanded: {{ $moduleIndex === 0 ? 'true' : 'false' }} }">
                    <button @click="expanded = !expanded"
                            class="w-full flex items-center justify-between px-4 py-3 hover:bg-gray-50 text-left">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $module->title }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $moduleCompleted }}/{{ $moduleLessons->count() }} • {{ $moduleLessons->sum('duration_minutes') }} min</p>
                        </div>
                        <svg :class="expanded ? 'rotate-180' : ''" class="w-4 h-4 text-gray-400 transition-transform shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="expanded" x-cloak>
                        @foreach($moduleLessons as $lesson)
                            @php $isCompleted = in_array($lesson->id, $completedLessons); $isCurrent = $currentLesson?->id === $lesson->id; @endphp
                            <a href="{{ route('student.learn', [$course->slug]) }}?lesson={{ $lesson->id }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors
                                      {{ $isCurrent ? 'bg-brand-50 border-r-2 border-brand-600' : 'hover:bg-gray-50' }}">
                                {{-- Completion icon --}}
                                <div class="shrink-0 w-5 h-5 rounded-full flex items-center justify-center
                                            {{ $isCompleted ? 'bg-green-500' : ($isCurrent ? 'bg-brand-600' : 'bg-gray-200') }}">
                                    @if($isCompleted)
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @elseif($isCurrent)
                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="{{ $isCurrent ? 'text-brand-700 font-semibold' : ($isCompleted ? 'text-gray-500 line-through' : 'text-gray-700') }} text-xs truncate">
                                        {{ $lesson->title }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $lesson->duration_minutes ? $lesson->duration_minutes . ' min' : '' }}
                                    </p>
                                </div>
                                {{-- Type badge --}}
                                <span class="shrink-0 text-xs text-gray-400 capitalize">{{ $lesson->type }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Main Lesson Area --}}
    <div class="flex-1 overflow-y-auto p-4 sm:p-6" style="max-height: calc(100vh - 64px)">
        @if($currentLesson)
            <div x-data="lessonPlayer({{ $currentLesson->id }}, '{{ route('student.lesson.complete', [$course->slug, $currentLesson->id]) }}')"
                 x-init="init()">

                {{-- Lesson Header --}}
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">{{ $currentLesson->type }}</p>
                        <h1 class="text-xl font-bold text-gray-900 mt-0.5">{{ $currentLesson->title }}</h1>
                    </div>
                    @if(in_array($currentLesson->id, $completedLessons))
                        <span class="flex items-center gap-1.5 bg-green-50 text-green-700 text-sm font-medium px-3 py-1.5 rounded-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Completed
                        </span>
                    @endif
                </div>

                {{-- Video Player --}}
                @if($currentLesson->video_url)
                    <div class="aspect-video bg-black rounded-2xl overflow-hidden mb-6">
                        @if(str_contains($currentLesson->video_url, 'youtube.com') || str_contains($currentLesson->video_url, 'youtu.be'))
                            @php
                                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $currentLesson->video_url, $matches);
                                $videoId = $matches[1] ?? '';
                            @endphp
                            <iframe src="https://www.youtube.com/embed/{{ $videoId }}?enablejsapi=1"
                                    class="w-full h-full" frameborder="0" allowfullscreen
                                    @ended="markComplete()"></iframe>
                        @elseif(str_contains($currentLesson->video_url, 'vimeo.com'))
                            <iframe src="{{ str_replace('vimeo.com/', 'player.vimeo.com/video/', $currentLesson->video_url) }}"
                                    class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                        @else
                            <video controls class="w-full h-full" @ended="markComplete()">
                                <source src="{{ $currentLesson->video_url }}">
                            </video>
                        @endif
                    </div>
                @endif

                {{-- Text Content --}}
                @if($currentLesson->content)
                    <div class="prose prose-lg max-w-none bg-white rounded-2xl p-6 border border-gray-100 mb-6">
                        {!! $currentLesson->content !!}
                    </div>
                @endif

                {{-- Attachment --}}
                @if($currentLesson->attachment)
                    <div class="flex items-center gap-3 bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6">
                        <svg class="w-8 h-8 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <div>
                            <p class="text-sm font-semibold text-blue-800">Lesson Resource</p>
                            <a href="{{ asset('storage/' . $currentLesson->attachment) }}" download class="text-xs text-blue-600 hover:underline">Download attachment</a>
                        </div>
                    </div>
                @endif

                {{-- Mark Complete Button --}}
                @if(!in_array($currentLesson->id, $completedLessons))
                    <button @click="markComplete()" :disabled="completing"
                            class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3.5 rounded-xl transition-colors flex items-center justify-center gap-2 disabled:opacity-60">
                        <svg x-show="completing" x-cloak class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                        <svg x-show="!completing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="completing ? 'Marking complete...' : 'Mark as Complete'"></span>
                    </button>
                @else
                    <div class="w-full bg-green-50 border border-green-200 text-green-700 font-semibold py-3.5 rounded-xl text-center">
                        ✓ Lesson Completed
                    </div>
                @endif

                {{-- Navigation --}}
                <div class="flex justify-between mt-6">
                    @php
                        $allLessons = $course->modules->flatMap->lessons->values();
                        $currentIndex = $allLessons->search(fn($l) => $l->id === $currentLesson->id);
                        $prevLesson = $currentIndex > 0 ? $allLessons[$currentIndex - 1] : null;
                        $nextLesson = $currentIndex < $allLessons->count() - 1 ? $allLessons[$currentIndex + 1] : null;
                    @endphp
                    @if($prevLesson)
                        <a href="{{ route('student.learn', $course->slug) }}?lesson={{ $prevLesson->id }}"
                           class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-brand-600 bg-white border border-gray-200 px-4 py-2.5 rounded-xl hover:border-brand-300 transition-colors">
                            ← Previous
                        </a>
                    @else
                        <div></div>
                    @endif
                    @if($nextLesson)
                        <a href="{{ route('student.learn', $course->slug) }}?lesson={{ $nextLesson->id }}"
                           class="flex items-center gap-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 px-4 py-2.5 rounded-xl transition-colors">
                            Next →
                        </a>
                    @endif
                </div>
            </div>
        @else
            <div class="text-center py-16">
                <p class="text-gray-400">Select a lesson to start learning.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function lessonPlayer(lessonId, completeUrl) {
    return {
        lessonId: lessonId,
        completeUrl: completeUrl,
        completing: false,
        init() {},
        markComplete() {
            this.completing = true;
            fetch(this.completeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ watch_time: 0 }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) window.location.reload();
            })
            .catch(() => { this.completing = false; });
        }
    }
}
</script>
@endpush
