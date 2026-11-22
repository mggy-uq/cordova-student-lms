<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCoursesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('courses.index'))
            ->assertRedirect(route('login'));
    }

    public function test_student_sees_only_their_enrolled_courses(): void
    {
        $student = User::factory()->create(['name' => 'Alex Student']);
        $otherStudent = User::factory()->create();
        $course = Course::create([
            'code' => 'BIO-101',
            'title' => 'Introduction to Biology',
            'description' => 'Study cells, genetics, and living organisms.',
            'instructor' => 'Prof. Santos',
            'schedule' => 'Mon and Wed, 9:00 AM',
            'status' => 'Active',
        ]);
        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
        $otherCourse = Course::create([
            'code' => 'MTH-201',
            'title' => 'Advanced Mathematics',
            'instructor' => 'Prof. Reyes',
            'status' => 'Active',
        ]);
        Enrollment::create([
            'user_id' => $otherStudent->id,
            'course_id' => $otherCourse->id,
        ]);

        $this->actingAs($student)
            ->get(route('courses.index'))
            ->assertOk()
            ->assertSee('My Courses')
            ->assertSee('1 course')
            ->assertSee('Introduction to Biology')
            ->assertSee('BIO-101')
            ->assertSee('Prof. Santos')
            ->assertSee('Mon and Wed, 9:00 AM')
            ->assertSee('Study cells, genetics, and living organisms.')
            ->assertSee('Active')
            ->assertDontSee('Advanced Mathematics')
            ->assertDontSee('MTH-201')
            ->assertDontSee('Prof. Reyes');
    }

    public function test_student_without_enrollments_sees_empty_state(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)
            ->get(route('courses.index'))
            ->assertOk()
            ->assertSee('0 courses')
            ->assertSee('No courses enrolled yet')
            ->assertSee('You are not currently enrolled in any courses.');
    }
}
