<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\HeroBanner;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    public function index()
    {
        $heroBanners  = HeroBanner::orderBy('sort_order')->get();
        $testimonials = Testimonial::orderBy('sort_order')->get();
        $faqs         = Faq::orderBy('sort_order')->get();
        return view('admin.cms.index', compact('heroBanners', 'testimonials', 'faqs'));
    }

    public function updateHeroBanner(Request $request)
    {
        $data = $request->validate([
            'id'                  => 'nullable|exists:hero_banners,id',
            'title'               => 'required|string|max:255',
            'subtitle'            => 'nullable|string|max:255',
            'description'         => 'nullable|string',
            'primary_btn_text'    => 'nullable|string|max:100',
            'primary_btn_url'     => 'nullable|string|max:255',
            'secondary_btn_text'  => 'nullable|string|max:100',
            'secondary_btn_url'   => 'nullable|string|max:255',
            'badge_text'          => 'nullable|string|max:100',
            'image'               => 'nullable|image|max:4096',
            'is_active'           => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        HeroBanner::updateOrCreate(['id' => $data['id'] ?? null], $data);
        return back()->with('success', 'Hero banner updated!');
    }

    public function storeTestimonial(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'title'      => 'nullable|string|max:100',
            'company'    => 'nullable|string|max:100',
            'content'    => 'required|string|max:1000',
            'rating'     => 'required|integer|between:1,5',
            'is_featured'=> 'boolean',
            'is_active'  => 'boolean',
            'avatar'     => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('testimonials', 'public');
        }

        Testimonial::create($data);
        return back()->with('success', 'Testimonial added!');
    }

    public function storeFaq(Request $request)
    {
        $data = $request->validate([
            'question'   => 'required|string|max:500',
            'answer'     => 'required|string',
            'category'   => 'nullable|string|max:100',
            'is_active'  => 'boolean',
            'is_featured'=> 'boolean',
        ]);

        Faq::create($data);
        return back()->with('success', 'FAQ added!');
    }
}
