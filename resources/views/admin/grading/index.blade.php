@extends('layouts.app')

@section('title', 'Grading Queue — Green Paw LMS')
@section('page_title', 'Grading Queue')

@section('content')
    <div class="card">
        @if($pendingAttempts->count())
            @foreach($pendingAttempts as $attempt)
                <div style="padding: 20px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                        <div>
                            <h3 style="font-size: 15px; font-weight: 700;">{{ $attempt->quiz->title }}</h3>
                            <div style="font-size: 13px; color: var(--text-muted);">
                                {{ $attempt->user->name }} · {{ $attempt->quiz->course->title }} · Submitted
                                {{ $attempt->submitted_at->diffForHumans() }}
                            </div>
                            <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">
                                Auto-graded: {{ $attempt->score }}/{{ $attempt->total_points }} pts ({{ $attempt->percentage }}%)
                                @if($attempt->tab_switches > 0)
                                    · <span style="color: var(--warning);">⚠️ {{ $attempt->tab_switches }} tab switches</span>
                                @endif
                            </div>
                        </div>
                        <span class="badge badge-warning">Needs Grading</span>
                    </div>

                    <!-- Show essay answers -->
                    @foreach($attempt->quiz->questions->where('type', 'essay') as $q)
                        <div style="margin-bottom: 12px; padding: 12px; background: var(--bg-input); border-radius: var(--radius-sm);">
                            <div style="font-size: 13px; font-weight: 600; margin-bottom: 6px;">{{ $q->body }} <span
                                    style="color: var(--text-muted); font-weight: 400;">({{ $q->points }} pts)</span></div>
                            <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">
                                {{ ($attempt->answers ?? [])[$q->id] ?? 'No answer' }}
                            </p>
                        </div>
                    @endforeach

                    <!-- Grade form -->
                    <form method="POST" action="{{ route('admin.grading.grade', $attempt) }}"
                        style="display: flex; gap: 10px; align-items: start;">
                        @csrf
                        @php
                            $essayMaxPoints = $attempt->quiz->questions->where('type', 'essay')->sum('points');
                        @endphp
                        <div style="flex-shrink: 0;">
                            <input type="number" name="essay_points" min="0" max="{{ $essayMaxPoints }}" value="0"
                                class="form-input" style="width: 90px;" placeholder="Points" required>
                            <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">/ {{ $essayMaxPoints }} pts
                            </div>
                        </div>
                        <input type="text" name="feedback" class="form-input" placeholder="Feedback (optional)" style="flex: 1;">
                        <button type="submit" class="btn btn-primary btn-sm" style="flex-shrink: 0;">Grade</button>
                    </form>
                </div>
            @endforeach

            {{ $pendingAttempts->links() }}
        @else
            <div class="card-body" style="text-align: center; padding: 60px; color: var(--text-muted);">
                ✅ No submissions pending grading.
            </div>
        @endif
    </div>
@endsection