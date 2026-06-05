<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $items      = GalleryItem::orderBy('sort_order')->orderByDesc('created_at')->get();
        $categories = GalleryItem::whereNotNull('category')->distinct()->pluck('category');
        return view('admin.gallery.index', compact('items', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'nullable|string|max:200',
            'images'     => 'required|array|min:1',
            'images.*'   => 'required|image|max:5120',
            'caption'    => 'nullable|string|max:500',
            'category'   => 'nullable|string|max:100',
            'alt_text'   => 'nullable|string|max:200',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'boolean',
        ]);

        foreach ($request->file('images', []) as $image) {
            GalleryItem::create([
                'title'      => $data['title'] ?? null,
                'image'      => $image->store('gallery', 'public'),
                'caption'    => $data['caption'] ?? null,
                'category'   => $data['category'] ?? null,
                'alt_text'   => $data['alt_text'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active'  => $request->boolean('is_active', true),
            ]);
        }

        return back()->with('success', count($request->file('images', [])) . ' image(s) uploaded.');
    }

    public function update(Request $request, GalleryItem $gallery)
    {
        $data = $request->validate([
            'title'      => 'nullable|string|max:200',
            'image'      => 'nullable|image|max:5120',
            'caption'    => 'nullable|string|max:500',
            'category'   => 'nullable|string|max:100',
            'alt_text'   => 'nullable|string|max:200',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($gallery->image);
            $data['image'] = $request->file('image')->store('gallery', 'public');
        }

        $gallery->update($data);
        return back()->with('success', 'Gallery item updated.');
    }

    public function destroy(GalleryItem $gallery)
    {
        Storage::disk('public')->delete($gallery->image);
        $gallery->delete();
        return back()->with('success', 'Image deleted.');
    }

    public function create() { return redirect()->route('admin.gallery.index'); }
    public function show($id) { return redirect()->route('admin.gallery.index'); }
    public function edit($id) { return redirect()->route('admin.gallery.index'); }
}
