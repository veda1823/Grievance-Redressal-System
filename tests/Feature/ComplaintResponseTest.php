<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_add_a_response_and_students_can_read_it(): void
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
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($staff)->post(route('staff.complaints.responses.store', $complaint), [
            'comment' => 'The department is reviewing the schedule and will follow up shortly.',
        ]);

        $response->assertRedirect(route('staff.complaints.show', $complaint));
        $this->assertDatabaseHas('complaint_responses', [
            'complaint_id' => $complaint->id,
            'staff_id' => $staff->id,
            'comment' => 'The department is reviewing the schedule and will follow up shortly.',
        ]);

        $studentResponse = $this->actingAs($student)->get(route('complaints.show', $complaint));
        $studentResponse->assertOk();
        $studentResponse->assertSee('The department is reviewing the schedule and will follow up shortly.');
    }

    public function test_students_cannot_view_another_students_complaint(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $owner = User::factory()->create(['role' => 'student']);
        $category = Category::create(['name' => 'Academic']);
        $complaint = Complaint::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'title' => 'Private complaint',
            'description' => 'This complaint belongs to another student.',
            'priority' => 'medium',
            'status' => 'pending',
        ]);

        $this->actingAs($student)->get(route('complaints.show', $complaint))->assertForbidden();
    }

    public function test_students_cannot_add_staff_responses(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $category = Category::create(['name' => 'Academic']);
        $complaint = Complaint::create([
            'user_id' => $student->id,
            'category_id' => $category->id,
            'title' => 'My complaint',
            'description' => 'This is a complaint with enough detail.',
            'priority' => 'medium',
            'status' => 'pending',
        ]);

        $this->actingAs($student)
            ->post(route('staff.complaints.responses.store', $complaint), ['comment' => 'Not allowed'])
            ->assertForbidden();

        $this->assertSame(0, ComplaintResponse::count());
    }
}