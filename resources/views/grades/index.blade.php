@extends('layouts.dashboard')

@section('title', 'Grades')
@section('page-heading', 'Grades')

@section('content')
    <div class="dashboard-content grades-page">
        <section class="grades-page-heading" aria-labelledby="grades-page-title">
            <div>
                <p class="welcome-eyebrow">YOUR PERFORMANCE</p>
                <h2 id="grades-page-title">Grades</h2>
                <p>Review recorded assignment scores and instructor feedback.</p>
            </div>
            <span class="grades-total">{{ $gradedCount }} {{ \Illuminate\Support\Str::plural('graded item', $gradedCount) }}</span>
        </section>

        @if ($gradedCount === 0)
            <section class="grades-empty-state" aria-label="No grades recorded">
                <span class="grades-empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 3 14.8 8.7 21 9.6l-4.5 4.4 1.1 6.2L12 17.3l-5.6 2.9 1.1-6.2L3 9.6l6.2-.9z"/></svg>
                </span>
                <h3>No grades recorded yet</h3>
                <p>Recorded assignment scores and feedback will appear here when they are available.</p>
            </section>
        @else
            <section class="grades-overview" aria-label="Grade overview">
                <article class="grade-overview-card">
                    <span>Graded assignments</span>
                    <strong>{{ $gradedCount }}</strong>
                </article>
                <article class="grade-overview-card">
                    <span>Recorded average</span>
                    <strong>{{ $recordedAverage !== null ? number_format($recordedAverage, 1).'%' : 'N/A' }}</strong>
                    @if ($recordedAverage !== null)
                        <small>Based only on graded items with a maximum score</small>
                    @else
                        <small>Maximum scores are not available</small>
                    @endif
                </article>
            </section>

            <section class="grade-courses" aria-label="Grades by course">
                @foreach ($courses as $course)
                    @php
                        $gradedAssignments = $course->assignments->filter(
                            fn ($assignment) => $assignment->submissions->first()?->grade !== null
                        );
                        $coursePercentages = $gradedAssignments
                            ->filter(fn ($assignment) => $assignment->max_score !== null && (float) $assignment->max_score > 0)
                            ->map(fn ($assignment) => ((float) $assignment->submissions->first()->grade / (float) $assignment->max_score) * 100);
                    @endphp

                    @if ($gradedAssignments->isNotEmpty())
                        <article class="grade-course-card">
                            <header class="grade-course-header">
                                <div>
                                    <span>{{ $course->code }}</span>
                                    <h3>{{ $course->title }}</h3>
                                </div>
                                @if ($coursePercentages->isNotEmpty())
                                    <div class="grade-course-average">
                                        <small>Recorded average</small>
                                        <strong>{{ number_format($coursePercentages->average(), 1) }}%</strong>
                                    </div>
                                @endif
                            </header>

                            <div class="grade-assignment-list">
                                @foreach ($gradedAssignments as $assignment)
                                    @php
                                        $submission = $assignment->submissions->first();
                                        $percentage = $assignment->max_score !== null && (float) $assignment->max_score > 0
                                            ? ((float) $submission->grade / (float) $assignment->max_score) * 100
                                            : null;
                                    @endphp
                                    <article class="grade-assignment-row">
                                        <div class="grade-assignment-name">
                                            <h4>{{ $assignment->title }}</h4>
                                            @if ($submission->feedback)
                                                <p><strong>Feedback:</strong> {{ $submission->feedback }}</p>
                                            @else
                                                <p class="grade-no-feedback">No instructor feedback has been provided.</p>
                                            @endif
                                        </div>
                                        <div class="grade-assignment-score">
                                            <strong>{{ number_format((float) $submission->grade, 2) }}@if ($assignment->max_score !== null) / {{ number_format((float) $assignment->max_score, 2) }}@endif</strong>
                                            @if ($percentage !== null)
                                                <span>{{ number_format($percentage, 1) }}%</span>
                                            @else
                                                <span>Score recorded</span>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </article>
                    @endif
                @endforeach
            </section>
        @endif
    </div>
@endsection
