<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseCategory;
use Illuminate\Http\Request;

class CourseCategoryController extends Controller
{
    public function index()
    {
        $categories = CourseCategory::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.course-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:50',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $data['is_active']   = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured');

        CourseCategory::create($data);
        return back()->with('success', 'Category created!');
    }

    public function update(Request $request, CourseCategory $courseCategory)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:50',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $data['is_active']   = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        $courseCategory->update($data);
        return back()->with('success', 'Category updated!');
    }

    public function destroy(CourseCategory $courseCategory)
    {
        $courseCategory->delete();
        return back()->with('success', 'Category deleted!');
    }
}
