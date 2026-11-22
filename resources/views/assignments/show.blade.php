@extends('layouts.dashboard')

@section('title', $assignment->title)
@section('page-heading', 'Assignment Details')

@section('content')
    <div class="dashboard-content assignment-detail-page">
        <a class="assignment-back-link" href="{{ route('assignments.index') }}">&larr; Back to Assignments</a>

        @if (session('status'))
            <div class="assignment-alert" role="status">{{ session('status') }}</div>
        @endif

        <section class="assignment-detail-card" aria-labelledby="assignment-title">
            <div class="assignment-detail-heading">
                <div>
                    <span class="assignment-course-code">{{ $assignment->course->code }}</span>
                    <h2 id="assignment-title">{{ $assignment->title }}</h2>
                    <p>{{ $assignment->course->title }}</p>
                </div>
                @php
                    $status = $submission ? 'Submitted' : ($assignment->due_at && $assignment->due_at->isPast() ? 'Overdue' : 'Pending');
                @endphp
                <span class="assignment-status status-{{ mb_strtolower($status) }}">{{ $status }}</span>
            </div>

            <div class="assignment-detail-meta">
                <div>
                    <small>Due date</small>
                    <strong>{{ $assignment->due_at?->format('M j, Y') ?? 'No deadline set' }}</strong>
                </div>
                @if ($submission)
                    <div>
                        <small>Date submitted</small>
                        <strong>{{ $submission->submitted_at->format('M j, Y') }}</strong>
                    </div>
                @endif
                @if ($submission?->grade !== null)
                    <div>
                        <small>Grade</small>
                        <strong>{{ number_format((float) $submission->grade, 2) }}</strong>
                    </div>
                @endif
            </div>

            <div class="assignment-description-block">
                <h3>Instructions</h3>
                <p>{{ $assignment->description ?: 'No assignment description is available yet.' }}</p>
            </div>

            @if ($submission?->feedback)
                <div class="assignment-feedback">
                    <h3>Instructor feedback</h3>
                    <p>{{ $submission->feedback }}</p>
                </div>
            @endif

            @if ($submission)
                <div class="assignment-submission-summary">
                    <div>
                        <small>Submitted file</small>
                        @if ($submission->original_name)
                            <strong>{{ $submission->original_name }}</strong>
                        @else
                            <strong>Submission recorded</strong>
                        @endif
                    </div>
                    @if ($submission->file_path)
                        <a href="{{ route('submissions.download', $submission) }}">Download</a>
                    @endif
                </div>
            @endif

            <form class="assignment-submit-form" method="POST" action="{{ route('assignments.submit', $assignment) }}" enctype="multipart/form-data">
                @csrf
                <h3>{{ $submission ? 'Replace your submission' : 'Submit assignment' }}</h3>
                <label for="submission">Choose a file</label>
                <input id="submission" name="submission" type="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.zip,.xls,.xlsx,.jpg,.jpeg,.png">
                @error('submission')
                    <p class="assignment-field-error">{{ $message }}</p>
                @enderror
                <p class="assignment-upload-help">PDF, Word, PowerPoint, text, ZIP, Excel, or image files up to 10 MB.</p>
                <button type="submit">{{ $submission ? 'Replace submission' : 'Submit assignment' }}</button>
            </form>
        </section>
    </div>
@endsection
