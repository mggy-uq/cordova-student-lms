<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentAssignmentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('assignments.index'))
            ->assertRedirect(route('login'));
    }

    public function test_student_sees_only_assignments_from_enrolled_courses(): void
    {
        $student = User::factory()->create();
        $otherStudent = User::factory()->create();
        $course = Course::create([
            'code' => 'BIO-101',
            'title' => 'Introduction to Biology',
            'status' => 'Active',
        ]);
        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
        $otherCourse = Course::create([
            'code' => 'MTH-201',
            'title' => 'Advanced Mathematics',
            'status' => 'Active',
        ]);
        Enrollment::create([
            'user_id' => $otherStudent->id,
            'course_id' => $otherCourse->id,
        ]);
        Assignment::create([
            'course_id' => $course->id,
            'title' => 'Cell Structure Review',
            'description' => 'Review the main parts of a cell.',
            'due_at' => now()->addDays(2),
        ]);
        Assignment::create([
            'course_id' => $otherCourse->id,
            'title' => 'Algebra Problem Set',
            'due_at' => now()->addDays(3),
        ]);

        $this->actingAs($student)
            ->get(route('assignments.index'))
            ->assertOk()
            ->assertSee('Cell Structure Review')
            ->assertSee('BIO-101')
            ->assertSee('Pending')
            ->assertDontSee('Algebra Problem Set')
            ->assertDontSee('MTH-201');
    }

    public function test_student_without_assignments_sees_empty_state(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)
            ->get(route('assignments.index'))
            ->assertOk()
            ->assertSee('0 assignments')
            ->assertSee('No assignments yet');
    }

    public function test_student_can_submit_and_download_their_own_assignment_file(): void
    {
        Storage::fake('local');
        $student = User::factory()->create();
        $course = Course::create([
            'code' => 'ENG-101',
            'title' => 'English Composition',
            'status' => 'Active',
        ]);
        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
        $assignment = Assignment::create([
            'course_id' => $course->id,
            'title' => 'Essay Draft',
            'due_at' => now()->addDay(),
        ]);

        $this->actingAs($student)
            ->post(route('assignments.submit', $assignment), [
                'submission' => UploadedFile::fake()->create('essay.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('assignments.show', $assignment))
            ->assertSessionHas('status');

        $submission = AssignmentSubmission::where('user_id', $student->id)
            ->where('assignment_id', $assignment->id)
            ->firstOrFail();

        Storage::disk('local')->assertExists($submission->file_path);
        $this->assertSame('essay.pdf', $submission->original_name);

        $this->actingAs($student)
            ->get(route('submissions.download', $submission))
            ->assertOk()
            ->assertDownload('essay.pdf');

        $firstPath = $submission->file_path;

        $this->actingAs($student)
            ->post(route('assignments.submit', $assignment), [
                'submission' => UploadedFile::fake()->create('revised-essay.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('assignments.show', $assignment));

        $submission->refresh();
        Storage::disk('local')->assertMissing($firstPath);
        Storage::disk('local')->assertExists($submission->file_path);
        $this->assertSame('revised-essay.pdf', $submission->original_name);
    }

    public function test_student_cannot_view_another_students_assignment_submission(): void
    {
        $student = User::factory()->create();
        $otherStudent = User::factory()->create();
        $course = Course::create([
            'code' => 'HIS-101',
            'title' => 'World History',
            'status' => 'Active',
        ]);
        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
        Enrollment::create([
            'user_id' => $otherStudent->id,
            'course_id' => $course->id,
        ]);
        $assignment = Assignment::create([
            'course_id' => $course->id,
            'title' => 'Timeline Project',
            'due_at' => now()->addDay(),
        ]);
        $submission = AssignmentSubmission::create([
            'user_id' => $otherStudent->id,
            'assignment_id' => $assignment->id,
            'submitted_at' => now(),
            'file_path' => 'assignment-submissions/private.pdf',
            'original_name' => 'private.pdf',
        ]);

        $this->actingAs($student)
            ->get(route('submissions.download', $submission))
            ->assertNotFound();
    }

    public function test_student_cannot_view_assignment_from_a_course_they_do_not_enroll_in(): void
    {
        $student = User::factory()->create();
        $course = Course::create([
            'code' => 'SCI-101',
            'title' => 'General Science',
            'status' => 'Active',
        ]);
        $assignment = Assignment::create([
            'course_id' => $course->id,
            'title' => 'Lab Report',
            'due_at' => now()->addDay(),
        ]);

        $this->actingAs($student)
            ->get(route('assignments.show', $assignment))
            ->assertNotFound();
    }
}
