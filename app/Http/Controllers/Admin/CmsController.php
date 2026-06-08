<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\HeroBanner;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CmsController extends Controller
{
    private static array $defaultFeatures = [
        ['icon' => '🎓', 'title' => 'Expert Instructors',    'description' => 'Learn from industry professionals with real-world experience.'],
        ['icon' => '📱', 'title' => 'Learn Anywhere',        'description' => 'Access courses on any device, anytime, at your own pace.'],
        ['icon' => '🏆', 'title' => 'Certified Courses',     'description' => 'Earn certificates recognized by top employers in the industry.'],
        ['icon' => '💼', 'title' => 'Career Support',        'description' => 'Get job placement assistance and career mentoring.'],
        ['icon' => '🤝', 'title' => 'Community',             'description' => 'Join a thriving community of learners and professionals.'],
        ['icon' => '⚡', 'title' => 'Practical Projects',    'description' => 'Build real projects and a portfolio that gets you hired.'],
    ];

    public function index()
    {
        $heroBanners  = HeroBanner::orderBy('sort_order')->get();
        $testimonials = Testimonial::orderBy('sort_order')->get();
        $faqs         = Faq::orderBy('sort_order')->get();

        $wcuSettings = SiteSetting::getGroup('wcu') ?: [];

        $featuresJson    = $wcuSettings['features'] ?? null;
        $currentFeatures = $featuresJson ? json_decode($featuresJson, true) : self::$defaultFeatures;

        $statsSettings = SiteSetting::getGroup('stats') ?: [];
        $stats = [
            'students'           => $statsSettings['stat_students']         ?? '10,000',
            'courses'            => $statsSettings['stat_courses']           ?? '50',
            'instructors'        => $statsSettings['stat_instructors']       ?? '25',
            'placement'          => $statsSettings['stat_placement']         ?? '92%',
            'students_label'     => $statsSettings['stat_students_label']    ?? 'Students Trained',
            'courses_label'      => $statsSettings['stat_courses_label']     ?? 'Expert Courses',
            'instructors_label'  => $statsSettings['stat_instructors_label'] ?? 'Industry Instructors',
            'placement_label'    => $statsSettings['stat_placement_label']   ?? 'Job Placement Rate',
        ];

        $partnerLogosJson = SiteSetting::get('partner_logos');
        $partnerLogos     = $partnerLogosJson ? json_decode($partnerLogosJson, true) : [];

        return view('admin.cms.index', compact(
            'heroBanners', 'testimonials', 'faqs',
            'wcuSettings', 'currentFeatures', 'stats', 'partnerLogos'
        ));
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
        $data['is_active']   = $request->boolean('is_active');

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
            'question'    => 'required|string|max:500',
            'answer'      => 'required|string',
            'category'    => 'nullable|string|max:100',
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
            'sort_order'  => 'nullable|integer|min:0',
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
            'question'    => 'required|string|max:500',
            'answer'      => 'required|string',
            'category'    => 'nullable|string|max:100',
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $data['is_active']   = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        $faq->update($data);
        return back()->with('success', 'FAQ updated!');
    }

    public function destroyFaq(int $id)
    {
        Faq::findOrFail($id)->delete();
        return back()->with('success', 'FAQ deleted!');
    }

    // ── Why Choose Us / Features ──────────────────────────────────────────────

    public function updateFeatures(Request $request)
    {
        $request->validate([
            'badge'                  => 'nullable|string|max:100',
            'title'                  => 'nullable|string|max:255',
            'subtitle'               => 'nullable|string|max:500',
            'features'               => 'required|array|min:1|max:6',
            'features.*.icon'        => 'nullable|string|max:50',
            'features.*.title'       => 'required|string|max:100',
            'features.*.description' => 'nullable|string|max:300',
        ]);

        $this->saveSetting('wcu_badge',    $request->input('badge', ''),    'wcu');
        $this->saveSetting('wcu_title',    $request->input('title', ''),    'wcu');
        $this->saveSetting('wcu_subtitle', $request->input('subtitle', ''), 'wcu');
        $this->saveSetting('features',     json_encode($request->input('features')), 'wcu');

        return back()->with('success', 'Features section updated!');
    }

    // ── Stats ─────────────────────────────────────────────────────────────────

    public function updateStats(Request $request)
    {
        $request->validate([
            'stat_students'          => 'nullable|string|max:20',
            'stat_courses'           => 'nullable|string|max:20',
            'stat_instructors'       => 'nullable|string|max:20',
            'stat_placement'         => 'nullable|string|max:20',
            'stat_students_label'    => 'nullable|string|max:100',
            'stat_courses_label'     => 'nullable|string|max:100',
            'stat_instructors_label' => 'nullable|string|max:100',
            'stat_placement_label'   => 'nullable|string|max:100',
        ]);

        $fields = [
            'stat_students', 'stat_courses', 'stat_instructors', 'stat_placement',
            'stat_students_label', 'stat_courses_label', 'stat_instructors_label', 'stat_placement_label',
        ];

        foreach ($fields as $field) {
            $this->saveSetting($field, $request->input($field, ''), 'stats');
        }

        return back()->with('success', 'Stats updated!');
    }

    // ── Partner Logos ─────────────────────────────────────────────────────────

    public function addPartnerLogo(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'logo' => 'nullable|image|max:2048',
            'url'  => 'nullable|url|max:255',
        ]);

        $existing = SiteSetting::get('partner_logos');
        $logos = $existing ? json_decode($existing, true) : [];

        $entry = ['name' => $request->input('name'), 'url' => $request->input('url', '#')];

        if ($request->hasFile('logo')) {
            $entry['logo'] = $request->file('logo')->store('partners', 'public');
        }

        $logos[] = $entry;
        $this->saveSetting('partner_logos', json_encode($logos), 'homepage');

        return back()->with('success', 'Partner logo added!');
    }

    public function deletePartnerLogo(int $index)
    {
        $existing = SiteSetting::get('partner_logos');
        $logos = $existing ? json_decode($existing, true) : [];

        if (isset($logos[$index])) {
            if (!empty($logos[$index]['logo'])) {
                Storage::disk('public')->delete($logos[$index]['logo']);
            }
            array_splice($logos, $index, 1);
        }

        $this->saveSetting('partner_logos', json_encode($logos), 'homepage');
        return back()->with('success', 'Partner logo removed!');
    }

    private function saveSetting(string $key, $value, string $group): void
    {
        SiteSetting::updateOrCreate(['key' => $key], ['group' => $group, 'value' => $value]);
        \Illuminate\Support\Facades\Cache::forget("setting_{$key}");
        \Illuminate\Support\Facades\Cache::forget("settings_group_{$group}");
    }
}
