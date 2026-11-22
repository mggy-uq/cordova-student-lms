<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentGradesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('grades.index'))
            ->assertRedirect(route('login'));
    }

    public function test_student_sees_only_their_recorded_grades(): void
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
        $assignment = Assignment::create([
            'course_id' => $course->id,
            'title' => 'Cell Structure Review',
            'due_at' => now()->subDay(),
            'max_score' => 100,
        ]);
        AssignmentSubmission::create([
            'user_id' => $student->id,
            'assignment_id' => $assignment->id,
            'submitted_at' => now()->subHours(2),
            'grade' => 88,
            'feedback' => 'Clear and accurate explanation.',
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
        $otherAssignment = Assignment::create([
            'course_id' => $otherCourse->id,
            'title' => 'Private Algebra Grade',
            'max_score' => 100,
        ]);
        AssignmentSubmission::create([
            'user_id' => $otherStudent->id,
            'assignment_id' => $otherAssignment->id,
            'submitted_at' => now(),
            'grade' => 95,
            'feedback' => 'Private feedback.',
        ]);

        $this->actingAs($student)
            ->get(route('grades.index'))
            ->assertOk()
            ->assertSee('Grades')
            ->assertSee('Introduction to Biology')
            ->assertSee('Cell Structure Review')
            ->assertSee('88.00 / 100.00')
            ->assertSee('88.0%')
            ->assertSee('Clear and accurate explanation.')
            ->assertDontSee('Advanced Mathematics')
            ->assertDontSee('Private Algebra Grade')
            ->assertDontSee('Private feedback.');
    }

    public function test_student_without_grades_sees_empty_state(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)
            ->get(route('grades.index'))
            ->assertOk()
            ->assertSee('0 graded items')
            ->assertSee('No grades recorded yet');
    }

    public function test_student_does_not_see_unenrolled_courses_with_grades(): void
    {
        $student = User::factory()->create();
        $course = Course::create([
            'code' => 'HIS-101',
            'title' => 'World History',
            'status' => 'Active',
        ]);
        $assignment = Assignment::create([
            'course_id' => $course->id,
            'title' => 'Timeline Project',
            'max_score' => 50,
        ]);
        AssignmentSubmission::create([
            'user_id' => $student->id,
            'assignment_id' => $assignment->id,
            'submitted_at' => now(),
            'grade' => 45,
        ]);

        $this->actingAs($student)
            ->get(route('grades.index'))
            ->assertOk()
            ->assertDontSee('World History')
            ->assertDontSee('Timeline Project');
    }
}
