<?php
namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Services\Course\CourseService;
use Illuminate\Http\Request;

class InstructorCourseController extends Controller
{
    public function __construct(private CourseService $courseService) {}

    public function index()
    {
        $instructor = auth()->user()->instructor;
        $courses = Course::where('instructor_id', $instructor->id)
            ->with('category')
            ->withCount('enrollments')
            ->orderByDesc('created_at')
            ->paginate(12);
        return view('instructor.courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = CourseCategory::active()->orderBy('name')->get();
        return view('instructor.courses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'             => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'description'       => 'required|string',
            'category_id'       => 'required|exists:course_categories,id',
            'type'              => 'required|in:online,physical,hybrid',
            'level'             => 'required|in:beginner,intermediate,advanced,all_levels',
            'price'             => 'required|numeric|min:0',
            'discount_price'    => 'nullable|numeric|lt:price',
            'duration_hours'    => 'nullable|integer|min:1',
            'thumbnail'         => 'nullable|image|max:2048',
        ]);

        $data['instructor_id'] = auth()->user()->instructor->id;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        $course = $this->courseService->create($data);
        return redirect()->route('instructor.courses.show', $course->id)->with('success', 'Course created!');
    }

    public function show(Course $course)
    {
        $this->authorizeInstructor($course);
        return view('instructor.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $this->authorizeInstructor($course);
        $categories = CourseCategory::active()->orderBy('name')->get();
        return view('instructor.courses.edit', compact('course', 'categories'));
    }

    public function update(Request $request, Course $course)
    {
        $this->authorizeInstructor($course);
        $data = $request->validate([
            'title'             => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'description'       => 'required|string',
            'category_id'       => 'required|exists:course_categories,id',
            'type'              => 'required|in:online,physical,hybrid',
            'level'             => 'required|in:beginner,intermediate,advanced,all_levels',
            'price'             => 'required|numeric|min:0',
            'discount_price'    => 'nullable|numeric',
            'thumbnail'         => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        $this->courseService->update($course, $data);
        return back()->with('success', 'Course updated!');
    }

    public function destroy(Course $course)
    {
        $this->authorizeInstructor($course);
        $course->delete();
        return redirect()->route('instructor.courses.index')->with('success', 'Course deleted!');
    }

    private function authorizeInstructor(Course $course): void
    {
        if ($course->instructor->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }
    }
}
