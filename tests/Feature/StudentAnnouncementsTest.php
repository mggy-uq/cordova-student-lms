<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentAnnouncementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('announcements.index'))
            ->assertRedirect(route('login'));
    }

    public function test_student_sees_school_wide_and_enrolled_course_announcements_only(): void
    {
        $student = User::factory()->create();
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

        Announcement::create([
            'course_id' => null,
            'title' => 'College Foundation Week',
            'body' => 'All students are invited to Foundation Week.',
            'author' => 'Student Affairs',
            'category' => 'College',
            'status' => 'Published',
            'published_at' => now()->subDay(),
        ]);
        Announcement::create([
            'course_id' => $course->id,
            'title' => 'Lab Schedule Update',
            'body' => 'The laboratory session has moved.',
            'author' => 'Prof. Santos',
            'category' => 'Course',
            'status' => 'Published',
            'published_at' => now()->subHours(2),
        ]);
        Announcement::create([
            'course_id' => $otherCourse->id,
            'title' => 'Private Mathematics Notice',
            'body' => 'This notice is for mathematics students only.',
            'status' => 'Published',
            'published_at' => now()->subHour(),
        ]);

        $this->actingAs($student)
            ->get(route('announcements.index'))
            ->assertOk()
            ->assertSee('College Foundation Week')
            ->assertSee('Lab Schedule Update')
            ->assertDontSee('Private Mathematics Notice');
    }

    public function test_student_cannot_view_course_announcement_without_enrollment(): void
    {
        $student = User::factory()->create();
        $course = Course::create([
            'code' => 'PHY-101',
            'title' => 'Physics',
            'status' => 'Active',
        ]);
        $announcement = Announcement::create([
            'course_id' => $course->id,
            'title' => 'Restricted Physics Notice',
            'body' => 'Physics students only.',
            'status' => 'Published',
            'published_at' => now()->subHour(),
        ]);

        $this->actingAs($student)
            ->get(route('announcements.show', $announcement))
            ->assertNotFound();
    }

    public function test_unpublished_and_draft_announcements_are_not_visible(): void
    {
        $student = User::factory()->create();
        $draft = Announcement::create([
            'course_id' => null,
            'title' => 'Draft College Notice',
            'body' => 'Still being prepared.',
            'status' => 'Draft',
            'published_at' => now()->subHour(),
        ]);
        $scheduled = Announcement::create([
            'course_id' => null,
            'title' => 'Scheduled College Notice',
            'body' => 'Not published yet.',
            'status' => 'Published',
            'published_at' => now()->addDay(),
        ]);

        $this->actingAs($student)
            ->get(route('announcements.index'))
            ->assertOk()
            ->assertDontSee('Draft College Notice')
            ->assertDontSee('Scheduled College Notice');

        $this->actingAs($student)
            ->get(route('announcements.show', $draft))
            ->assertNotFound();
        $this->actingAs($student)
            ->get(route('announcements.show', $scheduled))
            ->assertNotFound();
    }

    public function test_student_without_announcements_sees_empty_state(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)
            ->get(route('announcements.index'))
            ->assertOk()
            ->assertSee('0 announcements')
            ->assertSee('No announcements yet');
    }
}
