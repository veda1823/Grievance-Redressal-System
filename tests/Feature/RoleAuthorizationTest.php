<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_staff_member_can_access_the_staff_area(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get(route('staff.dashboard'));

        $response->assertOk();
        $response->assertSee('Complaint queue');
    }

    public function test_a_student_cannot_access_the_staff_area(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get(route('staff.dashboard'));

        $response->assertForbidden();
    }

    public function test_staff_cannot_access_the_student_dashboard(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get(route('dashboard'));

        $response->assertForbidden();
    }
}