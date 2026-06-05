<?php
namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Instructor;
use App\Services\Course\CourseService;
use Illuminate\Http\Request;

class InstructorCourseController extends Controller
{
    public function __construct(private CourseService $courseService) {}

    public function index()
    {
        $instructor = $this->resolvedInstructor();
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
            'title'               => 'required|string|max:255',
            'short_description'   => 'required|string|max:500',
            'description'         => 'required|string',
            'category_id'         => 'required|exists:course_categories,id',
            'type'                => 'required|in:online,physical,hybrid',
            'level'               => 'required|in:beginner,intermediate,advanced,all_levels',
            'price'               => 'required|numeric|min:0',
            'discount_price'      => 'nullable|numeric',
            'is_free'             => 'boolean',
            'thumbnail'           => 'nullable|image|max:4096',
            'duration_hours'      => 'nullable|integer|min:1',
            'duration_weeks'      => 'nullable|integer|min:1',
            'language'            => 'nullable|string|max:50',
            'promo_video'         => 'nullable|url|max:500',
            'promo_video_file'    => 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg|max:204800',
            'requirements'        => 'nullable|string',
            'what_you_learn'      => 'nullable|string',
            'who_is_this_for'     => 'nullable|string',
            'certificate_enabled' => 'boolean',
        ]);

        $data['instructor_id']      = $this->resolvedInstructor()->id;
        $data['is_free']            = $request->boolean('is_free');
        $data['certificate_enabled']= $request->boolean('certificate_enabled');
        $data = $this->convertTextareaToArrays($data);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }
        if ($request->hasFile('promo_video_file')) {
            $data['promo_video'] = $request->file('promo_video_file')->store('courses/promo', 'public');
        }
        unset($data['promo_video_file']);

        $course = $this->courseService->create($data);
        $this->saveCurriculum($course, $request->input('modules', []));

        return redirect()->route('instructor.courses.show', $course->id)->with('success', 'Course created! You can now add modules and lessons.');
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
            'title'               => 'required|string|max:255',
            'short_description'   => 'required|string|max:500',
            'description'         => 'required|string',
            'category_id'         => 'required|exists:course_categories,id',
            'type'                => 'required|in:online,physical,hybrid',
            'level'               => 'required|in:beginner,intermediate,advanced,all_levels',
            'price'               => 'required|numeric|min:0',
            'discount_price'      => 'nullable|numeric',
            'is_free'             => 'boolean',
            'thumbnail'           => 'nullable|image|max:4096',
            'duration_hours'      => 'nullable|integer|min:1',
            'duration_weeks'      => 'nullable|integer|min:1',
            'language'            => 'nullable|string|max:50',
            'promo_video'         => 'nullable|url|max:500',
            'promo_video_file'    => 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg|max:204800',
            'requirements'        => 'nullable|string',
            'what_you_learn'      => 'nullable|string',
            'who_is_this_for'     => 'nullable|string',
            'certificate_enabled' => 'boolean',
        ]);

        $data['is_free']            = $request->boolean('is_free');
        $data['certificate_enabled']= $request->boolean('certificate_enabled');
        $data = $this->convertTextareaToArrays($data);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }
        if ($request->hasFile('promo_video_file')) {
            $data['promo_video'] = $request->file('promo_video_file')->store('courses/promo', 'public');
        }
        unset($data['promo_video_file']);

        $this->courseService->update($course, $data);
        return back()->with('success', 'Course updated!');
    }

    public function destroy(Course $course)
    {
        $this->authorizeInstructor($course);
        $course->delete();
        return redirect()->route('instructor.courses.index')->with('success', 'Course deleted!');
    }

    private function resolvedInstructor(): Instructor
    {
        return Instructor::firstOrCreate(['user_id' => auth()->id()]);
    }

    private function authorizeInstructor(Course $course): void
    {
        if ($course->instructor_id !== $this->resolvedInstructor()->id && !auth()->user()->isAdmin()) {
            abort(403);
        }
    }

    private function saveCurriculum(\App\Models\Course $course, array $modules): void
    {
        foreach ($modules as $i => $moduleData) {
            $moduleTitle = trim($moduleData['title'] ?? '');
            if ($moduleTitle === '') continue;

            $module = $course->modules()->create([
                'title'           => $moduleTitle,
                'sort_order'      => $i + 1,
                'is_free_preview' => !empty($moduleData['is_free_preview']),
            ]);

            foreach ($moduleData['lessons'] ?? [] as $j => $lessonData) {
                $lessonTitle = trim($lessonData['title'] ?? '');
                if ($lessonTitle === '') continue;

                $module->lessons()->create([
                    'course_id'  => $course->id,
                    'title'      => $lessonTitle,
                    'type'       => $lessonData['type'] ?? 'video',
                    'sort_order' => $j + 1,
                ]);
            }
        }
    }

    private function convertTextareaToArrays(array $data): array
    {
        foreach (['requirements', 'what_you_learn', 'who_is_this_for'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $lines = array_values(array_filter(
                    array_map('trim', explode("\n", $data[$field]))
                ));
                $data[$field] = $lines ?: null;
            }
        }
        return $data;
    }
}
