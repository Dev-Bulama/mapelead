<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Models\Course;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(Course $course)
    {
        $course->load('quizzes');
        $quizzes = $course->quizzes;

        return view('admin.lms.quizzes.index', compact('course', 'quizzes'));
    }

    public function create(Course $course)
    {
        return view('admin.lms.quizzes.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title'                        => 'required|string|max:255',
            'description'                  => 'nullable|string',
            'type'                         => 'required|in:practice,graded',
            'pass_score'                   => 'required|numeric|min:0|max:100',
            'max_attempts'                 => 'required|integer|min:1',
            'time_limit_minutes'           => 'nullable|integer|min:1',
            'shuffle_questions'            => 'boolean',
            'show_results'                 => 'boolean',
            'is_required_for_certificate'  => 'boolean',
        ]);

        $validated['course_id']                    = $course->id;
        $validated['shuffle_questions']            = $request->boolean('shuffle_questions');
        $validated['show_results']                 = $request->boolean('show_results');
        $validated['is_required_for_certificate']  = $request->boolean('is_required_for_certificate');

        Quiz::create($validated);

        return redirect()->route('admin.courses.quizzes.index', $course)
            ->with('success', 'Quiz created successfully.');
    }

    public function edit(Quiz $quiz)
    {
        $quiz->load(['questions.options']);

        return view('admin.lms.quizzes.edit', compact('quiz'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title'                        => 'required|string|max:255',
            'description'                  => 'nullable|string',
            'type'                         => 'required|in:practice,graded',
            'pass_score'                   => 'required|numeric|min:0|max:100',
            'max_attempts'                 => 'required|integer|min:1',
            'time_limit_minutes'           => 'nullable|integer|min:1',
            'shuffle_questions'            => 'boolean',
            'show_results'                 => 'boolean',
            'is_required_for_certificate'  => 'boolean',
        ]);

        $validated['shuffle_questions']            = $request->boolean('shuffle_questions');
        $validated['show_results']                 = $request->boolean('show_results');
        $validated['is_required_for_certificate']  = $request->boolean('is_required_for_certificate');

        $quiz->update($validated);

        return redirect()->back()->with('success', 'Quiz updated successfully.');
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return redirect()->back()->with('success', 'Quiz deleted successfully.');
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'question_text'  => 'required|string',
            'type'           => 'required|in:single_choice,multiple_choice,true_false,short_answer',
            'points'         => 'required|integer|min:1',
            'explanation'    => 'nullable|string',
            'options'        => 'nullable|array',
            'options.*.option_text' => 'required_with:options|string',
            'options.*.is_correct'  => 'nullable|boolean',
        ]);

        $question = $quiz->questions()->create([
            'question_text' => $validated['question_text'],
            'type'          => $validated['type'],
            'points'        => $validated['points'],
            'explanation'   => $validated['explanation'] ?? null,
        ]);

        if (!empty($validated['options'])) {
            foreach ($validated['options'] as $optionData) {
                QuizOption::create([
                    'quiz_question_id' => $question->id,
                    'option_text'      => $optionData['option_text'],
                    'is_correct'       => isset($optionData['is_correct']) ? (bool) $optionData['is_correct'] : false,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Question added successfully.');
    }

    public function destroyQuestion(QuizQuestion $question)
    {
        $question->delete();

        return redirect()->back()->with('success', 'Question deleted successfully.');
    }

    public function reorderQuestions(Request $request, Quiz $quiz)
    {
        $request->validate([
            'questions'         => 'required|array',
            'questions.*.id'    => 'required|integer|exists:quiz_questions,id',
            'questions.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->input('questions') as $item) {
            QuizQuestion::where('id', $item['id'])->update(['order_column' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }
}
