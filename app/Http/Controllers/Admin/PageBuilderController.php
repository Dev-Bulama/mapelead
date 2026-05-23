<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageBuilderController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('sort_order')->orderBy('created_at', 'desc')->get();
        return view('admin.cms.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.cms.pages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255|unique:pages,slug',
            'content'      => 'nullable|string',
            'status'       => 'required|in:draft,published',
            'meta_title'   => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $data['user_id'] = auth()->id();

        if (empty($data['slug'])) {
            unset($data['slug']); // let SlugOptions generate it
        }

        $page = Page::create($data);
        return redirect()->route('admin.cms.pages.index')->with('success', 'Page created successfully.');
    }

    public function show($id)
    {
        $page = Page::findOrFail($id);
        return view('admin.cms.pages.show', compact('page'));
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);
        return view('admin.cms.pages.edit', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'content'      => 'nullable|string',
            'status'       => 'required|in:draft,published',
            'meta_title'   => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $page->update($data);
        return redirect()->route('admin.cms.pages.index')->with('success', 'Page updated.');
    }

    public function destroy($id)
    {
        Page::findOrFail($id)->delete();
        return redirect()->route('admin.cms.pages.index')->with('success', 'Page deleted.');
    }
}
