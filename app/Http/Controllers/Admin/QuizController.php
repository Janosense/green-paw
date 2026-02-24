<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(Course $course)
    {
        $quizzes = $course->quizzes()
            ->withCount('questions', 'attempts')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.quizzes.index', compact('course', 'quizzes'));
    }

    public function create(Course $course)
    {
        return view('admin.quizzes.form', [
            'course' => $course,
            'quiz' => null,
            'lessons' => $course->lessons()->ordered()->get(),
        ]);
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'lesson_id' => ['nullable', 'exists:lessons,id'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:1'],
            'passing_score' => ['required', 'integer', 'min:1', 'max:100'],
            'max_attempts' => ['required', 'integer', 'min:1'],
            'shuffle_questions' => ['boolean'],
            'shuffle_answers' => ['boolean'],
            'show_results_after' => ['boolean'],
            'is_published' => ['boolean'],
            'questions' => ['array'],
            'questions.*.type' => ['required', 'in:mcq,true_false,fill_blank,essay'],
            'questions.*.body' => ['required', 'string'],
            'questions.*.points' => ['required', 'integer', 'min:1'],
            'questions.*.options' => ['nullable', 'string'],
            'questions.*.correct_answer' => ['nullable', 'string'],
            'questions.*.explanation' => ['nullable', 'string'],
        ]);

        $quiz = $course->quizzes()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'lesson_id' => $validated['lesson_id'] ?? null,
            'time_limit_minutes' => $validated['time_limit_minutes'] ?? null,
            'passing_score' => $validated['passing_score'],
            'max_attempts' => $validated['max_attempts'],
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'shuffle_answers' => $request->boolean('shuffle_answers'),
            'show_results_after' => $request->boolean('show_results_after'),
            'is_published' => $request->boolean('is_published'),
        ]);

        $this->saveQuestions($quiz, $validated['questions'] ?? []);

        return redirect()->route('admin.courses.quizzes.index', $course)
            ->with('success', 'Quiz created successfully.');
    }

    public function edit(Course $course, Quiz $quiz)
    {
        $quiz->load('questions');
        return view('admin.quizzes.form', [
            'course' => $course,
            'quiz' => $quiz,
            'lessons' => $course->lessons()->ordered()->get(),
        ]);
    }

    public function update(Request $request, Course $course, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'lesson_id' => ['nullable', 'exists:lessons,id'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:1'],
            'passing_score' => ['required', 'integer', 'min:1', 'max:100'],
            'max_attempts' => ['required', 'integer', 'min:1'],
            'shuffle_questions' => ['boolean'],
            'shuffle_answers' => ['boolean'],
            'show_results_after' => ['boolean'],
            'is_published' => ['boolean'],
            'questions' => ['array'],
            'questions.*.type' => ['required', 'in:mcq,true_false,fill_blank,essay'],
            'questions.*.body' => ['required', 'string'],
            'questions.*.points' => ['required', 'integer', 'min:1'],
            'questions.*.options' => ['nullable', 'string'],
            'questions.*.correct_answer' => ['nullable', 'string'],
            'questions.*.explanation' => ['nullable', 'string'],
        ]);

        $quiz->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'lesson_id' => $validated['lesson_id'] ?? null,
            'time_limit_minutes' => $validated['time_limit_minutes'] ?? null,
            'passing_score' => $validated['passing_score'],
            'max_attempts' => $validated['max_attempts'],
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'shuffle_answers' => $request->boolean('shuffle_answers'),
            'show_results_after' => $request->boolean('show_results_after'),
            'is_published' => $request->boolean('is_published'),
        ]);

        // Replace all questions
        $quiz->questions()->delete();
        $this->saveQuestions($quiz, $validated['questions'] ?? []);

        return redirect()->route('admin.courses.quizzes.index', $course)
            ->with('success', 'Quiz updated successfully.');
    }

    public function destroy(Course $course, Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('admin.courses.quizzes.index', $course)
            ->with('success', 'Quiz deleted.');
    }

    private function saveQuestions(Quiz $quiz, array $questions): void
    {
        foreach ($questions as $i => $q) {
            $options = null;
            $correctAnswer = null;

            if (!empty($q['options'])) {
                $options = array_map('trim', explode("\n", $q['options']));
            }

            if (!empty($q['correct_answer'])) {
                $correctAnswer = array_map('trim', explode("\n", $q['correct_answer']));
            }

            // For true_false, set options automatically
            if ($q['type'] === 'true_false') {
                $options = ['True', 'False'];
            }

            $quiz->questions()->create([
                'type' => $q['type'],
                'body' => $q['body'],
                'options' => $options,
                'correct_answer' => $correctAnswer,
                'points' => $q['points'],
                'explanation' => $q['explanation'] ?? null,
                'sort_order' => $i,
            ]);
        }
    }
}
