<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentGradesController extends Controller
{
    public function __invoke(Request $request): View
    {
        $student = $request->user();

        $courses = $student->courses()
            ->with(['assignments' => function ($query) use ($student) {
                $query
                    ->with(['submissions' => function ($submissionQuery) use ($student) {
                        $submissionQuery
                            ->where('user_id', $student->getKey())
                            ->latest('submitted_at');
                    }])
                    ->orderBy('due_at');
            }])
            ->orderBy('title')
            ->get();

        $gradedSubmissions = $courses
            ->flatMap(fn ($course) => $course->assignments
                ->flatMap(fn ($assignment) => $assignment->submissions
                    ->filter(fn ($submission) => $submission->grade !== null)));

        $percentageValues = $courses
            ->flatMap(fn ($course) => $course->assignments)
            ->flatMap(function ($assignment) {
                $submission = $assignment->submissions->first();

                if (! $submission || $submission->grade === null || $assignment->max_score === null || (float) $assignment->max_score <= 0) {
                    return [];
                }

                return [((float) $submission->grade / (float) $assignment->max_score) * 100];
            });

        return view('grades.index', [
            'student' => $student,
            'courses' => $courses,
            'gradedCount' => $gradedSubmissions->count(),
            'recordedAverage' => $percentageValues->isNotEmpty()
                ? round($percentageValues->average(), 1)
                : null,
        ]);
    }
}
