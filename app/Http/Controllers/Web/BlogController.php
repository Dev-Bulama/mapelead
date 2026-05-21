<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $posts = BlogPost::published()
            ->with(['author', 'category', 'tags'])
            ->when($request->search, fn($q, $s) => $q->where('title', 'like', "%$s%"))
            ->latest('published_at')
            ->paginate(9);

        $categories = BlogCategory::withCount(['posts' => fn($q) => $q->published()])->where('is_active', true)->get();
        $featured   = BlogPost::published()->featured()->latest('published_at')->limit(3)->get();

        return view('web.blog.index', compact('posts', 'categories', 'featured'));
    }

    public function show(string $slug)
    {
        $post = BlogPost::published()->with(['author', 'category', 'tags', 'comments.user'])->where('slug', $slug)->firstOrFail();
        $post->increment('views');
        $related = BlogPost::published()->where('category_id', $post->category_id)->where('id', '!=', $post->id)->latest('published_at')->limit(3)->get();
        return view('web.blog.show', compact('post', 'related'));
    }

    public function category(string $slug)
    {
        $category = BlogCategory::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $posts = BlogPost::published()->where('category_id', $category->id)->with(['author', 'category'])->latest('published_at')->paginate(9);
        $categories = BlogCategory::withCount(['posts' => fn($q) => $q->published()])->where('is_active', true)->get();
        return view('web.blog.index', compact('posts', 'categories', 'category'));
    }

    public function tag(string $slug)
    {
        $tag = BlogTag::where('slug', $slug)->firstOrFail();
        $posts = $tag->posts()->published()->with(['author', 'category'])->latest('published_at')->paginate(9);
        $categories = BlogCategory::withCount(['posts' => fn($q) => $q->published()])->where('is_active', true)->get();
        return view('web.blog.index', compact('posts', 'categories', 'tag'));
    }

    public function comment(string $slug, Request $request)
    {
        $request->validate(['content' => 'required|string|max:2000']);
        $post = BlogPost::where('slug', $slug)->firstOrFail();

        $post->comments()->create([
            'content'    => $request->content,
            'user_id'    => auth()->id(),
            'name'       => auth()->check() ? auth()->user()->full_name : $request->name,
            'email'      => auth()->check() ? auth()->user()->email : $request->email,
            'ip_address' => $request->ip(),
            'status'     => 'approved',
        ]);

        return back()->with('success', 'Comment posted successfully!');
    }
}
