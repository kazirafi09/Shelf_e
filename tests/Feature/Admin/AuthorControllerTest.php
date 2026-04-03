<?php

namespace Tests\Feature\Admin;

use App\Models\Author;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthorControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->regularUser = User::factory()->create();
    }

    public function test_admin_can_view_authors_index(): void
    {
        Author::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.authors.index'))
            ->assertOk()
            ->assertSee('Authors');
    }

    public function test_admin_can_store_an_author(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
            ->post(route('admin.authors.store'), [
                'name' => 'Gabriel García Márquez',
                'bio'  => 'Colombian novelist and Nobel Prize laureate.',
            ])
            ->assertRedirect(route('admin.authors.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('authors', [
            'name' => 'Gabriel García Márquez',
            'bio'  => 'Colombian novelist and Nobel Prize laureate.',
        ]);
    }

    public function test_admin_can_update_an_author(): void
    {
        Storage::fake('public');

        $author = Author::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->admin)
            ->put(route('admin.authors.update', $author), [
                'name' => 'New Name',
                'bio'  => 'Updated bio.',
            ])
            ->assertRedirect(route('admin.authors.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('authors', [
            'id'   => $author->id,
            'name' => 'New Name',
            'bio'  => 'Updated bio.',
        ]);
    }

    public function test_admin_can_delete_an_author(): void
    {
        Storage::fake('public');

        $author = Author::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.authors.destroy', $author))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('authors', ['id' => $author->id]);
    }

    public function test_non_admin_cannot_access_admin_author_routes(): void
    {
        // Guest is redirected to login
        $this->get(route('admin.authors.index'))
            ->assertRedirect(route('login'));

        // Regular authenticated user is redirected away (not 200)
        $this->actingAs($this->regularUser)
            ->get(route('admin.authors.index'))
            ->assertRedirect('/');
    }
}
