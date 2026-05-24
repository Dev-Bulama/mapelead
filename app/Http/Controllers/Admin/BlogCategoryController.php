<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::withCount('posts')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        return view('admin.blog.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = BlogCategory::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.blog.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100|unique:blog_categories,name',
            'description'      => 'nullable|string|max:500',
            'color'            => 'nullable|string|max:20',
            'parent_id'        => 'nullable|exists:blog_categories,id',
            'sort_order'       => 'nullable|integer|min:0',
            'is_active'        => 'boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        BlogCategory::create($data);

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Category created.');
    }

    public function show(BlogCategory $category)
    {
        $category->load(['posts' => fn($q) => $q->latest()->limit(20)]);
        return view('admin.blog.categories.show', compact('category'));
    }

    public function edit(BlogCategory $category)
    {
        $parents = BlogCategory::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();
        return view('admin.blog.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, BlogCategory $category)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100|unique:blog_categories,name,' . $category->id,
            'description'      => 'nullable|string|max:500',
            'color'            => 'nullable|string|max:20',
            'parent_id'        => 'nullable|exists:blog_categories,id',
            'sort_order'       => 'nullable|integer|min:0',
            'is_active'        => 'boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $category->update($data);

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(BlogCategory $category)
    {
        if ($category->posts()->count() > 0) {
            return back()->with('error', 'Cannot delete a category with posts. Reassign posts first.');
        }
        $category->delete();
        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Category deleted.');
    }
}
