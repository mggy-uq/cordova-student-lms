<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Assignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $student = $request->user();

        $courseCount = $student->courses()->count();
        $courses = $student->courses()
            ->withCount('assignments')
            ->orderBy('title')
            ->limit(4)
            ->get();

        $pendingAssignmentsQuery = Assignment::query()
            ->whereHas('course.enrollments', fn (Builder $query) => $query->where('user_id', $student->getKey()))
            ->whereDoesntHave('submissions', fn (Builder $query) => $query->where('user_id', $student->getKey()));

        $pendingAssignmentCount = (clone $pendingAssignmentsQuery)->count();
        $upcomingAssignments = (clone $pendingAssignmentsQuery)
            ->with('course:id,title,code')
            ->orderByRaw('CASE WHEN due_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_at')
            ->limit(4)
            ->get();

        $recentAnnouncementsQuery = Announcement::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('status', 'Published')
            ->where(fn (Builder $query) => $query
                ->whereNull('course_id')
                ->orWhereHas('course.enrollments', fn (Builder $query) => $query->where('user_id', $student->getKey())));

        $announcementCount = (clone $recentAnnouncementsQuery)->count();
        $recentAnnouncements = (clone $recentAnnouncementsQuery)
            ->with('course:id,title')
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('dashboard.index', [
            'student' => $student,
            'courseCount' => $courseCount,
            'courses' => $courses,
            'pendingAssignmentCount' => $pendingAssignmentCount,
            'upcomingAssignments' => $upcomingAssignments,
            'announcementCount' => $announcementCount,
            'recentAnnouncements' => $recentAnnouncements,
        ]);
    }
}
