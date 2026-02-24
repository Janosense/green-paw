@extends('layouts.app')

@section('title', 'Quiz Results — Green Paw LMS')
@section('page_title', $attempt->quiz->title . ' — Results')

@section('content')
    <div style="max-width: 720px; margin: 0 auto;">
        <!-- Score card -->
        <div class="card"
            style="margin-bottom: 24px; {{ $attempt->isPassed() ? 'border-color: rgba(52, 211, 153, 0.3);' : 'border-color: rgba(239, 68, 68, 0.3);' }}">
            <div class="card-body" style="text-align: center; padding: 32px;">
                <div style="font-size: 48px; margin-bottom: 8px;">{{ $attempt->isPassed() ? '🎉' : '😔' }}</div>
                <div
                    style="font-size: 36px; font-weight: 800; color: {{ $attempt->isPassed() ? 'var(--accent)' : 'var(--danger)' }}; font-family: 'JetBrains Mono', monospace;">
                    {{ $attempt->percentage }}%
                </div>
                <div style="font-size: 14px; color: var(--text-muted); margin-top: 4px;">
                    {{ $attempt->score }} / {{ $attempt->total_points }} points
                    · {{ $attempt->isPassed() ? 'Passed' : 'Not passed' }} ({{ $attempt->quiz->passing_score }}% required)
                </div>

                @if($attempt->status === 'submitted')
                    <div style="margin-top: 12px;">
                        <span class="badge badge-warning">⏳ Essay answers pending instructor review</span>
                    </div>
                @endif

                <div
                    style="display: flex; gap: 16px; justify-content: center; margin-top: 16px; font-size: 13px; color: var(--text-muted);">
                    <span>⏱ {{ floor($attempt->time_spent_seconds / 60) }}m {{ $attempt->time_spent_seconds % 60 }}s</span>
                    @if($attempt->tab_switches > 0)
                        <span style="color: var(--warning);">⚠️ {{ $attempt->tab_switches }} tab switches</span>
                    @endif
                </div>

                @if($attempt->instructor_feedback)
                    <div
                        style="margin-top: 16px; padding: 12px; background: var(--bg-input); border-radius: var(--radius-sm); text-align: left;">
                        <div style="font-size: 12px; font-weight: 600; color: var(--accent); margin-bottom: 4px;">Instructor
                            Feedback</div>
                        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">
                            {{ $attempt->instructor_feedback }}</p>
                    </div>
                @endif
            </div>
        </div>

        @if($attempt->quiz->show_results_after)
            <!-- Answer review -->
            @foreach($attempt->quiz->questions as $i => $question)
                @php
                    $userAnswer = ($attempt->answers ?? [])[$question->id] ?? null;
                    $isCorrect = $question->isAutoGradable() ? $question->grade($userAnswer) > 0 : null;
                @endphp
                <div class="card"
                    style="margin-bottom: 12px; {{ $isCorrect === true ? 'border-color: rgba(52, 211, 153, 0.2);' : ($isCorrect === false ? 'border-color: rgba(239, 68, 68, 0.2);' : '') }}">
                    <div class="card-header">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            @if($isCorrect === true)
                                <span style="color: var(--accent);">✓</span>
                            @elseif($isCorrect === false)
                                <span style="color: var(--danger);">✗</span>
                            @else
                                <span style="color: var(--warning);">📝</span>
                            @endif
                            <span style="font-weight: 700;">Q{{ $i + 1 }}</span>
                            <span class="badge badge-muted" style="font-size: 10px;">{{ $question->typeLabel() }}</span>
                        </div>
                        <span style="font-size: 12px; color: var(--text-muted);">
                            {{ $question->isAutoGradable() ? ($question->grade($userAnswer) . '/' . $question->points) : 'Pending' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <p style="font-weight: 600; margin-bottom: 10px;">{{ $question->body }}</p>

                        @if($question->type === 'mcq' || $question->type === 'true_false')
                            @php $correctVal = is_array($question->correct_answer) ? ($question->correct_answer[0] ?? '') : $question->correct_answer; @endphp
                            @foreach($question->options ?? [] as $opt)
                                <div
                                    style="padding: 6px 10px; margin-bottom: 4px; border-radius: var(--radius-sm); font-size: 14px;
                                            {{ (string) $opt === (string) $correctVal ? 'background: rgba(52, 211, 153, 0.1); color: var(--accent);' : '' }}
                                            {{ (string) $opt === (string) $userAnswer && (string) $opt !== (string) $correctVal ? 'background: rgba(239, 68, 68, 0.1); color: var(--danger);' : '' }}">
                                    {{ $opt }}
                                    @if((string) $opt === (string) $correctVal) ✓ @endif
                                    @if((string) $opt === (string) $userAnswer && (string) $opt !== (string) $correctVal) (your answer) @endif
                                </div>
                            @endforeach
                        @elseif($question->type === 'fill_blank')
                            <div style="font-size: 14px; margin-bottom: 4px;">
                                <span style="color: var(--text-muted);">Your answer:</span>
                                <span
                                    style="font-weight: 600; color: {{ $isCorrect ? 'var(--accent)' : 'var(--danger)' }};">{{ $userAnswer ?: '—' }}</span>
                            </div>
                            @if(!$isCorrect)
                                <div style="font-size: 13px; color: var(--accent);">
                                    Correct:
                                    {{ is_array($question->correct_answer) ? implode(' / ', $question->correct_answer) : $question->correct_answer }}
                                </div>
                            @endif
                        @elseif($question->type === 'essay')
                            <div
                                style="padding: 10px; background: var(--bg-input); border-radius: var(--radius-sm); font-size: 14px; color: var(--text-secondary); line-height: 1.6;">
                                {{ $userAnswer ?: 'No answer provided.' }}
                            </div>
                        @endif

                        @if($question->explanation)
                            <div
                                style="margin-top: 10px; padding: 8px 12px; background: rgba(52, 211, 153, 0.05); border-radius: var(--radius-sm); font-size: 13px; color: var(--text-secondary);">
                                💡 {{ $question->explanation }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif

        <a href="{{ route('learn.my-courses') }}" class="btn btn-secondary" style="width: 100%;">← Back to My Courses</a>
    </div>
@endsection