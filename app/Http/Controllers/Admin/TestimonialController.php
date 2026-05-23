<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::orderBy('sort_order')->get();
        return view('admin.cms.testimonials', compact('testimonials'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'title'       => 'nullable|string|max:100',
            'company'     => 'nullable|string|max:100',
            'content'     => 'required|string|max:1000',
            'rating'      => 'required|integer|between:1,5',
            'avatar'      => 'nullable|image|max:2048',
            'is_featured' => 'nullable|boolean',
            'is_active'   => 'nullable|boolean',
            'sort_order'  => 'nullable|integer',
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active');

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('testimonials', 'public');
        }

        Testimonial::create($data);
        return back()->with('success', 'Testimonial added successfully.');
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'title'       => 'nullable|string|max:100',
            'company'     => 'nullable|string|max:100',
            'content'     => 'required|string|max:1000',
            'rating'      => 'required|integer|between:1,5',
            'avatar'      => 'nullable|image|max:2048',
            'is_featured' => 'nullable|boolean',
            'is_active'   => 'nullable|boolean',
            'sort_order'  => 'nullable|integer',
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active');

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('testimonials', 'public');
        }

        $testimonial->update($data);
        return back()->with('success', 'Testimonial updated successfully.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return back()->with('success', 'Testimonial deleted.');
    }

    public function toggleFeatured(Testimonial $testimonial)
    {
        $testimonial->update(['is_featured' => !$testimonial->is_featured]);
        return back();
    }

    // Stubs to satisfy resource route (not used in our views)
    public function create() { return redirect()->route('admin.testimonials.index'); }
    public function show($id) { return redirect()->route('admin.testimonials.index'); }
    public function edit($id) { return redirect()->route('admin.testimonials.index'); }
}
