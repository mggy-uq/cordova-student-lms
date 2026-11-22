@extends('layouts.dashboard')

@section('title', 'Assignments')
@section('page-heading', 'Assignments')

@section('content')
    <div class="dashboard-content assignments-page">
        <section class="assignments-page-heading" aria-labelledby="assignments-page-title">
            <div>
                <p class="welcome-eyebrow">STAY ON TRACK</p>
                <h2 id="assignments-page-title">Assignments</h2>
                <p>Review due dates, submission status, grades, and feedback.</p>
            </div>
            <span class="assignment-total">{{ $assignments->count() }} {{ \Illuminate\Support\Str::plural('assignment', $assignments->count()) }}</span>
        </section>

        @if ($assignments->isEmpty())
            <section class="assignments-empty-state" aria-label="No assignments">
                <span class="assignments-empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M8 4h8M9 3h6v3H9z"/><path d="M6 4H4v17h16V4h-2M8 12l2 2 5-5"/></svg>
                </span>
                <h3>No assignments yet</h3>
                <p>Assignments for your enrolled courses will appear here.</p>
            </section>
        @else
            <section class="assignment-list" aria-label="Assignment list">
                @foreach ($assignments as $assignment)
                    @php
                        $submission = $assignment->submissions->first();
                        $status = $submission ? 'Submitted' : ($assignment->due_at && $assignment->due_at->isPast() ? 'Overdue' : 'Pending');
                    @endphp
                    <article class="assignment-card">
                        <div class="assignment-card-status">
                            <span class="assignment-status status-{{ mb_strtolower($status) }}">{{ $status }}</span>
                            @if ($submission?->grade !== null)
                                <span class="assignment-grade">{{ number_format((float) $submission->grade, 2) }} grade</span>
                            @endif
                        </div>

                        <div class="assignment-card-main">
                            <div>
                                <span class="assignment-course-code">{{ $assignment->course->code }}</span>
                                <h3>{{ $assignment->title }}</h3>
                            </div>
                            <time class="assignment-card-due" @if ($assignment->due_at) datetime="{{ $assignment->due_at->toIso8601String() }}" @endif>
                                <small>{{ $assignment->due_at ? 'Due date' : 'Due date not set' }}</small>
                                <strong>{{ $assignment->due_at?->format('M j, Y') ?? 'No deadline' }}</strong>
                            </time>
                        </div>

                        <p class="assignment-card-description">{{ $assignment->description ?: 'No assignment description is available yet.' }}</p>

                        @if ($submission)
                            <p class="assignment-submitted-date">Submitted {{ $submission->submitted_at->format('M j, Y') }}</p>
                        @endif

                        <a class="assignment-open-link" href="{{ route('assignments.show', $assignment) }}">
                            View details
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </article>
                @endforeach
            </section>
        @endif
    </div>
@endsection
