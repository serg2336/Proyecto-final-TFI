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
            'role' => User::ROLE_ADMIN,
        ]);
    }

    private function usuarioConRol(string $email, string $role): User
    {
        return User::factory()->create([
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => $role,
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

    /** HU1: el vendedor entra directo a su módulo. */
    public function test_vendedor_es_redirigido_a_ventas(): void
    {
        $this->usuarioConRol('vendedor@italfiat.com', User::ROLE_VENDEDOR);

        $this->post('/login', [
            'email' => 'vendedor@italfiat.com',
            'password' => 'password',
        ])->assertRedirect('/ventas');

        $this->assertAuthenticated();
    }

    /** HU1: el encargado de depósito entra directo a stock. */
    public function test_deposito_es_redirigido_a_stock(): void
    {
        $this->usuarioConRol('deposito@italfiat.com', User::ROLE_DEPOSITO);

        $this->post('/login', [
            'email' => 'deposito@italfiat.com',
            'password' => 'password',
        ])->assertRedirect('/stock');

        $this->assertAuthenticated();
    }

    /** HU1: si el rol no se reconoce, no se rompe el login. */
    public function test_rol_desconocido_cae_al_dashboard(): void
    {
        $this->usuarioConRol('otro@italfiat.com', 'otro');

        $this->post('/login', [
            'email' => 'otro@italfiat.com',
            'password' => 'password',
        ])->assertRedirect('/');

        $this->assertAuthenticated();
    }

    /** El layout muestra el rol real del usuario autenticado. */
    public function test_dashboard_muestra_el_rol_del_usuario(): void
    {
        $this->actingAs($this->admin())
            ->get('/')
            ->assertOk()
            ->assertSee('Administrador');
    }
}
