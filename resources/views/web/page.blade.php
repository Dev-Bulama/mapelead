@extends('layouts.app')
@section('title', $page->meta_title ?? $page->title)
@section('meta_description', $page->meta_description ?? '')

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
        <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
            {!! $page->content !!}
        </div>
    @endif

    {{-- Page Sections (if any) --}}
    @foreach($page->sections ?? [] as $section)
        <div class="mt-12">
            @if($section->title)
                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $section->title }}</h2>
            @endif
            <div class="text-gray-700 leading-relaxed">
                {!! $section->content !!}
            </div>
        </div>
    @endforeach

</div>

@endsection
