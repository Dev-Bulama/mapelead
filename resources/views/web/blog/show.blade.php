@extends('layouts.app')
@section('title', $post->meta_title ?? $post->title)
@section('meta_description', $post->meta_description ?? $post->excerpt)
@section('og_image', $post->featured_image_url)

@section('content')

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col lg:flex-row gap-10">

        {{-- Article --}}
        <article class="flex-1 min-w-0">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-1.5 text-sm text-gray-400 mb-6">
                <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a>
                <span>›</span>
                <a href="{{ route('blog.index') }}" class="hover:text-brand-600">Blog</a>
                <span>›</span>
                <a href="{{ route('blog.category', $post->category->slug) }}" class="hover:text-brand-600">{{ $post->category->name }}</a>
                <span>›</span>
                <span class="text-gray-700 truncate max-w-xs">{{ $post->title }}</span>
            </nav>

            {{-- Category + Tags --}}
            <div class="flex flex-wrap gap-2 mb-4">
                <a href="{{ route('blog.category', $post->category->slug) }}"
                   class="bg-brand-50 text-brand-700 text-xs font-semibold px-3 py-1 rounded-full">{{ $post->category->name }}</a>
                @foreach($post->tags as $tag)
                    <a href="{{ route('blog.tag', $tag->slug) }}"
                       class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full hover:bg-gray-200 transition-colors">#{{ $tag->name }}</a>
                @endforeach
            </div>

            {{-- Title --}}
            <h1 class="text-3xl lg:text-4xl font-display font-bold text-gray-900 leading-tight">{{ $post->title }}</h1>

            {{-- Meta --}}
            <div class="flex flex-wrap items-center gap-4 mt-4 pb-5 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->full_name }}" class="w-9 h-9 rounded-full object-cover">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $post->author->full_name }}</p>
                    </div>
                </div>
                <span class="text-gray-300">·</span>
                <span class="text-sm text-gray-500">{{ $post->published_at?->format('F j, Y') }}</span>
                @if($post->read_time_minutes)
                    <span class="text-gray-300">·</span>
                    <span class="text-sm text-gray-500">{{ $post->read_time_minutes }} min read</span>
                @endif
                <span class="text-gray-300">·</span>
                <span class="text-sm text-gray-500">{{ number_format($post->views) }} views</span>
            </div>

            {{-- Featured Image --}}
            <div class="mt-6 mb-8 rounded-2xl overflow-hidden aspect-video bg-gray-100">
                <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
            </div>

            {{-- Article Content --}}
            <div class="prose prose-lg max-w-none prose-headings:font-bold prose-headings:text-gray-900 prose-a:text-brand-600 prose-img:rounded-xl">
                {!! $post->content !!}
            </div>

            {{-- Share --}}
            <div class="mt-10 pt-6 border-t border-gray-100">
                <p class="text-sm font-semibold text-gray-700 mb-3">Share this article:</p>
                <div class="flex gap-3">
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" target="_blank"
                       class="flex items-center gap-2 bg-black text-white text-sm font-medium px-4 py-2 rounded-xl hover:bg-gray-800 transition-colors">
                        Twitter/X
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank"
                       class="flex items-center gap-2 bg-blue-600 text-white text-sm font-medium px-4 py-2 rounded-xl hover:bg-blue-700 transition-colors">
                        Facebook
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank"
                       class="flex items-center gap-2 bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-xl hover:bg-blue-800 transition-colors">
                        LinkedIn
                    </a>
                    <button onclick="navigator.clipboard.writeText('{{ url()->current() }}')"
                            class="flex items-center gap-2 border border-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-xl hover:bg-gray-50 transition-colors">
                        Copy Link
                    </button>
                </div>
            </div>

            {{-- Comments --}}
            @if($post->allow_comments)
                <div class="mt-10 pt-6 border-t border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">
                        {{ $post->comments->count() }} Comment{{ $post->comments->count() !== 1 ? 's' : '' }}
                    </h3>

                    @foreach($post->comments as $comment)
                        <div class="flex gap-4 mb-6">
                            <img src="{{ $comment->user?->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($comment->name ?? 'A').'&background=6366f1&color=fff' }}"
                                 alt="{{ $comment->name }}" class="w-10 h-10 rounded-full object-cover shrink-0">
                            <div class="flex-1 bg-gray-50 rounded-2xl p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-semibold text-gray-900 text-sm">{{ $comment->name ?? $comment->user?->full_name }}</span>
                                    <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-700 text-sm">{{ $comment->content }}</p>
                            </div>
                        </div>
                    @endforeach

                    {{-- Comment Form --}}
                    <div class="bg-white rounded-2xl border border-gray-100 p-6">
                        <h4 class="font-bold text-gray-900 mb-4">Leave a Comment</h4>
                        <form action="{{ route('blog.comment', $post->slug) }}" method="POST" class="space-y-3">
                            @csrf
                            @guest
                                <div class="grid grid-cols-2 gap-3">
                                    <input type="text" name="name" required placeholder="Your name" class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                    <input type="email" name="email" placeholder="Your email" class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                </div>
                            @endguest
                            <textarea name="content" rows="4" required placeholder="Share your thoughts..."
                                      class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors text-sm">
                                Post Comment
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </article>

        {{-- Sidebar --}}
        <aside class="lg:w-72 shrink-0 space-y-6">
            {{-- Author Bio --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center">
                <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->full_name }}" class="w-16 h-16 rounded-full object-cover mx-auto mb-3 ring-2 ring-brand-100">
                <h3 class="font-bold text-gray-900">{{ $post->author->full_name }}</h3>
                @if($post->author->bio)
                    <p class="text-gray-500 text-xs mt-2 line-clamp-3">{{ $post->author->bio }}</p>
                @endif
            </div>

            {{-- Related Posts --}}
            @if($related->isNotEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Related Articles</h3>
                    <div class="space-y-4">
                        @foreach($related as $rel)
                            <a href="{{ route('blog.show', $rel->slug) }}" class="flex gap-3 group">
                                <img src="{{ $rel->featured_image_url }}" alt="{{ $rel->title }}" class="w-16 h-12 object-cover rounded-lg shrink-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 group-hover:text-brand-600 line-clamp-2 transition-colors">{{ $rel->title }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $rel->published_at?->format('M d') }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </aside>
    </div>
</div>
@endsection
