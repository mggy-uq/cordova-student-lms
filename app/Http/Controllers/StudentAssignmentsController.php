<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentAssignmentsController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user();

        $assignments = Assignment::query()
            ->whereHas('course.enrollments', fn ($query) => $query->where('user_id', $student->getKey()))
            ->with(['course:id,title,code', 'submissions' => fn ($query) => $query->where('user_id', $student->getKey())])
            ->orderByRaw('CASE WHEN due_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_at')
            ->get();

        return view('assignments.index', [
            'student' => $student,
            'assignments' => $assignments,
        ]);
    }

    public function show(Request $request, Assignment $assignment): View
    {
        $student = $request->user();
        $this->ensureStudentIsEnrolled($student, $assignment);

        $assignment->load(['course:id,title,code', 'submissions' => fn ($query) => $query->where('user_id', $student->getKey())]);
        $submission = $assignment->submissions->first();

        return view('assignments.show', [
            'student' => $student,
            'assignment' => $assignment,
            'submission' => $submission,
        ]);
    }

    public function submit(Request $request, Assignment $assignment): RedirectResponse
    {
        $student = $request->user();
        $this->ensureStudentIsEnrolled($student, $assignment);

        $validated = $request->validate([
            'submission' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,ppt,pptx,txt,zip,xls,xlsx,jpg,jpeg,png',
                'extensions:pdf,doc,docx,ppt,pptx,txt,zip,xls,xlsx,jpg,jpeg,png',
                'max:10240',
            ],
        ]);

        $existing = AssignmentSubmission::query()
            ->where('user_id', $student->getKey())
            ->where('assignment_id', $assignment->getKey())
            ->first();

        $file = $validated['submission'];
        $path = $file->store('assignment-submissions');

        AssignmentSubmission::updateOrCreate(
            [
                'user_id' => $student->getKey(),
                'assignment_id' => $assignment->getKey(),
            ],
            [
                'submitted_at' => now(),
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
            ],
        );

        if ($existing?->file_path && $existing->file_path !== $path) {
            Storage::disk()->delete($existing->file_path);
        }

        return redirect()
            ->route('assignments.show', $assignment)
            ->with('status', 'Assignment submitted successfully.');
    }

    public function download(Request $request, AssignmentSubmission $submission): StreamedResponse
    {
        if ($submission->user_id !== $request->user()->getKey()) {
            abort(404);
        }

        if (! $submission->file_path || ! Storage::disk()->exists($submission->file_path)) {
            abort(404);
        }

        return Storage::disk()->download(
            $submission->file_path,
            $submission->original_name ?: 'submission',
        );
    }

    private function ensureStudentIsEnrolled($student, Assignment $assignment): void
    {
        $isEnrolled = $student->courses()
            ->whereKey($assignment->course_id)
            ->exists();

        if (! $isEnrolled) {
            abort(404);
        }
    }
}
