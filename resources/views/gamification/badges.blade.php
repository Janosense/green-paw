@extends('layouts.app')

@section('title', 'Badges — Green Paw LMS')
@section('page_title', 'Badges & Achievements')

@section('content')
    <div style="margin-bottom: 24px;">
        <p style="color: var(--text-secondary);">
            You've earned <strong style="color: var(--accent);">{{ count(array_filter($earnedBadgeIds)) }}</strong> of {{ $allBadges->count() }} badges.
        </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px;">
        @foreach($allBadges as $badge)
        @php $isEarned = in_array($badge->id, $earnedBadgeIds); @endphp
        <div class="card" style="{{ !$isEarned ? 'opacity: 0.5; filter: grayscale(0.6);' : 'border-color: rgba(52, 211, 153, 0.3);' }} transition: all var(--transition);">
            <div class="card-body" style="text-align: center; padding: 24px;">
                <div style="font-size: 48px; margin-bottom: 12px;">{{ $badge->icon }}</div>
                <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 6px;">{{ $badge->name }}</h3>
                <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 12px;">{{ $badge->description }}</p>

                @if($isEarned)
                    <span class="badge badge-accent">✓ Earned</span>
                @else
                    <div style="font-size: 12px; color: var(--text-muted);">
                        @switch($badge->criteria_type)
                            @case('lessons_completed')
                                Complete {{ $badge->criteria_value }} lessons
                                @break
                            @case('courses_completed')
                                Complete {{ $badge->criteria_value }} courses
                                @break
                            @case('points_earned')
                                Earn {{ number_format($badge->criteria_value) }} points
                                @break
                            @case('streak_days')
                                {{ $badge->criteria_value }}-day learning streak
                                @break
                        @endswitch
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
@endsection
