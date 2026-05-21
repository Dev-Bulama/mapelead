<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Instructor;
use App\Services\Course\CourseService;
use Illuminate\Http\Request;

class CourseManagementController extends Controller
{
    public function __construct(private CourseService $courseService) {}

    public function index(Request $request)
    {
        $courses = Course::with(['instructor.user', 'category'])
            ->when($request->search, fn($q, $s) => $q->where('title', 'like', "%$s%"))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->category, fn($q, $c) => $q->where('category_id', $c))
            ->withCount('enrollments')
            ->orderByDesc('created_at')
            ->paginate(15);

        $categories = CourseCategory::active()->get();
        return view('admin.courses.index', compact('courses', 'categories'));
    }

    public function create()
    {
        $categories  = CourseCategory::active()->get();
        $instructors = Instructor::with('user')->get();
        return view('admin.courses.create', compact('categories', 'instructors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'             => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'description'       => 'required|string',
            'category_id'       => 'required|exists:course_categories,id',
            'instructor_id'     => 'required|exists:instructors,id',
            'type'              => 'required|in:online,physical,hybrid',
            'level'             => 'required|in:beginner,intermediate,advanced,all_levels',
            'price'             => 'required|numeric|min:0',
            'discount_price'    => 'nullable|numeric',
            'is_free'           => 'boolean',
            'thumbnail'         => 'nullable|image|max:4096',
            'duration_hours'    => 'nullable|integer',
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        $course = $this->courseService->create($data);
        return redirect()->route('admin.courses.show', $course->id)->with('success', 'Course created!');
    }

    public function show(Course $course)
    {
        $course->load(['instructor.user', 'category', 'modules.lessons', 'enrollments']);
        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $categories  = CourseCategory::active()->get();
        $instructors = Instructor::with('user')->get();
        return view('admin.courses.edit', compact('course', 'categories', 'instructors'));
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'title'             => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'description'       => 'required|string',
            'category_id'       => 'required|exists:course_categories,id',
            'instructor_id'     => 'required|exists:instructors,id',
            'type'              => 'required|in:online,physical,hybrid',
            'level'             => 'required|in:beginner,intermediate,advanced,all_levels',
            'price'             => 'required|numeric|min:0',
            'thumbnail'         => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        $this->courseService->update($course, $data);
        return back()->with('success', 'Course updated!');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Course deleted!');
    }

    public function publish(int $id)
    {
        $course = Course::findOrFail($id);
        $this->courseService->publish($course);
        return back()->with('success', 'Course published!');
    }

    public function unpublish(int $id)
    {
        Course::findOrFail($id)->update(['status' => 'draft', 'is_published' => false]);
        return back()->with('success', 'Course unpublished!');
    }
}
