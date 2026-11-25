<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Instructor;
use App\Models\Lesson;
use Illuminate\Http\Request;

class InstructorContentController extends Controller
{
    // ── Modules ───────────────────────────────────────────────────────────────

    public function storeModule(Request $request, Course $course)
    {
        $this->authorizeInstructor($course);

        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string|max:1000',
            'is_free_preview' => 'boolean',
        ]);

        $data['course_id']       = $course->id;
        $data['sort_order']      = $course->modules()->max('sort_order') + 1;
        $data['is_free_preview'] = $request->boolean('is_free_preview');

        CourseModule::create($data);

        return back()->with('success', 'Module added.');
    }

    public function updateModule(Request $request, Course $course, CourseModule $module)
    {
        $this->authorizeInstructor($course);
        abort_if($module->course_id !== $course->id, 404);

        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string|max:1000',
            'sort_order'      => 'nullable|integer|min:0',
            'is_free_preview' => 'boolean',
        ]);
        $data['is_free_preview'] = $request->boolean('is_free_preview');
        $module->update($data);

        return back()->with('success', 'Module updated.');
    }

    public function destroyModule(Course $course, CourseModule $module)
    {
        $this->authorizeInstructor($course);
        abort_if($module->course_id !== $course->id, 404);

        $module->lessons()->delete();
        $module->delete();

        return back()->with('success', 'Module deleted.');
    }

    // ── Lessons ───────────────────────────────────────────────────────────────

    public function storeLesson(Request $request, Course $course, CourseModule $module)
    {
        $this->authorizeInstructor($course);
        abort_if($module->course_id !== $course->id, 404);

        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'type'             => 'required|in:video,text,quiz,assignment,live',
            'content'          => 'nullable|string',
            'video_url'        => 'nullable|url|max:500',
            'video_provider'   => 'nullable|in:youtube,vimeo,wistia,bunny,other',
            'duration_minutes' => 'nullable|integer|min:1',
            'is_free_preview'  => 'boolean',
            'is_published'     => 'boolean',
        ]);

        $data['module_id']       = $module->id;
        $data['course_id']       = $module->course_id;
        $data['sort_order']      = $module->lessons()->max('sort_order') + 1;
        $data['is_free_preview'] = $request->boolean('is_free_preview');
        $data['is_published']    = $request->boolean('is_published');

        Lesson::create($data);

        return back()->with('success', 'Lesson added.');
    }

    public function updateLesson(Request $request, Course $course, Lesson $lesson)
    {
        $this->authorizeInstructor($course);
        abort_if($lesson->course_id !== $course->id, 404);

        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'type'             => 'required|in:video,text,quiz,assignment,live',
            'content'          => 'nullable|string',
            'video_url'        => 'nullable|url|max:500',
            'video_provider'   => 'nullable|in:youtube,vimeo,wistia,bunny,other',
            'duration_minutes' => 'nullable|integer|min:1',
            'sort_order'       => 'nullable|integer|min:0',
            'is_free_preview'  => 'boolean',
            'is_published'     => 'boolean',
        ]);
        $data['is_free_preview'] = $request->boolean('is_free_preview');
        $data['is_published']    = $request->boolean('is_published');
        $lesson->update($data);

        return back()->with('success', 'Lesson updated.');
    }

    public function destroyLesson(Course $course, Lesson $lesson)
    {
        $this->authorizeInstructor($course);
        abort_if($lesson->course_id !== $course->id, 404);

        $lesson->delete();

        return back()->with('success', 'Lesson deleted.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

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
}
