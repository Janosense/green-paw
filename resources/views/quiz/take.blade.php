@extends('layouts.app')

@section('title', $quiz->title . ' — Quiz')
@section('page_title', $quiz->title)

@section('content')
    <div style="max-width: 720px; margin: 0 auto;">
        <form method="POST" action="{{ route('quiz.submit', $attempt) }}" id="quizForm">
            @csrf
            <input type="hidden" name="time_spent" id="timeSpent" value="0">
            <input type="hidden" name="tab_switches" id="tabSwitches" value="0">

            <!-- Timer & Info bar -->
            <div class="card" style="margin-bottom: 20px;">
                <div class="card-body"
                    style="display: flex; justify-content: space-between; align-items: center; padding: 12px 20px;">
                    <div style="display: flex; gap: 16px; font-size: 13px; color: var(--text-muted);">
                        <span>{{ $questions->count() }} questions</span>
                        <span>{{ $quiz->totalPoints() }} points</span>
                        <span>Passing: {{ $quiz->passing_score }}%</span>
                    </div>
                    @if($quiz->hasTimeLimit())
                        <div id="timer"
                            style="font-size: 18px; font-weight: 800; font-family: 'JetBrains Mono', monospace; color: var(--accent);">
                            --:--
                        </div>
                    @endif
                </div>
            </div>

            <!-- Questions -->
            @foreach($questions as $i => $question)
                <div class="card" style="margin-bottom: 16px;" id="q-{{ $question->id }}">
                    <div class="card-header">
                        <div>
                            <span style="font-weight: 700; color: var(--accent); margin-right: 8px;">Q{{ $i + 1 }}</span>
                            <span class="badge badge-muted" style="font-size: 10px;">{{ $question->typeLabel() }}</span>
                        </div>
                        <span style="font-size: 12px; color: var(--text-muted);">{{ $question->points }} pts</span>
                    </div>
                    <div class="card-body">
                        <p style="font-size: 15px; font-weight: 600; margin-bottom: 16px; line-height: 1.6;">
                            {{ $question->body }}</p>

                        @if($question->type === 'mcq')
                            @php
                                $options = $question->options ?? [];
                                if ($quiz->shuffle_answers)
                                    shuffle($options);
                            @endphp
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @foreach($options as $option)
                                    <label
                                        style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; background: var(--bg-input); border-radius: var(--radius-sm); cursor: pointer; transition: background var(--transition);"
                                        onmouseover="this.style.background='var(--bg-card-hover)'"
                                        onmouseout="this.style.background='var(--bg-input)'">
                                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}"
                                            style="accent-color: var(--accent);">
                                        <span style="font-size: 14px;">{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @elseif($question->type === 'true_false')
                            <div style="display: flex; gap: 12px;">
                                @foreach(['True', 'False'] as $tf)
                                    <label
                                        style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 14px; background: var(--bg-input); border-radius: var(--radius-sm); cursor: pointer; transition: background var(--transition);"
                                        onmouseover="this.style.background='var(--bg-card-hover)'"
                                        onmouseout="this.style.background='var(--bg-input)'">
                                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $tf }}"
                                            style="accent-color: var(--accent);">
                                        <span style="font-size: 14px; font-weight: 600;">{{ $tf }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @elseif($question->type === 'fill_blank')
                            <input type="text" name="answers[{{ $question->id }}]" class="form-input"
                                placeholder="Type your answer..." autocomplete="off">
                        @elseif($question->type === 'essay')
                            <textarea name="answers[{{ $question->id }}]" class="form-input form-textarea" rows="5"
                                placeholder="Write your essay answer..."></textarea>
                        @endif
                    </div>
                </div>
            @endforeach

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px;"
                onclick="return confirm('Submit your quiz? You cannot change answers after submitting.')">
                Submit Quiz
            </button>
        </form>
    </div>

    <script>
        // Timer
        @if($quiz->hasTimeLimit() && $remainingSeconds !== null)
            (function () {
                let remaining = {{ $remainingSeconds }};
                const timerEl = document.getElementById('timer');
                const form = document.getElementById('quizForm');

                function updateTimer() {
                    if (remaining <= 0) {
                        timerEl.textContent = '00:00';
                        timerEl.style.color = 'var(--danger)';
                        form.submit();
                        return;
                    }
                    const m = Math.floor(remaining / 60);
                    const s = remaining % 60;
                    timerEl.textContent = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
                    if (remaining <= 60) timerEl.style.color = 'var(--danger)';
                    remaining--;
                }
                updateTimer();
                setInterval(updateTimer, 1000);
            })();
        @endif

        // Time tracking
        (function () {
            const start = Date.now();
            document.getElementById('quizForm').addEventListener('submit', function () {
                document.getElementById('timeSpent').value = Math.floor((Date.now() - start) / 1000);
            });
        })();

        // Anti-cheat: tab switch detection
        (function () {
            let switches = 0;
            document.addEventListener('visibilitychange', function () {
                if (document.hidden) {
                    switches++;
                    document.getElementById('tabSwitches').value = switches;
                    if (switches === 1) {
                        alert('⚠️ Please do not switch tabs during the quiz. Tab switches are recorded.');
                    } else if (switches >= 3) {
                        alert('⚠️ Multiple tab switches detected (' + switches + '). This will be reported.');
                    }
                }
            });
        })();
    </script>
@endsection