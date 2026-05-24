<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(Course $course)
    {
        $modules = $course->modules()->withCount('lessons')->orderBy('sort_order')->get();
        return view('admin.modules.index', compact('course', 'modules'));
    }

    public function create(Course $course)
    {
        return view('admin.modules.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'is_free_preview'=> 'boolean',
        ]);

        $data['course_id']   = $course->id;
        $data['sort_order']  = $course->modules()->max('sort_order') + 1;
        $data['is_free_preview'] = $request->boolean('is_free_preview');

        CourseModule::create($data);

        return redirect()->route('admin.courses.modules.index', $course)
            ->with('success', 'Module created.');
    }

    public function show(CourseModule $module)
    {
        $module->load(['course', 'lessons' => fn($q) => $q->orderBy('sort_order')]);
        return view('admin.modules.show', compact('module'));
    }

    public function edit(CourseModule $module)
    {
        return view('admin.modules.edit', compact('module'));
    }

    public function update(Request $request, CourseModule $module)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'sort_order'     => 'nullable|integer|min:0',
            'is_free_preview'=> 'boolean',
        ]);
        $data['is_free_preview'] = $request->boolean('is_free_preview');
        $module->update($data);

        return redirect()->route('admin.courses.modules.index', $module->course_id)
            ->with('success', 'Module updated.');
    }

    public function destroy(CourseModule $module)
    {
        $courseId = $module->course_id;
        $module->lessons()->delete();
        $module->delete();

        return redirect()->route('admin.courses.modules.index', $courseId)
            ->with('success', 'Module deleted.');
    }
}
