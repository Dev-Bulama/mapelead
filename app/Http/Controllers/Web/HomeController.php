<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Testimonial;
use App\Models\Faq;
use App\Models\BlogPost;
use App\Models\SiteSetting;
use App\Services\CMS\SettingsService;

class HomeController extends Controller
{
    public function __construct(private SettingsService $settings) {}

    public function index()
    {
        $data = [
            'settings'     => $this->settings->group('homepage'),
            'generalSettings' => $this->settings->group('general'),
            'featuredCourses' => Course::published()->featured()->with(['instructor.user', 'category'])->limit(6)->get(),
            'categories'   => CourseCategory::active()->featured()->withCount(['courses' => fn($q) => $q->published()])->orderBy('sort_order')->limit(8)->get(),
            'testimonials' => Testimonial::active()->featured()->orderBy('sort_order')->limit(6)->get(),
            'faqs'         => Faq::active()->limit(8)->get(),
            'blogPosts'    => BlogPost::published()->with('author', 'category')->latest('published_at')->limit(3)->get(),
            'stats'        => [
                'students'    => SiteSetting::get('stat_students', '10,000+'),
                'courses'     => SiteSetting::get('stat_courses', '50+'),
                'instructors' => SiteSetting::get('stat_instructors', '25+'),
                'placement'   => SiteSetting::get('stat_placement', '92%'),
            ],
        ];

        return view('web.home', $data);
    }

    public function about()
    {
        return view('web.about', [
            'settings' => $this->settings->group('general'),
            'testimonials' => Testimonial::active()->featured()->limit(4)->get(),
        ]);
    }

    public function sitemap()
    {
        $courses = Course::published()->select('slug', 'updated_at')->get();
        $posts   = BlogPost::published()->select('slug', 'updated_at')->get();
        return response()->view('web.sitemap', compact('courses', 'posts'))
            ->header('Content-Type', 'text/xml');
    }
}
