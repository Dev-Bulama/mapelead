<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\HeroBanner;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CmsController extends Controller
{
    private static array $defaultFeatures = [
        ['title' => 'Industry-Led Curriculum', 'desc' => 'Our programs are designed in collaboration with tech leaders at top companies. Every course maps directly to real job requirements.', 'color' => 'bg-blue-50 text-blue-600', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
        ['title' => 'Expert Instructors', 'desc' => 'Learn from senior engineers, CTOs, and product leaders with 10+ years of real-world experience at top African and global tech companies.', 'color' => 'bg-purple-50 text-purple-600', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
        ['title' => 'Flexible Learning', 'desc' => 'Study at your own pace with on-demand videos, or join our live cohorts. Access content on any device, anytime — even offline.', 'color' => 'bg-green-50 text-green-600', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['title' => 'Job Placement Support', 'desc' => 'Dedicated career coaches, resume reviews, mock interviews, and direct connections to our 200+ hiring partner companies.', 'color' => 'bg-orange-50 text-orange-600', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ['title' => 'Certificate Programs', 'desc' => 'Earn verifiable blockchain-backed certificates recognized by top employers. Showcase your credentials on LinkedIn with one click.', 'color' => 'bg-yellow-50 text-yellow-600', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['title' => 'Community & Networking', 'desc' => 'Join a vibrant community of 10,000+ peers. Collaborate on projects, attend events, and build a professional network that opens doors.', 'color' => 'bg-pink-50 text-pink-600', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
    ];

    public function index()
    {
        $heroBanners  = HeroBanner::orderBy('sort_order')->get();
        $testimonials = Testimonial::orderBy('sort_order')->get();
        $faqs         = Faq::orderBy('sort_order')->get();

        $homepageSettings = SiteSetting::getGroup('homepage');
        $featuresJson     = $homepageSettings['why_choose_us_features'] ?? null;
        $currentFeatures  = $featuresJson ? json_decode($featuresJson, true) : self::$defaultFeatures;
        $wcuSettings      = [
            'badge'    => $homepageSettings['why_choose_us_badge']    ?? 'Our Difference',
            'title'    => $homepageSettings['why_choose_us_title']    ?? 'Why 10,000+ Students Choose MapeLearn',
            'subtitle' => $homepageSettings['why_choose_us_subtitle'] ?? "We've built every aspect of our platform with one goal: getting you hired and growing your career faster.",
        ];

        return view('admin.cms.index', compact('heroBanners', 'testimonials', 'faqs', 'currentFeatures', 'wcuSettings'));
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

    // ── Why Choose Us Features ────────────────────────────────────────

    public function updateFeatures(Request $request)
    {
        $features = [];
        for ($i = 0; $i < 6; $i++) {
            $features[] = [
                'title' => $request->input("feature_{$i}_title", ''),
                'desc'  => $request->input("feature_{$i}_desc", ''),
                'color' => $request->input("feature_{$i}_color", 'bg-blue-50 text-blue-600'),
                'icon'  => $request->input("feature_{$i}_icon", self::$defaultFeatures[$i]['icon'] ?? ''),
            ];
        }

        SiteSetting::updateOrCreate(
            ['key' => 'why_choose_us_features'],
            ['group' => 'homepage', 'value' => json_encode($features, JSON_UNESCAPED_UNICODE)]
        );

        foreach (['why_choose_us_badge', 'why_choose_us_title', 'why_choose_us_subtitle'] as $key) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['group' => 'homepage', 'value' => $request->input($key, '')]
            );
        }

        Cache::forget('settings_group_homepage');
        foreach (['why_choose_us_features', 'why_choose_us_badge', 'why_choose_us_title', 'why_choose_us_subtitle'] as $key) {
            Cache::forget("setting_{$key}");
        }

        return back()->with('success', 'Why Choose Us section updated!');
    }
}
