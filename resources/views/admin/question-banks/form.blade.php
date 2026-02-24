@extends('layouts.app')

@section('title', ($bank ? 'Edit' : 'Create') . ' Question Bank')
@section('page_title', ($bank ? 'Edit' : 'Create') . ' Question Bank')

@section('content')
    <div style="max-width: 800px;">
        <form method="POST"
            action="{{ $bank ? route('admin.question-banks.update', $bank) : route('admin.question-banks.store') }}">
            @csrf
            @if($bank) @method('PUT') @endif

            <div class="card" style="margin-bottom: 20px;">
                <div class="card-body">
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-input"
                                value="{{ old('name', $bank?->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="course_id">Course (optional)</label>
                            <select name="course_id" id="course_id" class="form-input form-select">
                                <option value="">— Global —</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id', $bank?->course_id) == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="description">Description</label>
                        <textarea name="description" id="description" class="form-input form-textarea"
                            rows="2">{{ old('description', $bank?->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <h3 class="card-title">Questions</h3>
                    <button type="button" class="btn btn-primary btn-sm" onclick="addBankItem()">+ Add Question</button>
                </div>
                <div id="itemsContainer">
                    @if($bank)
                        @foreach($bank->items as $i => $item)
                            <div class="question-block" style="padding: 16px 20px; border-bottom: 1px solid var(--border);">
                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                    <span style="font-weight: 700; font-size: 14px; color: var(--accent);">Q{{ $i + 1 }}</span>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="this.closest('.question-block').remove()"
                                        style="padding: 4px 8px; font-size: 11px;">Remove</button>
                                </div>
                                <div class="grid-2" style="margin-bottom: 8px;">
                                    <select name="items[{{ $i }}][type]" class="form-input form-select" required>
                                        <option value="mcq" {{ $item->type === 'mcq' ? 'selected' : '' }}>MCQ</option>
                                        <option value="true_false" {{ $item->type === 'true_false' ? 'selected' : '' }}>True/False
                                        </option>
                                        <option value="fill_blank" {{ $item->type === 'fill_blank' ? 'selected' : '' }}>Fill Blank
                                        </option>
                                        <option value="essay" {{ $item->type === 'essay' ? 'selected' : '' }}>Essay</option>
                                    </select>
                                    <input type="number" name="items[{{ $i }}][points]" class="form-input"
                                        value="{{ $item->points }}" min="1" required>
                                </div>
                                <textarea name="items[{{ $i }}][body]" class="form-input form-textarea" rows="2"
                                    required>{{ $item->body }}</textarea>
                                <textarea name="items[{{ $i }}][options]" class="form-input form-textarea" rows="2"
                                    placeholder="Options (one per line)"
                                    style="margin-top: 8px;">{{ is_array($item->options) ? implode("\n", $item->options) : '' }}</textarea>
                                <textarea name="items[{{ $i }}][correct_answer]" class="form-input form-textarea" rows="1"
                                    placeholder="Correct answer"
                                    style="margin-top: 8px;">{{ is_array($item->correct_answer) ? implode("\n", $item->correct_answer) : '' }}</textarea>
                                <input type="text" name="items[{{ $i }}][explanation]" class="form-input"
                                    value="{{ $item->explanation }}" placeholder="Explanation" style="margin-top: 8px;">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">{{ $bank ? 'Update' : 'Create' }} Bank</button>
                <a href="{{ route('admin.question-banks.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        let bankItemIndex = {{ $bank ? $bank->items->count() : 0 }};
        function addBankItem() {
            const i = bankItemIndex++;
            const html = `
            <div class="question-block" style="padding: 16px 20px; border-bottom: 1px solid var(--border);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="font-weight: 700; font-size: 14px; color: var(--accent);">Q${i + 1}</span>
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.question-block').remove()" style="padding: 4px 8px; font-size: 11px;">Remove</button>
                </div>
                <div class="grid-2" style="margin-bottom: 8px;">
                    <select name="items[${i}][type]" class="form-input form-select" required>
                        <option value="mcq">MCQ</option>
                        <option value="true_false">True/False</option>
                        <option value="fill_blank">Fill Blank</option>
                        <option value="essay">Essay</option>
                    </select>
                    <input type="number" name="items[${i}][points]" class="form-input" value="1" min="1" required>
                </div>
                <textarea name="items[${i}][body]" class="form-input form-textarea" rows="2" required placeholder="Question text"></textarea>
                <textarea name="items[${i}][options]" class="form-input form-textarea" rows="2" placeholder="Options (one per line)" style="margin-top: 8px;"></textarea>
                <textarea name="items[${i}][correct_answer]" class="form-input form-textarea" rows="1" placeholder="Correct answer" style="margin-top: 8px;"></textarea>
                <input type="text" name="items[${i}][explanation]" class="form-input" placeholder="Explanation" style="margin-top: 8px;">
            </div>`;
            document.getElementById('itemsContainer').insertAdjacentHTML('beforeend', html);
        }
    </script>
@endsection