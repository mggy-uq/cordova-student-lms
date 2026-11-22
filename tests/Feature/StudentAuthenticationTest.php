<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_deployment_health_endpoint_is_available(): void
    {
        $this->get('/health')
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);
    }

    public function test_guest_can_view_login_and_registration_forms(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Welcome back');

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Create your account');
    }

    public function test_registration_creates_a_hashed_password_and_authenticates_the_student(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Alex Student',
            'email' => 'Alex.Student@example.com',
            'password' => 'correct-horse-battery',
            'password_confirmation' => 'correct-horse-battery',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Alex Student',
            'email' => 'alex.student@example.com',
        ]);

        $user = User::where('email', 'alex.student@example.com')->firstOrFail();
        $this->assertNotSame('correct-horse-battery', $user->password);
        $this->assertTrue(Hash::check('correct-horse-battery', $user->password));
    }

    public function test_registration_requires_valid_unique_email_and_matching_password_confirmation(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Alex Student',
                'email' => 'existing@example.com',
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertDatabaseCount('users', 1);
        $this->assertGuest();
    }

    public function test_student_can_log_in_and_log_out(): void
    {
        $user = User::factory()->create([
            'email' => 'student@example.com',
            'password' => 'student-password',
        ]);

        $this->post(route('login.store'), [
            'email' => 'student@example.com',
            'password' => 'student-password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Welcome back, '.$user->name)
            ->assertSee('No courses yet')
            ->assertSee('You’re all caught up')
            ->assertSee('No announcements yet');

        $this->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_dashboard_shows_only_data_for_the_authenticated_students_courses(): void
    {
        $student = User::factory()->create();
        $otherStudent = User::factory()->create();
        $course = Course::create([
            'code' => 'BIO-101',
            'title' => 'Introduction to Biology',
            'instructor' => 'Prof. Santos',
        ]);
        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
        $otherCourse = Course::create([
            'code' => 'MTH-201',
            'title' => 'Advanced Mathematics',
        ]);
        Enrollment::create([
            'user_id' => $otherStudent->id,
            'course_id' => $otherCourse->id,
        ]);
        Assignment::create([
            'course_id' => $course->id,
            'title' => 'Cell Structure Review',
            'due_at' => now()->addDays(3),
        ]);
        Assignment::create([
            'course_id' => $course->id,
            'title' => 'Overdue Biology Quiz',
            'due_at' => now()->subDays(1),
        ]);
        $submittedAssignment = Assignment::create([
            'course_id' => $course->id,
            'title' => 'Completed Biology Worksheet',
            'due_at' => now()->addDays(2),
        ]);
        AssignmentSubmission::create([
            'user_id' => $student->id,
            'assignment_id' => $submittedAssignment->id,
            'submitted_at' => now(),
        ]);
        Assignment::create([
            'course_id' => $otherCourse->id,
            'title' => 'Algebra Problem Set',
            'due_at' => now()->addDays(2),
        ]);
        Announcement::create([
            'course_id' => $course->id,
            'title' => 'Lab schedule posted',
            'body' => 'Check the course portal for your lab schedule.',
            'published_at' => now()->subHour(),
        ]);
        Announcement::create([
            'course_id' => $otherCourse->id,
            'title' => 'Math class update',
            'body' => 'A schedule update for another course.',
            'published_at' => now()->subHour(),
        ]);
        Announcement::create([
            'course_id' => $course->id,
            'title' => 'Draft Biology Notice',
            'body' => 'This draft must not appear.',
            'published_at' => now()->subHour(),
            'status' => 'Draft',
        ]);

        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Introduction to Biology')
            ->assertSee('Cell Structure Review')
            ->assertSee('Overdue Biology Quiz')
            ->assertSee('Overdue')
            ->assertSee('Lab schedule posted')
            ->assertSee('2 pending')
            ->assertDontSee('Completed Biology Worksheet')
            ->assertDontSee('Advanced Mathematics')
            ->assertDontSee('Algebra Problem Set')
            ->assertDontSee('Math class update')
            ->assertDontSee('Draft Biology Notice');
    }

    public function test_invalid_credentials_and_guest_dashboard_access_are_rejected(): void
    {
        User::factory()->create([
            'email' => 'student@example.com',
            'password' => 'student-password',
        ]);

        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->post(route('login.store'), [
            'email' => 'student@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
