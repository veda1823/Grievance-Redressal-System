<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_login_page_is_available(): void
    {
        $response = $this->get(route('staff.login'));

        $response->assertOk();
        $response->assertSee('Staff Login');
    }

    public function test_staff_can_log_in_from_the_staff_login_page(): void
    {
        $staff = User::factory()->create([
            'email' => 'staff@example.com',
            'password' => 'secret-password',
            'role' => 'staff',
        ]);

        $response = $this->post(route('staff.login'), [
            'email' => $staff->email,
            'password' => 'secret-password',
        ]);

        $response->assertRedirect(route('staff.dashboard'));
        $this->assertAuthenticatedAs($staff);
    }

    public function test_students_cannot_log_in_through_the_staff_login_page(): void
    {
        $student = User::factory()->create([
            'email' => 'student@example.com',
            'password' => 'secret-password',
            'role' => 'student',
        ]);

        $response = $this->from(route('staff.login'))->post(route('staff.login'), [
            'email' => $student->email,
            'password' => 'secret-password',
        ]);

        $response->assertRedirect(route('staff.login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
