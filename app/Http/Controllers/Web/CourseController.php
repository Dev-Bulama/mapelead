<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CourseCategory;
use App\Services\Course\CourseService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(private CourseService $courseService) {}

    public function index(Request $request)
    {
        $courses = $this->courseService->getAllPublished($request->all());
        $categories = $this->courseService->getCategories();
        return view('web.courses.index', compact('courses', 'categories'));
    }

    public function category(string $slug)
    {
        $category = CourseCategory::active()->where('slug', $slug)->firstOrFail();
        $courses  = $this->courseService->getAllPublished(['category' => $slug]);
        $categories = $this->courseService->getCategories();
        return view('web.courses.index', compact('courses', 'categories', 'category'));
    }

    public function show(string $slug)
    {
        $course = $this->courseService->findBySlug($slug);
        $isEnrolled = auth()->check()
            ? app(\App\Services\Student\EnrollmentService::class)->checkEnrollment(auth()->user(), $course)
            : null;
        $relatedCourses = \App\Models\Course::published()
            ->where('category_id', $course->category_id)
            ->where('id', '!=', $course->id)
            ->limit(3)->get();
        return view('web.courses.show', compact('course', 'isEnrolled', 'relatedCourses'));
    }

    public function review(string $slug, Request $request)
    {
        $request->validate(['rating' => 'required|integer|between:1,5', 'body' => 'nullable|string|max:1000']);
        $course = \App\Models\Course::where('slug', $slug)->firstOrFail();
        $enrollment = \App\Models\Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)->where('status', 'active')->firstOrFail();

        \App\Models\CourseReview::updateOrCreate(
            ['user_id' => auth()->id(), 'course_id' => $course->id],
            array_merge($request->only(['rating', 'title', 'body']), [
                'enrollment_id' => $enrollment->id,
                'is_approved' => true,
            ])
        );

        return back()->with('success', 'Review submitted successfully!');
    }
}
