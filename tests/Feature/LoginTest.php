<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'name' => 'Soria, Sergio',
            'email' => 'admin@italfiat.com',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $this->admin();

        $response = $this->post('/login', [
            'email' => 'admin@italfiat.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs(User::where('email', 'admin@italfiat.com')->first());
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $this->admin();

        $response = $this->from('/login')->post('/login', [
            'email' => 'admin@italfiat.com',
            'password' => 'otra-clave',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_fails_with_unknown_email(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'noexiste@italfiat.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_password_is_stored_hashed(): void
    {
        $user = $this->admin();

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
        $this->assertStringStartsWith('$2y$', $user->fresh()->password);
        $this->assertNotSame('password', $user->fresh()->password);
    }

    public function test_user_can_logout(): void
    {
        $user = $this->admin();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
