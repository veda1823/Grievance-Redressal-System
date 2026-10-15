<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffComplaintManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_view_submitted_complaints(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $student = User::factory()->create(['role' => 'student']);
        $category = Category::create(['name' => 'Academic']);
        $complaint = Complaint::create([
            'user_id' => $student->id,
            'category_id' => $category->id,
            'title' => 'Examination schedule issue',
            'description' => 'The examination schedule contains a conflicting time slot.',
            'priority' => 'high',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($staff)->get(route('staff.dashboard'));

        $response->assertOk();
        $response->assertSee($complaint->title);
        $response->assertSee($student->name);
    }

    public function test_staff_can_view_and_update_a_complaint_status(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $student = User::factory()->create(['role' => 'student']);
        $category = Category::create(['name' => 'Academic']);
        $complaint = Complaint::create([
            'user_id' => $student->id,
            'category_id' => $category->id,
            'title' => 'Examination schedule issue',
            'description' => 'The examination schedule contains a conflicting time slot.',
            'priority' => 'high',
            'status' => 'pending',
        ]);

        $showResponse = $this->actingAs($staff)->get(route('staff.complaints.show', $complaint));
        $showResponse->assertOk();
        $showResponse->assertSee($complaint->description);

        $updateResponse = $this->actingAs($staff)->patch(route('staff.complaints.status', $complaint), [
            'status' => 'in_progress',
        ]);

        $updateResponse->assertRedirect(route('staff.complaints.show', $complaint));
        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_students_cannot_view_or_update_staff_complaints(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $owner = User::factory()->create(['role' => 'student']);
        $category = Category::create(['name' => 'Academic']);
        $complaint = Complaint::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'title' => 'Examination schedule issue',
            'description' => 'The examination schedule contains a conflicting time slot.',
            'priority' => 'high',
            'status' => 'pending',
        ]);

        $this->actingAs($student)->get(route('staff.complaints.show', $complaint))->assertForbidden();
        $this->actingAs($student)->patch(route('staff.complaints.status', $complaint), ['status' => 'resolved'])->assertForbidden();
    }
}