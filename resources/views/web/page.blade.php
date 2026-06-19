@extends('layouts.app')
@section('title', $page->meta_title ?? $page->title)
@section('meta_description', $page->meta_description ?? '')

@push('styles')
<style>
/* CMS Page Typography */
.cms-content h1 { font-size: 2rem; font-weight: 700; margin: 1.5rem 0 0.75rem; color: #111827; line-height: 1.3; }
.cms-content h2 { font-size: 1.5rem; font-weight: 700; margin: 2rem 0 0.75rem; color: #111827; line-height: 1.3; }
.cms-content h3 { font-size: 1.25rem; font-weight: 600; margin: 1.5rem 0 0.5rem; color: #1f2937; }
.cms-content h4 { font-size: 1.1rem; font-weight: 600; margin: 1.25rem 0 0.5rem; color: #374151; }
.cms-content p { margin: 0 0 1rem; line-height: 1.75; color: #374151; }
.cms-content ul { list-style: disc; padding-left: 1.5rem; margin: 0.75rem 0 1rem; }
.cms-content ol { list-style: decimal; padding-left: 1.5rem; margin: 0.75rem 0 1rem; }
.cms-content li { margin-bottom: 0.4rem; line-height: 1.7; color: #374151; }
.cms-content a { color: #14215B; text-decoration: underline; }
.cms-content a:hover { color: #2a42af; }
.cms-content strong, .cms-content b { font-weight: 700; color: #111827; }
.cms-content em, .cms-content i { font-style: italic; }
.cms-content blockquote { border-left: 4px solid #2a42af; padding: 0.75rem 1.25rem; margin: 1.5rem 0; background: #f8f9ff; color: #374151; font-style: italic; border-radius: 0 0.5rem 0.5rem 0; }
.cms-content code { background: #f3f4f6; padding: 0.15rem 0.35rem; border-radius: 0.25rem; font-family: monospace; font-size: 0.9em; color: #14215B; }
.cms-content pre { background: #1e293b; color: #e2e8f0; padding: 1.25rem; border-radius: 0.75rem; overflow-x: auto; margin: 1.5rem 0; }
.cms-content pre code { background: none; color: inherit; padding: 0; }
.cms-content table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; font-size: 0.9rem; }
.cms-content th { background: #14215B; color: white; padding: 0.6rem 0.9rem; text-align: left; font-weight: 600; }
.cms-content td { padding: 0.6rem 0.9rem; border-bottom: 1px solid #e5e7eb; color: #374151; }
.cms-content tr:nth-child(even) td { background: #f9fafb; }
.cms-content img { max-width: 100%; height: auto; border-radius: 0.75rem; margin: 1rem 0; }
.cms-content hr { border: none; border-top: 2px solid #e5e7eb; margin: 2rem 0; }
</style>
@endpush

@section('content')

{{-- Hero --}}
@if($page->show_hero ?? true)
    <div class="bg-brand-950 text-white py-14">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-display font-bold">{{ $page->title }}</h1>
            @if($page->excerpt)
                <p class="text-brand-200 mt-3 text-lg">{{ $page->excerpt }}</p>
            @endif
        </div>
    </div>
@endif

{{-- Page Content --}}
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

    @if($page->content)
        <div class="cms-content text-gray-700 leading-relaxed">
            {!! $page->content !!}
        </div>
    @endif

    {{-- Page Sections (if any) --}}
    @foreach($page->sections ?? [] as $section)
        <div class="mt-12 pt-10 border-t border-gray-100">
            @if($section->title)
                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $section->title }}</h2>
            @endif
            <div class="cms-content text-gray-700 leading-relaxed">
                {!! $section->content !!}
            </div>
        </div>
    @endforeach

</div>

@endsection
