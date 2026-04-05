<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // ProductFactory resolves category_id via Category::inRandomOrder()->first()
        Category::factory()->create();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_view_reviews_index(): void
    {
        Review::factory()->count(3)->create(['status' => 'pending']);

        $this->actingAs($this->admin)
            ->get(route('admin.reviews.index'))
            ->assertOk();
    }

    public function test_admin_can_approve_a_review(): void
    {
        $review = Review::factory()->create(['status' => 'pending']);

        $this->actingAs($this->admin)
            ->put(route('admin.reviews.approve', $review))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'id'     => $review->id,
            'status' => 'approved',
        ]);
    }

    public function test_admin_can_reject_a_review(): void
    {
        $review = Review::factory()->create(['status' => 'pending']);

        $this->actingAs($this->admin)
            ->put(route('admin.reviews.reject', $review))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'id'     => $review->id,
            'status' => 'rejected',
        ]);
    }
}
