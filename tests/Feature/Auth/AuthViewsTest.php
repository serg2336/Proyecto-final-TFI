<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthViewsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_login_muestra_el_enlace_de_recuperacion(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('¿Olvidaste tu contraseña?')
            ->assertSee('href="'.route('password.request').'"', false);
    }

    public function test_register_muestra_el_formulario_en_espanol(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Creá tu')
            ->assertSee('Nombre y apellido')
            ->assertSee('Confirmá la contraseña')
            ->assertSee('Crear cuenta')
            ->assertSee('action="'.route('register').'"', false);
    }

    public function test_register_muestra_errores_por_campo_en_espanol(): void
    {
        $this->from('/register')->post('/register', [
            'name' => '',
            'email' => 'no-es-email',
            'password' => '123',
            'password_confirmation' => '456',
        ])->assertRedirect('/register');

        $this->get('/register')
            ->assertOk()
            ->assertSee('id="name-error"', false)
            ->assertSee('id="email-error"', false)
            ->assertSee('id="password-error"', false)
            ->assertSee('aria-invalid="true"', false)
            ->assertSee('El campo nombre es obligatorio.');
    }

    public function test_forgot_password_muestra_el_formulario(): void
    {
        $this->get('/forgot-password')
            ->assertOk()
            ->assertSee('Recuperar acceso')
            ->assertSee('Enviar enlace')
            ->assertSee('action="'.route('password.email').'"', false);
    }

    public function test_reset_password_muestra_el_formulario_con_el_correo(): void
    {
        $url = route('password.reset', [
            'token' => 'token-demo',
            'email' => 'admin@italfiat.com',
        ]);

        $this->get($url)
            ->assertOk()
            ->assertSee('Elegí una')
            ->assertSee('Guardar contraseña')
            ->assertSee('value="admin@italfiat.com"', false)
            ->assertSee('action="'.route('password.store').'"', false);
    }

    public function test_confirm_password_muestra_el_formulario(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/confirm-password')
            ->assertOk()
            ->assertSee('Confirmá tu')
            ->assertSee('Confirmar')
            ->assertSee('action="'.route('password.confirm').'"', false);
    }

    public function test_verify_email_muestra_el_aviso_y_el_reenvio(): void
    {
        $this->actingAs(User::factory()->unverified()->create())
            ->get('/verify-email')
            ->assertOk()
            ->assertSee('Verificá tu')
            ->assertSee('Reenviar correo')
            ->assertSee('action="'.route('verification.send').'"', false)
            ->assertSee('action="'.route('logout').'"', false);
    }
}
