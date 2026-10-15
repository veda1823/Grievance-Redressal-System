<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_student_can_view_the_complaint_form(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        Category::create(['name' => 'Academic']);

        $response = $this->actingAs($user)->get(route('complaints.create'));

        $response->assertOk();
        $response->assertSee('Submit a complaint');
        $response->assertSee('Academic');
    }

    public function test_a_student_can_submit_a_complaint(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $category = Category::create(['name' => 'Academic']);

        $response = $this->actingAs($user)->post(route('complaints.store'), [
            'category_id' => $category->id,
            'title' => 'Issue with examination schedule',
            'description' => 'The published examination schedule contains a conflicting time slot.',
            'priority' => 'high',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('complaints', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Issue with examination schedule',
            'priority' => 'high',
            'status' => 'pending',
        ]);
    }

    public function test_invalid_complaint_data_is_rejected(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($user)->post(route('complaints.store'), [
            'category_id' => 999,
            'title' => '',
            'description' => 'Too short',
            'priority' => 'urgent',
        ]);

        $response->assertSessionHasErrors(['category_id', 'title', 'description', 'priority']);
        $this->assertSame(0, Complaint::count());
    }
}