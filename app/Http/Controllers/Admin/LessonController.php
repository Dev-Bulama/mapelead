<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseModule;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index(CourseModule $module)
    {
        $module->load('course');
        $lessons = $module->lessons()->orderBy('sort_order')->get();
        return view('admin.lessons.index', compact('module', 'lessons'));
    }

    public function create(CourseModule $module)
    {
        $module->load('course');
        return view('admin.lessons.create', compact('module'));
    }

    public function store(Request $request, CourseModule $module)
    {
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

        return redirect()->route('admin.modules.lessons.index', $module)
            ->with('success', 'Lesson created.');
    }

    public function show(Lesson $lesson)
    {
        $lesson->load('module.course');
        return view('admin.lessons.show', compact('lesson'));
    }

    public function edit(Lesson $lesson)
    {
        $lesson->load('module.course');
        return view('admin.lessons.edit', compact('lesson'));
    }

    public function update(Request $request, Lesson $lesson)
    {
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

        return redirect()->route('admin.modules.lessons.index', $lesson->module_id)
            ->with('success', 'Lesson updated.');
    }

    public function destroy(Lesson $lesson)
    {
        $moduleId = $lesson->module_id;
        $lesson->delete();

        return redirect()->route('admin.modules.lessons.index', $moduleId)
            ->with('success', 'Lesson deleted.');
    }
}
