@extends('layouts.app')
@section('title', isset($category) ? $category->name . ' — Blog' : (isset($tag) ? $tag->name . ' — Blog' : 'Blog — MapeLearn'))

@section('content')

{{-- Hero --}}
<div class="bg-brand-950 text-white py-12">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h1 class="text-4xl font-display font-bold">
            @isset($category) {{ $category->name }}
            @elseif(isset($tag)) Posts tagged: {{ $tag->name }}
            @else Learning Hub & Insights @endisset
        </h1>
        <p class="text-brand-300 mt-2">Expert articles, career tips, and tech industry insights</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex flex-col lg:flex-row gap-10">

        {{-- Main Posts --}}
        <main class="flex-1">
            {{-- Search --}}
            <form action="{{ route('blog.index') }}" method="GET" class="flex gap-2 mb-8">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search articles..."
                       class="flex-1 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                <button type="submit" class="bg-brand-600 text-white px-5 py-3 rounded-xl hover:bg-brand-700 transition-colors">Search</button>
            </form>

            @if($posts->isEmpty())
                <div class="text-center py-12 bg-white rounded-2xl border border-gray-100">
                    <p class="text-gray-400">No articles found.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($posts as $post)
                        <article class="bg-white rounded-2xl border border-gray-100 overflow-hidden card-hover">
                            <a href="{{ route('blog.show', $post->slug) }}" class="block aspect-video bg-gray-100 overflow-hidden">
                                <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}"
                                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            </a>
                            <div class="p-5">
                                <div class="flex items-center gap-2 mb-3">
                                    <a href="{{ route('blog.category', $post->category->slug) }}"
                                       class="text-xs bg-brand-50 text-brand-700 font-medium px-2.5 py-1 rounded-full hover:bg-brand-100 transition-colors">
                                        {{ $post->category->name }}
                                    </a>
                                    @if($post->read_time_minutes)
                                        <span class="text-xs text-gray-400">{{ $post->read_time_minutes }} min read</span>
                                    @endif
                                </div>
                                <h2 class="font-bold text-gray-900 line-clamp-2 hover:text-brand-600 transition-colors">
                                    <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                </h2>
                                @if($post->excerpt)
                                    <p class="text-gray-500 text-sm mt-2 line-clamp-2">{{ $post->excerpt }}</p>
                                @endif
                                <div class="flex items-center gap-3 mt-4 pt-3 border-t border-gray-50">
                                    <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->full_name }}" class="w-7 h-7 rounded-full object-cover">
                                    <div>
                                        <p class="text-xs font-medium text-gray-700">{{ $post->author->full_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $post->published_at?->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mt-8">{{ $posts->links() }}</div>
            @endif
        </main>

        {{-- Sidebar --}}
        <aside class="lg:w-72 shrink-0 space-y-6">
            {{-- Categories --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="font-bold text-gray-900 mb-4">Categories</h3>
                <div class="space-y-2">
                    @foreach($categories as $cat)
                        <a href="{{ route('blog.category', $cat->slug) }}"
                           class="flex items-center justify-between text-sm text-gray-600 hover:text-brand-600 py-1.5 border-b border-gray-50 transition-colors {{ isset($category) && $category->id === $cat->id ? 'text-brand-600 font-semibold' : '' }}">
                            <span>{{ $cat->name }}</span>
                            <span class="bg-gray-100 text-gray-500 text-xs px-2 py-0.5 rounded-full">{{ $cat->posts_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Featured Posts --}}
            @if(isset($featured) && $featured->isNotEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-900 mb-4">Featured Articles</h3>
                    <div class="space-y-4">
                        @foreach($featured as $fp)
                            <a href="{{ route('blog.show', $fp->slug) }}" class="flex gap-3 group">
                                <img src="{{ $fp->featured_image_url }}" alt="{{ $fp->title }}"
                                     class="w-16 h-12 object-cover rounded-lg shrink-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 group-hover:text-brand-600 line-clamp-2 transition-colors">{{ $fp->title }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $fp->published_at?->format('M d, Y') }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Newsletter signup --}}
            <div class="bg-brand-600 rounded-2xl p-5 text-white">
                <h3 class="font-bold mb-2">Get Weekly Insights</h3>
                <p class="text-brand-100 text-sm mb-4">No spam. Unsubscribe anytime.</p>
                <form action="{{ route('newsletter.subscribe') }}" method="POST">
                    @csrf
                    <input type="email" name="email" required placeholder="Your email"
                           class="w-full bg-white/10 border border-white/20 rounded-xl px-3 py-2.5 text-white placeholder-white/60 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-white">
                    <button type="submit" class="w-full bg-white text-brand-700 font-semibold py-2.5 rounded-xl hover:bg-brand-50 transition-colors text-sm">
                        Subscribe
                    </button>
                </form>
            </div>
        </aside>
    </div>
</div>
@endsection
