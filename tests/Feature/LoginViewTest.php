<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginViewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Evita depender del build de Vite al renderizar la vista en los tests.
        $this->withoutVite();
    }

    private function admin(): User
    {
        return User::factory()->create([
            'name' => 'Soria, Sergio',
            'email' => 'admin@italfiat.com',
            'password' => Hash::make('password'),
        ]);
    }

    /** TASK-8: el error de credenciales se renderiza debajo del campo email. */
    public function test_login_view_renders_email_error_after_wrong_credentials(): void
    {
        $this->admin();

        $this->from('/login')->post('/login', [
            'email' => 'admin@italfiat.com',
            'password' => 'otra-clave',
        ])->assertRedirect('/login');

        $this->get('/login')
            ->assertOk()
            ->assertSee('La combinación de correo y contraseña no coincide.')
            ->assertSee('id="email-error"', false)
            ->assertSee('aria-invalid="true"', false)
            ->assertSee('aria-describedby="email-error"', false)
            ->assertSee('is-invalid', false);
    }

    /** TASK-8: los errores de validación se muestran en email y password. */
    public function test_login_view_renders_required_errors_for_both_fields(): void
    {
        $this->from('/login')->post('/login', [
            'email' => '',
            'password' => '',
        ])->assertRedirect('/login');

        $this->get('/login')
            ->assertOk()
            ->assertSee('id="email-error"', false)
            ->assertSee('id="password-error"', false)
            ->assertSee('aria-describedby="password-error"', false);
    }

    /** TASK-7: la vista conserva el email ingresado y apunta al login. */
    public function test_login_view_keeps_old_email_and_points_to_login_route(): void
    {
        $this->admin();

        $this->from('/login')->post('/login', [
            'email' => 'admin@italfiat.com',
            'password' => 'otra-clave',
        ]);

        $this->get('/login')
            ->assertOk()
            ->assertSee('value="admin@italfiat.com"', false)
            ->assertSee('action="'.route('login').'"', false)
            ->assertSee('name="_token"', false);
    }

    /** El copy del login es el aprobado en el tablero. */
    public function test_login_view_muestra_el_copy_actualizado(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Acceso al sistema')
            ->assertSee('Bienvenido')
            ->assertSee('de nuevo')
            ->assertSee('Iniciá sesión para administrar tu catálogo.')
            ->assertSee('Correo y contraseña');
    }
}
