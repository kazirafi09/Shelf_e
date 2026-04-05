<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Tests for the registration and login lifecycle.
 *
 * NOTE: The basic "screen renders" and "happy-path redirect" cases are already
 * covered by tests\Feature\Auth\RegistrationTest and AuthenticationTest (Breeze
 * scaffolding). This file focuses on edge-cases, security constraints, and
 * route-access protection that those files do not exercise.
 */
class UserAuthTest extends TestCase
{
    use RefreshDatabase;

    // ── Registration ─────────────────────────────────────────────────────────

    /** @test */
    public function registration_persists_user_to_the_database(): void
    {
        $this->post('/register', [
            'name'                  => 'Shelf Reader',
            'email'                 => 'reader@shelf-e.com',
            'password'              => 'SecurePass1!',
            'password_confirmation' => 'SecurePass1!',
        ]);

        $this->assertDatabaseHas('users', [
            'name'  => 'Shelf Reader',
            'email' => 'reader@shelf-e.com',
        ]);
    }

    /** @test */
    public function registration_fires_the_registered_event(): void
    {
        Event::fake([Registered::class]);

        $this->post('/register', [
            'name'                  => 'Event Reader',
            'email'                 => 'events@shelf-e.com',
            'password'              => 'SecurePass1!',
            'password_confirmation' => 'SecurePass1!',
        ]);

        Event::assertDispatched(Registered::class);
    }

    /** @test */
    public function newly_registered_user_is_not_an_admin(): void
    {
        // Registering via the public form must never grant admin privileges,
        // regardless of what the request contains.  This guards against the
        // mass-assignment vector where 'role' is not in $fillable on User.
        $this->post('/register', [
            'name'                  => 'Sneaky Admin',
            'email'                 => 'sneaky@shelf-e.com',
            'password'              => 'SecurePass1!',
            'password_confirmation' => 'SecurePass1!',
        ]);

        $user = User::where('email', 'sneaky@shelf-e.com')->firstOrFail();

        $this->assertFalse(
            $user->isAdmin(),
            'A newly registered user must never receive the admin role.'
        );
    }

    /** @test */
    public function registration_fails_with_a_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@shelf-e.com']);

        $response = $this->post('/register', [
            'name'                  => 'Second Person',
            'email'                 => 'taken@shelf-e.com',
            'password'              => 'SecurePass1!',
            'password_confirmation' => 'SecurePass1!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
    }

    /** @test */
    public function registration_fails_when_passwords_do_not_match(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Typo User',
            'email'                 => 'typo@shelf-e.com',
            'password'              => 'SecurePass1!',
            'password_confirmation' => 'DifferentPass1!',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'typo@shelf-e.com']);
    }

    // ── Login ────────────────────────────────────────────────────────────────

    /** @test */
    public function user_is_logged_in_and_redirected_to_home_after_login(): void
    {
        // Non-admin users are sent to route('home') — see AuthenticatedSessionController::store().
        // Admins are redirected to the admin dashboard via a separate branch.
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password', // UserFactory default
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('home', absolute: false));
    }

    /** @test */
    public function login_with_wrong_password_keeps_user_as_guest(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'not-the-right-password',
        ]);

        $this->assertGuest();
    }

    /** @test */
    public function login_with_wrong_password_returns_an_email_validation_error(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong',
        ]);

        // Breeze attaches the error to the 'email' key
        $response->assertSessionHasErrors('email');
    }

    // ── Route protection ─────────────────────────────────────────────────────

    /** @test */
    public function unauthenticated_user_is_redirected_away_from_the_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    /** @test */
    public function unauthenticated_user_cannot_access_profile_page(): void
    {
        $this->get(route('profile.edit'))->assertRedirect(route('login'));
    }

    /** @test */
    public function unauthenticated_user_cannot_access_wallet_page(): void
    {
        $this->get(route('wallet.index'))->assertRedirect(route('login'));
    }

    /** @test */
    public function non_admin_user_is_blocked_from_admin_dashboard(): void
    {
        $user = User::factory()->create(); // regular user, role != 'admin'

        $this->actingAs($user)
             ->get(route('admin.dashboard'))
             ->assertRedirect('/');
    }

    /** @test */
    public function admin_user_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
             ->get(route('admin.dashboard'))
             ->assertOk();
    }
}
