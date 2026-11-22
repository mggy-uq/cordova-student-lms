@extends('layouts.dashboard')

@section('title', $announcement->title)
@section('page-heading', 'Announcement Details')

@section('content')
    <div class="dashboard-content announcement-detail-page">
        <a class="announcement-back-link" href="{{ route('announcements.index') }}">&larr; Back to Announcements</a>

        <article class="announcement-detail-card" aria-labelledby="announcement-title">
            <div class="announcement-detail-meta">
                <span>{{ $announcement->category ?: ($announcement->course ? 'Course update' : 'College-wide') }}</span>
                <time datetime="{{ $announcement->published_at->toIso8601String() }}">{{ $announcement->published_at->format('F j, Y') }}</time>
            </div>
            <h2 id="announcement-title">{{ $announcement->title }}</h2>
            <p class="announcement-detail-context">
                {{ $announcement->course?->code ? $announcement->course->code.' · '.$announcement->course->title : 'Cordova Public College' }}
            </p>
            @if ($announcement->author)
                <p class="announcement-detail-author">Posted by {{ $announcement->author }}</p>
            @endif
            <div class="announcement-detail-body">{{ $announcement->body }}</div>
        </article>
    </div>
@endsection
