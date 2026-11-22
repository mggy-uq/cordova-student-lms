@extends('layouts.dashboard')

@section('title', 'My Courses')
@section('page-heading', 'My Courses')

@section('content')
    <div class="dashboard-content courses-page">
        <section class="courses-page-heading" aria-labelledby="courses-page-title">
            <div>
                <p class="welcome-eyebrow">YOUR LEARNING</p>
                <h2 id="courses-page-title">My Courses</h2>
                <p>View the courses you are currently enrolled in at Cordova Public College.</p>
            </div>
            <span class="course-total">{{ $courses->count() }} {{ \Illuminate\Support\Str::plural('course', $courses->count()) }}</span>
        </section>

        @if ($courses->isEmpty())
            <section class="courses-empty-state" aria-label="No enrolled courses">
                <span class="courses-empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v15H6.5A2.5 2.5 0 0 0 4 20.5z"/><path d="M4 5.5v15M8 7h8M8 11h7"/></svg>
                </span>
                <h3>No courses enrolled yet</h3>
                <p>You are not currently enrolled in any courses. Your courses will appear here once enrollment is available.</p>
            </section>
        @else
            <section class="course-card-grid" aria-label="Enrolled courses">
                @foreach ($courses as $course)
                    <article class="course-card">
                        <div class="course-card-top">
                            <span class="course-card-code">{{ $course->code }}</span>
                            @if ($course->status)
                                <span class="course-status {{ mb_strtolower(str_replace(' ', '-', $course->status)) }}">{{ $course->status }}</span>
                            @endif
                        </div>

                        <h3>{{ $course->title }}</h3>

                        @if ($course->instructor)
                            <div class="course-detail">
                                <span class="course-detail-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"/><path d="M5.5 20a6.5 6.5 0 0 1 13 0"/></svg>
                                </span>
                                <div>
                                    <small>Instructor</small>
                                    <strong>{{ $course->instructor }}</strong>
                                </div>
                            </div>
                        @endif

                        @if ($course->schedule)
                            <div class="course-detail">
                                <span class="course-detail-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>
                                </span>
                                <div>
                                    <small>Schedule</small>
                                    <strong>{{ $course->schedule }}</strong>
                                </div>
                            </div>
                        @endif

                        @if ($course->description)
                            <p class="course-description">{{ $course->description }}</p>
                        @else
                            <p class="course-description course-description-muted">No course description is available yet.</p>
                        @endif

                        <div class="course-card-footer">
                            <span>{{ $course->assignments_count }} {{ \Illuminate\Support\Str::plural('assignment', $course->assignments_count) }}</span>
                            <span class="course-enrolled">Enrolled</span>
                        </div>
                    </article>
                @endforeach
            </section>
        @endif
    </div>
@endsection
