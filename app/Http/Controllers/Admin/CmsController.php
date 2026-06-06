<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\HeroBanner;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'sort_order'          => 'nullable|integer|min:0',
            'is_active'           => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if (isset($data['id'])) {
                $existing = HeroBanner::find($data['id']);
                if ($existing?->image) Storage::disk('public')->delete($existing->image);
            }
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        HeroBanner::updateOrCreate(['id' => $data['id'] ?? null], $data);
        return back()->with('success', 'Hero banner saved!');
    }

    public function destroyHeroBanner(int $id)
    {
        $banner = HeroBanner::findOrFail($id);
        if ($banner->image) Storage::disk('public')->delete($banner->image);
        $banner->delete();
        return back()->with('success', 'Banner deleted!');
    }

    // ── Testimonials ──────────────────────────────────────────────────────────

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
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active', true);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('testimonials', 'public');
        }

        Testimonial::create($data);
        return back()->with('success', 'Testimonial added!');
    }

    public function updateTestimonial(Request $request, int $id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'title'       => 'nullable|string|max:100',
            'company'     => 'nullable|string|max:100',
            'content'     => 'required|string|max:1000',
            'rating'      => 'required|integer|between:1,5',
            'is_featured' => 'boolean',
            'is_active'   => 'boolean',
            'avatar'      => 'nullable|image|max:2048',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active', true);

        if ($request->hasFile('avatar')) {
            if ($testimonial->avatar) Storage::disk('public')->delete($testimonial->avatar);
            $data['avatar'] = $request->file('avatar')->store('testimonials', 'public');
        }

        $testimonial->update($data);
        return back()->with('success', 'Testimonial updated!');
    }

    public function destroyTestimonial(int $id)
    {
        $testimonial = Testimonial::findOrFail($id);
        if ($testimonial->avatar) Storage::disk('public')->delete($testimonial->avatar);
        $testimonial->delete();
        return back()->with('success', 'Testimonial deleted!');
    }

    // ── FAQs ─────────────────────────────────────────────────────────────────

    public function storeFaq(Request $request)
    {
        $data = $request->validate([
            'question'   => 'required|string|max:500',
            'answer'     => 'required|string',
            'category'   => 'nullable|string|max:100',
            'is_active'  => 'boolean',
            'is_featured'=> 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['is_active']   = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured');

        Faq::create($data);
        return back()->with('success', 'FAQ added!');
    }

    public function updateFaq(Request $request, int $id)
    {
        $faq = Faq::findOrFail($id);
        $data = $request->validate([
            'question'   => 'required|string|max:500',
            'answer'     => 'required|string',
            'category'   => 'nullable|string|max:100',
            'is_active'  => 'boolean',
            'is_featured'=> 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['is_active']   = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured');

        $faq->update($data);
        return back()->with('success', 'FAQ updated!');
    }

    public function destroyFaq(int $id)
    {
        Faq::findOrFail($id)->delete();
        return back()->with('success', 'FAQ deleted!');
    }
}
