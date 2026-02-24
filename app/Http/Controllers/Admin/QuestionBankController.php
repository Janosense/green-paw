<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\QuestionBank;
use App\Models\QuestionBankItem;
use Illuminate\Http\Request;

class QuestionBankController extends Controller
{
    public function index()
    {
        $banks = QuestionBank::with('course')->withCount('items')->orderBy('name')->get();
        return view('admin.question-banks.index', compact('banks'));
    }

    public function create()
    {
        $courses = Course::orderBy('title')->get();
        return view('admin.question-banks.form', ['bank' => null, 'courses' => $courses]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'items' => ['array'],
            'items.*.type' => ['required', 'in:mcq,true_false,fill_blank,essay'],
            'items.*.body' => ['required', 'string'],
            'items.*.points' => ['required', 'integer', 'min:1'],
            'items.*.options' => ['nullable', 'string'],
            'items.*.correct_answer' => ['nullable', 'string'],
            'items.*.explanation' => ['nullable', 'string'],
        ]);

        $bank = QuestionBank::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'course_id' => $validated['course_id'] ?? null,
        ]);

        $this->saveItems($bank, $validated['items'] ?? []);

        return redirect()->route('admin.question-banks.index')
            ->with('success', 'Question bank created.');
    }

    public function edit(QuestionBank $questionBank)
    {
        $questionBank->load('items');
        $courses = Course::orderBy('title')->get();
        return view('admin.question-banks.form', ['bank' => $questionBank, 'courses' => $courses]);
    }

    public function update(Request $request, QuestionBank $questionBank)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'items' => ['array'],
            'items.*.type' => ['required', 'in:mcq,true_false,fill_blank,essay'],
            'items.*.body' => ['required', 'string'],
            'items.*.points' => ['required', 'integer', 'min:1'],
            'items.*.options' => ['nullable', 'string'],
            'items.*.correct_answer' => ['nullable', 'string'],
            'items.*.explanation' => ['nullable', 'string'],
        ]);

        $questionBank->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'course_id' => $validated['course_id'] ?? null,
        ]);

        $questionBank->items()->delete();
        $this->saveItems($questionBank, $validated['items'] ?? []);

        return redirect()->route('admin.question-banks.index')
            ->with('success', 'Question bank updated.');
    }

    public function destroy(QuestionBank $questionBank)
    {
        $questionBank->delete();
        return redirect()->route('admin.question-banks.index')
            ->with('success', 'Question bank deleted.');
    }

    private function saveItems(QuestionBank $bank, array $items): void
    {
        foreach ($items as $item) {
            $options = null;
            $correctAnswer = null;

            if (!empty($item['options'])) {
                $options = array_map('trim', explode("\n", $item['options']));
            }
            if (!empty($item['correct_answer'])) {
                $correctAnswer = array_map('trim', explode("\n", $item['correct_answer']));
            }
            if ($item['type'] === 'true_false') {
                $options = ['True', 'False'];
            }

            $bank->items()->create([
                'type' => $item['type'],
                'body' => $item['body'],
                'options' => $options,
                'correct_answer' => $correctAnswer,
                'points' => $item['points'],
                'explanation' => $item['explanation'] ?? null,
            ]);
        }
    }
}
