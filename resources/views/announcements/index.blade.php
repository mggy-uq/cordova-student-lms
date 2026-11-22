@extends('layouts.dashboard')

@section('title', 'Announcements')
@section('page-heading', 'Announcements')

@section('content')
    <div class="dashboard-content announcements-page">
        <section class="announcements-page-heading" aria-labelledby="announcements-page-title">
            <div>
                <p class="welcome-eyebrow">CAMPUS UPDATES</p>
                <h2 id="announcements-page-title">Announcements</h2>
                <p>Stay informed with school-wide and course-related updates.</p>
            </div>
            <span class="announcements-total">{{ $announcements->count() }} {{ \Illuminate\Support\Str::plural('announcement', $announcements->count()) }}</span>
        </section>

        @if ($announcements->isEmpty())
            <section class="announcements-empty-state" aria-label="No announcements">
                <span class="announcements-empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M4 14V9a2 2 0 0 1 2-2h3l8-3v15l-8-3H6a2 2 0 0 1-2-2z"/></svg>
                </span>
                <h3>No announcements yet</h3>
                <p>Published announcements for your courses or the whole college will appear here.</p>
            </section>
        @else
            <section class="announcement-list" aria-label="Announcement list">
                @foreach ($announcements as $announcement)
                    <article class="announcement-card">
                        <div class="announcement-card-top">
                            <span class="announcement-category">{{ $announcement->category ?: ($announcement->course ? 'Course update' : 'College-wide') }}</span>
                            <time datetime="{{ $announcement->published_at->toIso8601String() }}">{{ $announcement->published_at->format('M j, Y') }}</time>
                        </div>
                        <h3>{{ $announcement->title }}</h3>
                        <p class="announcement-card-body">{{ \Illuminate\Support\Str::limit($announcement->body, 180) }}</p>
                        <div class="announcement-card-footer">
                            <span>{{ $announcement->course?->code ?? 'Cordova Public College' }}</span>
                            <a href="{{ route('announcements.show', $announcement) }}">Read announcement <span aria-hidden="true">&rarr;</span></a>
                        </div>
                    </article>
                @endforeach
            </section>
        @endif
    </div>
@endsection
