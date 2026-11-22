<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_settings(): void
    {
        $this->get(route('settings.edit'))
            ->assertRedirect(route('login'));
    }

    public function test_student_can_view_and_update_their_profile(): void
    {
        $student = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'password' => 'original-password',
        ]);

        $this->actingAs($student)
            ->get(route('settings.edit'))
            ->assertOk()
            ->assertSee('Old Name')
            ->assertSee('old@example.com');

        $this->actingAs($student)
            ->patch(route('settings.profile.update'), [
                'name' => 'New Student Name',
                'email' => 'NEW.Student@Example.com',
                'id' => 999,
                'password' => 'restricted-change',
            ])
            ->assertRedirect(route('settings.edit'))
            ->assertSessionHas('profile_status');

        $student->refresh();
        $this->assertSame('New Student Name', $student->name);
        $this->assertSame('new.student@example.com', $student->email);
        $this->assertTrue(Hash::check('original-password', $student->password));
    }

    public function test_profile_update_validates_name_email_and_uniqueness(): void
    {
        $student = User::factory()->create(['email' => 'student@example.com']);
        User::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs($student)
            ->from(route('settings.edit'))
            ->patch(route('settings.profile.update'), [
                'name' => '',
                'email' => 'taken@example.com',
            ])
            ->assertRedirect(route('settings.edit'))
            ->assertSessionHasErrors(['name', 'email']);

        $this->assertSame('student@example.com', $student->refresh()->email);
    }

    public function test_student_can_change_password_with_valid_current_password(): void
    {
        $student = User::factory()->create([
            'password' => 'old-password',
        ]);

        $this->actingAs($student)
            ->put(route('settings.password.update'), [
                'current_password' => 'old-password',
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertRedirect(route('settings.edit'))
            ->assertSessionHas('password_status');

        $student->refresh();
        $this->assertTrue(Hash::check('new-secure-password', $student->password));
        $this->assertFalse(Hash::check('old-password', $student->password));
    }

    public function test_password_change_rejects_incorrect_current_password(): void
    {
        $student = User::factory()->create([
            'password' => 'old-password',
        ]);

        $this->actingAs($student)
            ->from(route('settings.edit'))
            ->put(route('settings.password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertRedirect(route('settings.edit'))
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('old-password', $student->refresh()->password));
    }

    public function test_password_change_requires_confirmation_and_minimum_length(): void
    {
        $student = User::factory()->create([
            'password' => 'old-password',
        ]);

        $this->actingAs($student)
            ->from(route('settings.edit'))
            ->put(route('settings.password.update'), [
                'current_password' => 'old-password',
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertRedirect(route('settings.edit'))
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('old-password', $student->refresh()->password));
    }
}
