@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-heading', 'Dashboard')

@section('content')
    <div class="dashboard-content">
        <section class="welcome-card" aria-labelledby="welcome-heading">
            <div class="welcome-card-copy">
                <p class="welcome-eyebrow">YOUR LEARNING SPACE</p>
                <h2 id="welcome-heading">Welcome back, {{ $student->name }}!</h2>
                <p>Here’s a quick look at your courses and what’s coming up.</p>
            </div>
            <div class="welcome-decoration" aria-hidden="true">
                <span class="welcome-decoration-ring"></span>
                <span class="welcome-decoration-dot"></span>
                <span class="welcome-decoration-square"></span>
            </div>
        </section>

        <section class="overview-grid" aria-label="Dashboard overview">
            <article class="overview-card">
                <span class="overview-icon icon-courses" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v15H6.5A2.5 2.5 0 0 0 4 20.5z"/><path d="M4 5.5v15M8 7h8M8 11h7"/></svg>
                </span>
                <div class="overview-copy">
                    <p>Enrolled courses</p>
                    <strong>{{ $courseCount }}</strong>
                </div>
                <span class="overview-caption">Your active learning</span>
            </article>
            <article class="overview-card">
                <span class="overview-icon icon-assignments" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M8 4h8M9 3h6v3H9z"/><path d="M6 4H4v17h16V4h-2M8 12l2 2 5-5"/></svg>
                </span>
                <div class="overview-copy">
                    <p>Pending assignments</p>
                    <strong>{{ $pendingAssignmentCount }}</strong>
                </div>
                <span class="overview-caption">Waiting for your submission</span>
            </article>
            <article class="overview-card">
                <span class="overview-icon icon-announcements" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M4 14V9a2 2 0 0 1 2-2h3l8-3v15l-8-3H6a2 2 0 0 1-2-2z"/><path d="m8 16 1.5 5h4L11 16M20 9l2-1"/></svg>
                </span>
                <div class="overview-copy">
                    <p>Recent announcements</p>
                    <strong>{{ $announcementCount }}</strong>
                </div>
                <span class="overview-caption">Shared with your courses</span>
            </article>
        </section>

        <div class="dashboard-columns">
            <section class="dashboard-panel courses-panel" aria-labelledby="courses-heading">
                <div class="panel-heading">
                    <div>
                        <p class="panel-eyebrow">YOUR LEARNING</p>
                        <h2 id="courses-heading">My Courses</h2>
                    </div>
                    <span class="panel-count">{{ $courseCount }} {{ \Illuminate\Support\Str::plural('course', $courseCount) }}</span>
                </div>

                @forelse ($courses as $course)
                    <article class="course-row">
                        <span class="course-monogram" aria-hidden="true">{{ mb_strtoupper(mb_substr($course->code, 0, 1)) }}</span>
                        <div class="course-copy">
                            <strong>{{ $course->title }}</strong>
                            <span>{{ $course->code }}@if ($course->instructor) <span aria-hidden="true">&middot;</span> {{ $course->instructor }}@endif</span>
                        </div>
                        <span class="course-assignment-count">{{ $course->assignments_count }} {{ \Illuminate\Support\Str::plural('assignment', $course->assignments_count) }}</span>
                    </article>
                @empty
                    <div class="empty-state">
                        <span class="empty-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v15H6.5A2.5 2.5 0 0 0 4 20.5z"/><path d="M4 5.5v15M8 7h8"/></svg>
                        </span>
                        <strong>No courses yet</strong>
                        <p>Your enrolled courses will appear here when they’re available.</p>
                    </div>
                @endforelse
            </section>

            <section class="dashboard-panel assignments-panel" aria-labelledby="assignments-heading">
                <div class="panel-heading">
                    <div>
                        <p class="panel-eyebrow">STAY ON TRACK</p>
                        <h2 id="assignments-heading">Upcoming Assignments</h2>
                    </div>
                    <span class="panel-count">{{ $pendingAssignmentCount }} pending</span>
                </div>

                @forelse ($upcomingAssignments as $assignment)
                    <article class="assignment-row">
                        <span class="assignment-marker" aria-hidden="true"></span>
                        <div class="assignment-copy">
                            <strong>{{ $assignment->title }}</strong>
                            <span>{{ $assignment->course->code }} <span aria-hidden="true">&middot;</span> {{ $assignment->course->title }}</span>
                        </div>
                        @if ($assignment->due_at?->isPast())
                            <span class="assignment-due is-overdue">Overdue {{ $assignment->due_at->format('M j') }}</span>
                        @else
                            <time class="assignment-due" @if ($assignment->due_at) datetime="{{ $assignment->due_at->toIso8601String() }}" @endif>
                                {{ $assignment->due_at?->format('M j') ?? 'No due date' }}
                            </time>
                        @endif
                    </article>
                @empty
                    <div class="empty-state compact-empty-state">
                        <span class="empty-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M5 12.5 9.5 17 19 7.5"/></svg>
                        </span>
                        <strong>You’re all caught up</strong>
                        <p>New upcoming assignments will show up here.</p>
                    </div>
                @endforelse
            </section>

            <section class="dashboard-panel announcements-panel" aria-labelledby="announcements-heading">
                <div class="panel-heading">
                    <div>
                        <p class="panel-eyebrow">CAMPUS UPDATES</p>
                        <h2 id="announcements-heading">Recent Announcements</h2>
                    </div>
                    <span class="panel-count">{{ $announcementCount }} recent</span>
                </div>

                @forelse ($recentAnnouncements as $announcement)
                    <article class="announcement-row">
                        <div class="announcement-topline">
                            <span class="announcement-tag">{{ $announcement->course?->code ?? 'College-wide' }}</span>
                            <time datetime="{{ $announcement->published_at->toIso8601String() }}">{{ $announcement->published_at->format('M j') }}</time>
                        </div>
                        <h3>{{ $announcement->title }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit($announcement->body, 150) }}</p>
                    </article>
                @empty
                    <div class="empty-state compact-empty-state">
                        <span class="empty-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M4 14V9a2 2 0 0 1 2-2h3l8-3v15l-8-3H6a2 2 0 0 1-2-2z"/></svg>
                        </span>
                        <strong>No announcements yet</strong>
                        <p>There aren’t any recent updates to share right now.</p>
                    </div>
                @endforelse
            </section>
        </div>
    </div>
@endsection
