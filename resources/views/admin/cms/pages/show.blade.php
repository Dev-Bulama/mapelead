@extends('layouts.admin')
@section('title', $page->title)

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $page->title }}</h1>
            <p class="text-sm text-gray-500 mt-0.5 font-mono">/{{ $page->slug }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.cms.pages.edit', $page) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium text-white hover:opacity-90 transition"
               style="background-color:#14215B;">
                Edit Page
            </a>
            <a href="{{ route('admin.cms.pages.index') }}"
               class="text-sm text-gray-600 bg-white border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                &larr; Back
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-5">
        <dl class="grid grid-cols-2 gap-4 text-sm mb-6">
            <div>
                <dt class="text-xs text-gray-500">Status</dt>
                <dd class="mt-0.5">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $page->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($page->status) }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">Last Updated</dt>
                <dd class="text-gray-900">{{ $page->updated_at->format('d M Y, H:i') }}</dd>
            </div>
            @if($page->meta_title)
            <div class="col-span-2">
                <dt class="text-xs text-gray-500">Meta Title</dt>
                <dd class="text-gray-900">{{ $page->meta_title }}</dd>
            </div>
            @endif
            @if($page->meta_description)
            <div class="col-span-2">
                <dt class="text-xs text-gray-500">Meta Description</dt>
                <dd class="text-gray-900">{{ $page->meta_description }}</dd>
            </div>
            @endif
        </dl>

        @if($page->content)
        <div class="prose prose-sm max-w-none border-t border-gray-100 pt-5">
            {!! $page->content !!}
        </div>
        @else
        <p class="text-sm text-gray-400 italic">No content yet.</p>
        @endif
    </div>

</div>
@endsection
