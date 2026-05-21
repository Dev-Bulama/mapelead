<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ url('/') }}</loc><changefreq>daily</changefreq><priority>1.0</priority></url>
    <url><loc>{{ url('/courses') }}</loc><changefreq>daily</changefreq><priority>0.9</priority></url>
    <url><loc>{{ url('/about') }}</loc><changefreq>monthly</changefreq><priority>0.8</priority></url>
    <url><loc>{{ url('/blog') }}</loc><changefreq>daily</changefreq><priority>0.8</priority></url>
    <url><loc>{{ url('/contact') }}</loc><changefreq>monthly</changefreq><priority>0.7</priority></url>
    @foreach($courses as $course)
    <url><loc>{{ url('/courses/'.$course->slug) }}</loc><lastmod>{{ $course->updated_at->toAtomString() }}</lastmod><changefreq>weekly</changefreq><priority>0.9</priority></url>
    @endforeach
    @foreach($posts as $post)
    <url><loc>{{ url('/blog/'.$post->slug) }}</loc><lastmod>{{ $post->updated_at->toAtomString() }}</lastmod><changefreq>monthly</changefreq><priority>0.6</priority></url>
    @endforeach
</urlset>
