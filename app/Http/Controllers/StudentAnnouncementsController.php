<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentAnnouncementsController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user();

        $announcements = Announcement::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('status', 'Published')
            ->where(function (Builder $query) use ($student) {
                $query
                    ->whereNull('course_id')
                    ->orWhereHas('course.enrollments', fn (Builder $enrollmentQuery) => $enrollmentQuery->where('user_id', $student->getKey()));
            })
            ->with('course:id,title,code')
            ->latest('published_at')
            ->get();

        return view('announcements.index', [
            'student' => $student,
            'announcements' => $announcements,
        ]);
    }

    public function show(Request $request, Announcement $announcement): View
    {
        $student = $request->user();

        if (! $announcement->published_at || $announcement->published_at->isFuture()) {
            abort(404);
        }
        if ($announcement->status !== 'Published') {
            abort(404);
        }

        if ($announcement->course_id) {
            $isEnrolled = $student->courses()
                ->whereKey($announcement->course_id)
                ->exists();

            if (! $isEnrolled) {
                abort(404);
            }
        }

        $announcement->load('course:id,title,code');

        return view('announcements.show', [
            'student' => $student,
            'announcement' => $announcement,
        ]);
    }
}
