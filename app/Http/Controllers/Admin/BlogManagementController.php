<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with(['author', 'category'])
            ->when($request->search, fn($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest('published_at');

        $posts      = $query->paginate(20)->withQueryString();
        $categories = BlogCategory::orderBy('name')->get();

        return view('admin.blog.posts.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = BlogCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.blog.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'category_id'      => 'nullable|exists:blog_categories,id',
            'excerpt'          => 'nullable|string|max:500',
            'content'          => 'required|string',
            'status'           => 'required|in:draft,published,scheduled',
            'is_featured'      => 'boolean',
            'allow_comments'   => 'boolean',
            'published_at'     => 'nullable|date',
            'scheduled_at'     => 'nullable|date',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $data['user_id']        = auth()->id();
        $data['is_featured']    = $request->boolean('is_featured');
        $data['allow_comments'] = $request->boolean('allow_comments', true);
        $data['read_time_minutes'] = max(1, (int) (str_word_count(strip_tags($data['content'])) / 200));

        if (empty($data['category_id'])) {
            $data['category_id'] = BlogCategory::firstOrCreate(
                ['slug' => 'uncategorized'],
                ['name' => 'Uncategorized', 'is_active' => true, 'sort_order' => 999]
            )->id;
        }

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        BlogPost::create($data);

        return redirect()->route('admin.blog.posts.index')
            ->with('success', 'Post created.');
    }

    public function show(BlogPost $post)
    {
        $post->load(['author', 'category', 'comments']);
        return view('admin.blog.posts.show', compact('post'));
    }

    public function edit(BlogPost $post)
    {
        $categories = BlogCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.blog.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, BlogPost $post)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'category_id'      => 'nullable|exists:blog_categories,id',
            'excerpt'          => 'nullable|string|max:500',
            'content'          => 'required|string',
            'status'           => 'required|in:draft,published,scheduled',
            'is_featured'      => 'boolean',
            'allow_comments'   => 'boolean',
            'published_at'     => 'nullable|date',
            'scheduled_at'     => 'nullable|date',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $data['is_featured']    = $request->boolean('is_featured');
        $data['allow_comments'] = $request->boolean('allow_comments');
        $data['read_time_minutes'] = max(1, (int) (str_word_count(strip_tags($data['content'])) / 200));

        if (empty($data['category_id'])) {
            $data['category_id'] = BlogCategory::firstOrCreate(
                ['slug' => 'uncategorized'],
                ['name' => 'Uncategorized', 'is_active' => true, 'sort_order' => 999]
            )->id;
        }

        if ($data['status'] === 'published' && ! $post->published_at) {
            $data['published_at'] = now();
        }

        $post->update($data);

        return redirect()->route('admin.blog.posts.index')
            ->with('success', 'Post updated.');
    }

    public function destroy(BlogPost $post)
    {
        $post->delete();
        return redirect()->route('admin.blog.posts.index')
            ->with('success', 'Post deleted.');
    }

    public function publish(int $id)
    {
        $post = BlogPost::findOrFail($id);
        $post->update(['status' => 'published', 'published_at' => $post->published_at ?? now()]);
        return back()->with('success', 'Post published.');
    }
}
