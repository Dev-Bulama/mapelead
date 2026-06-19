<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Batch;
use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\HeroBanner;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Services\CMS\SettingsService;

class HomeController extends Controller
{
    public function __construct(private SettingsService $settings) {}

    public function index()
    {
        $data = [
            'settings'        => $this->settings->group('homepage'),
            'generalSettings' => $this->settings->group('general'),
            'heroBanners'     => HeroBanner::where('is_active', true)->orderBy('sort_order')->get(),

            'featuredCourses' => Course::published()
                ->featured()
                ->with(['instructor.user', 'category', 'courseInstructors.instructor.user'])
                ->limit(6)
                ->get(),

            'categories' => CourseCategory::where('is_active', true)
                ->withCount(['courses' => fn($q) => $q->published()])
                ->orderBy('sort_order')
                ->limit(8)
                ->get(),

            // All active testimonials (featured first) — removed false ->featured() requirement
            'testimonials' => Testimonial::where('is_active', true)
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->limit(6)
                ->get(),

            'faqs' => Faq::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->limit(10)
                ->get(),

            'blogPosts' => BlogPost::where('status', 'published')
                ->with('author', 'category')
                ->latest('published_at')
                ->limit(3)
                ->get(),

            'teamMembers' => TeamMember::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->limit(8)
                ->get(),

            'galleryItems' => GalleryItem::where('is_active', true)
                ->orderBy('sort_order')
                ->limit(12)
                ->get(),

            'services' => Service::where('is_active', true)
                ->orderBy('sort_order')
                ->limit(8)
                ->get(),

            // Active & upcoming batches for class-status indicators
            'activeBatches'   => Batch::where('status', 'active')->with('course')->get(),
            'upcomingBatches' => Batch::where('status', 'upcoming')->with('course')->limit(5)->get(),

            // News ticker (type=ticker announcements)
            'newsTickers' => Announcement::where('is_active', true)
                ->where('type', 'ticker')
                ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
                ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),

            'stats' => [
                'students'           => SiteSetting::get('stat_students', '10,000+'),
                'courses'            => SiteSetting::get('stat_courses', '50+'),
                'instructors'        => SiteSetting::get('stat_instructors', '25+'),
                'placement'          => SiteSetting::get('stat_placement', '92%'),
                'students_label'     => SiteSetting::get('stat_students_label', 'Students Trained'),
                'courses_label'      => SiteSetting::get('stat_courses_label', 'Expert Courses'),
                'instructors_label'  => SiteSetting::get('stat_instructors_label', 'Industry Instructors'),
                'placement_label'    => SiteSetting::get('stat_placement_label', 'Job Placement Rate'),
            ],
        ];

        return view('web.home', $data);
    }

    public function about()
    {
        return view('web.about', [
            'settings'    => $this->settings->group('general'),
            'testimonials'=> Testimonial::where('is_active', true)->orderByDesc('is_featured')->limit(4)->get(),
            'teamMembers' => TeamMember::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function gallery(\Illuminate\Http\Request $request)
    {
        $currentCategory = $request->get('category');

        $categories = GalleryItem::active()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $items = GalleryItem::active()
            ->when($currentCategory, fn($q) => $q->where('category', $currentCategory))
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(24)
            ->withQueryString();

        return view('web.gallery', compact('items', 'categories', 'currentCategory'));
    }

    public function sitemap()
    {
        $courses = Course::published()->select('slug', 'updated_at')->get();
        $posts   = BlogPost::where('status', 'published')->select('slug', 'updated_at')->get();
        return response()->view('web.sitemap', compact('courses', 'posts'))
            ->header('Content-Type', 'text/xml');
    }
}
