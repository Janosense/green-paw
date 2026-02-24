@extends('layouts.app')

@section('title', ($quiz ? 'Edit' : 'Create') . ' Quiz — Green Paw LMS')
@section('page_title', ($quiz ? 'Edit' : 'Create') . ' Quiz')

@section('content')
    <div style="max-width: 800px;">
        <a href="{{ route('admin.courses.quizzes.index', $course) }}" class="btn btn-secondary btn-sm"
            style="margin-bottom: 16px;">← Back to Quizzes</a>

        <form method="POST"
            action="{{ $quiz ? route('admin.courses.quizzes.update', [$course, $quiz]) : route('admin.courses.quizzes.store', $course) }}"
            id="quizForm">
            @csrf
            @if($quiz) @method('PUT') @endif

            <!-- Quiz settings -->
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <h3 class="card-title">Quiz Settings</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="title">Title</label>
                        <input type="text" name="title" id="title" class="form-input"
                            value="{{ old('title', $quiz?->title) }}" required>
                        @error('title') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="description">Description</label>
                        <textarea name="description" id="description" class="form-input form-textarea"
                            rows="2">{{ old('description', $quiz?->description) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="lesson_id">Attach to Lesson (optional)</label>
                        <select name="lesson_id" id="lesson_id" class="form-input form-select">
                            <option value="">— Standalone Quiz —</option>
                            @foreach($lessons as $lesson)
                                <option value="{{ $lesson->id }}" {{ old('lesson_id', $quiz?->lesson_id) == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="time_limit_minutes">Time Limit (minutes)</label>
                            <input type="number" name="time_limit_minutes" id="time_limit_minutes" class="form-input"
                                value="{{ old('time_limit_minutes', $quiz?->time_limit_minutes) }}" min="1"
                                placeholder="Leave empty for no limit">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="passing_score">Passing Score (%)</label>
                            <input type="number" name="passing_score" id="passing_score" class="form-input"
                                value="{{ old('passing_score', $quiz?->passing_score ?? 70) }}" min="1" max="100" required>
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="max_attempts">Max Attempts</label>
                            <input type="number" name="max_attempts" id="max_attempts" class="form-input"
                                value="{{ old('max_attempts', $quiz?->max_attempts ?? 3) }}" min="1" required>
                        </div>
                        <div class="form-group"
                            style="display: flex; flex-direction: column; justify-content: flex-end; gap: 8px; padding-bottom: 4px;">
                            <div class="checkbox-item">
                                <input type="checkbox" name="shuffle_questions" value="1" id="shuffle_questions" {{ old('shuffle_questions', $quiz?->shuffle_questions) ? 'checked' : '' }}>
                                <label for="shuffle_questions">Shuffle questions</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="shuffle_answers" value="1" id="shuffle_answers" {{ old('shuffle_answers', $quiz?->shuffle_answers) ? 'checked' : '' }}>
                                <label for="shuffle_answers">Shuffle answers</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="show_results_after" value="1" id="show_results_after" {{ old('show_results_after', $quiz?->show_results_after ?? true) ? 'checked' : '' }}>
                                <label for="show_results_after">Show results after</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" name="is_published" value="1" id="is_published" {{ old('is_published', $quiz?->is_published) ? 'checked' : '' }}>
                                <label for="is_published">Published</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions -->
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <h3 class="card-title">Questions</h3>
                    <button type="button" class="btn btn-primary btn-sm" onclick="addQuestion()">+ Add Question</button>
                </div>
                <div id="questionsContainer">
                    @if($quiz)
                        @foreach($quiz->questions as $i => $q)
                            <div class="question-block" style="padding: 16px 20px; border-bottom: 1px solid var(--border);">
                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                    <span style="font-weight: 700; font-size: 14px; color: var(--accent);">Q{{ $i + 1 }}</span>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="this.closest('.question-block').remove()"
                                        style="padding: 4px 8px; font-size: 11px;">Remove</button>
                                </div>
                                <div class="grid-2" style="margin-bottom: 8px;">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <select name="questions[{{ $i }}][type]" class="form-input form-select"
                                            onchange="toggleQuestionFields(this)" required>
                                            <option value="mcq" {{ $q->type === 'mcq' ? 'selected' : '' }}>Multiple Choice</option>
                                            <option value="true_false" {{ $q->type === 'true_false' ? 'selected' : '' }}>True / False
                                            </option>
                                            <option value="fill_blank" {{ $q->type === 'fill_blank' ? 'selected' : '' }}>Fill in the
                                                Blank</option>
                                            <option value="essay" {{ $q->type === 'essay' ? 'selected' : '' }}>Essay</option>
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <input type="number" name="questions[{{ $i }}][points]" class="form-input"
                                            value="{{ $q->points }}" min="1" placeholder="Points" required>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-bottom: 8px;">
                                    <textarea name="questions[{{ $i }}][body]" class="form-input form-textarea" rows="2"
                                        placeholder="Question text..." required>{{ $q->body }}</textarea>
                                </div>
                                <div class="question-options"
                                    style="{{ in_array($q->type, ['mcq']) ? '' : 'display:none;' }} margin-bottom: 8px;">
                                    <textarea name="questions[{{ $i }}][options]" class="form-input form-textarea" rows="3"
                                        placeholder="One option per line">{{ is_array($q->options) ? implode("\n", $q->options) : '' }}</textarea>
                                </div>
                                <div class="question-answer"
                                    style="{{ $q->type !== 'essay' ? '' : 'display:none;' }} margin-bottom: 8px;">
                                    <textarea name="questions[{{ $i }}][correct_answer]" class="form-input form-textarea" rows="1"
                                        placeholder="Correct answer (one per line for multiple accepted answers)">{{ is_array($q->correct_answer) ? implode("\n", $q->correct_answer) : '' }}</textarea>
                                </div>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <input type="text" name="questions[{{ $i }}][explanation]" class="form-input"
                                        value="{{ $q->explanation }}" placeholder="Explanation (shown after submission)">
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div id="noQuestions"
                    style="padding: 32px; text-align: center; color: var(--text-muted); {{ ($quiz && $quiz->questions->count()) ? 'display:none;' : '' }}">
                    No questions added yet. Click "Add Question" to start.
                </div>
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">{{ $quiz ? 'Update' : 'Create' }} Quiz</button>
                <a href="{{ route('admin.courses.quizzes.index', $course) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        let questionIndex = {{ $quiz ? $quiz->questions->count() : 0 }};

        function addQuestion() {
            const i = questionIndex++;
            const html = `
            <div class="question-block" style="padding: 16px 20px; border-bottom: 1px solid var(--border);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="font-weight: 700; font-size: 14px; color: var(--accent);">Q${i + 1}</span>
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.question-block').remove()" style="padding: 4px 8px; font-size: 11px;">Remove</button>
                </div>
                <div class="grid-2" style="margin-bottom: 8px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <select name="questions[${i}][type]" class="form-input form-select" onchange="toggleQuestionFields(this)" required>
                            <option value="mcq">Multiple Choice</option>
                            <option value="true_false">True / False</option>
                            <option value="fill_blank">Fill in the Blank</option>
                            <option value="essay">Essay</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <input type="number" name="questions[${i}][points]" class="form-input" value="1" min="1" placeholder="Points" required>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom: 8px;">
                    <textarea name="questions[${i}][body]" class="form-input form-textarea" rows="2" placeholder="Question text..." required></textarea>
                </div>
                <div class="question-options" style="margin-bottom: 8px;">
                    <textarea name="questions[${i}][options]" class="form-input form-textarea" rows="3" placeholder="One option per line"></textarea>
                </div>
                <div class="question-answer" style="margin-bottom: 8px;">
                    <textarea name="questions[${i}][correct_answer]" class="form-input form-textarea" rows="1" placeholder="Correct answer"></textarea>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <input type="text" name="questions[${i}][explanation]" class="form-input" placeholder="Explanation (shown after submission)">
                </div>
            </div>`;

            document.getElementById('questionsContainer').insertAdjacentHTML('beforeend', html);
            document.getElementById('noQuestions').style.display = 'none';
        }

        function toggleQuestionFields(select) {
            const block = select.closest('.question-block');
            const optionsDiv = block.querySelector('.question-options');
            const answerDiv = block.querySelector('.question-answer');
            const type = select.value;

            optionsDiv.style.display = type === 'mcq' ? '' : 'none';
            answerDiv.style.display = type === 'essay' ? 'none' : '';
        }
    </script>
@endsection