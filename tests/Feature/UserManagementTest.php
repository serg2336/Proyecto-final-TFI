<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function admin(): User
    {
        return User::factory()->create([
            'name' => 'Soria, Sergio',
            'email' => 'admin@italfiat.com',
            'role' => User::ROLE_ADMIN,
        ]);
    }

    private function usuario(string $role, array $atributos = []): User
    {
        return User::factory()->create(array_merge(['role' => $role], $atributos));
    }

    public function test_invitado_es_redirigido_al_login(): void
    {
        $this->get('/usuarios')->assertRedirect('/login');
    }

    public function test_solo_el_administrador_puede_ver_la_gestion_de_usuarios(): void
    {
        $this->actingAs($this->usuario(User::ROLE_VENDEDOR))
            ->get('/usuarios')
            ->assertForbidden();

        $this->actingAs($this->usuario(User::ROLE_DEPOSITO))
            ->get('/usuarios')
            ->assertForbidden();

        $this->actingAs($this->admin())
            ->get('/usuarios')
            ->assertOk();
    }

    public function test_el_administrador_ve_la_lista_con_los_roles(): void
    {
        $admin = $this->admin();
        $this->usuario(User::ROLE_VENDEDOR, ['name' => 'Vendedor Demo', 'email' => 'vendedor@italfiat.com']);
        $this->usuario(User::ROLE_DEPOSITO, ['name' => 'Depósito Demo', 'email' => 'deposito@italfiat.com']);

        $this->actingAs($admin)
            ->get('/usuarios')
            ->assertOk()
            ->assertSee('Vendedor Demo')
            ->assertSee('deposito@italfiat.com')
            ->assertSee('Administrador')
            ->assertSee('Vendedor')
            ->assertSee('Depósito');
    }

    public function test_el_administrador_puede_crear_un_usuario_con_rol(): void
    {
        $this->actingAs($this->admin())
            ->post('/usuarios', [
                'name' => 'Lía Frontend',
                'email' => 'lia@italfiat.com',
                'role' => User::ROLE_VENDEDOR,
                'password' => 'secreto123',
                'password_confirmation' => 'secreto123',
            ])
            ->assertRedirect(route('usuarios.index'))
            ->assertSessionHas('success');

        $usuario = User::where('email', 'lia@italfiat.com')->first();

        $this->assertNotNull($usuario);
        $this->assertSame(User::ROLE_VENDEDOR, $usuario->role);
        $this->assertSame('Lía Frontend', $usuario->name);
        $this->assertTrue(Hash::check('secreto123', $usuario->password));
    }

    public function test_crear_usuario_valida_correo_duplicado_y_rol_invalido(): void
    {
        $this->usuario(User::ROLE_VENDEDOR, ['email' => 'repetido@italfiat.com']);

        $this->actingAs($this->admin())
            ->from('/usuarios')
            ->post('/usuarios', [
                'name' => 'Otro Usuario',
                'email' => 'repetido@italfiat.com',
                'role' => 'superadmin',
                'password' => 'corta',
                'password_confirmation' => 'distinta',
            ])
            ->assertRedirect('/usuarios')
            ->assertSessionHasErrors(['email', 'role', 'password']);

        $this->assertSame(1, User::where('email', 'repetido@italfiat.com')->count());
    }

    public function test_la_contrasena_es_obligatoria_al_crear(): void
    {
        $this->actingAs($this->admin())
            ->from('/usuarios')
            ->post('/usuarios', [
                'name' => 'Sin Clave',
                'email' => 'sinclave@italfiat.com',
                'role' => User::ROLE_VENDEDOR,
            ])
            ->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'sinclave@italfiat.com']);
    }

    public function test_el_administrador_puede_editar_usuario_y_cambiar_su_rol(): void
    {
        $usuario = $this->usuario(User::ROLE_VENDEDOR, [
            'name' => 'Nombre Viejo',
            'email' => 'cambio@italfiat.com',
        ]);

        $this->actingAs($this->admin())
            ->put("/usuarios/{$usuario->id}", [
                'name' => 'Nombre Nuevo',
                'email' => 'cambio@italfiat.com',
                'role' => User::ROLE_DEPOSITO,
            ])
            ->assertRedirect(route('usuarios.index'))
            ->assertSessionHas('success');

        $usuario->refresh();

        $this->assertSame('Nombre Nuevo', $usuario->name);
        $this->assertSame(User::ROLE_DEPOSITO, $usuario->role);
    }

    public function test_editar_sin_contrasena_conserva_la_actual(): void
    {
        $usuario = $this->usuario(User::ROLE_VENDEDOR, [
            'email' => 'mantener@italfiat.com',
            'password' => Hash::make('clave-original'),
        ]);

        $this->actingAs($this->admin())
            ->put("/usuarios/{$usuario->id}", [
                'name' => $usuario->name,
                'email' => $usuario->email,
                'role' => User::ROLE_VENDEDOR,
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('usuarios.index'));

        $this->assertTrue(Hash::check('clave-original', $usuario->fresh()->password));
    }

    public function test_editar_con_contrasena_la_actualiza(): void
    {
        $usuario = $this->usuario(User::ROLE_VENDEDOR, [
            'email' => 'nueva@italfiat.com',
            'password' => Hash::make('clave-original'),
        ]);

        $this->actingAs($this->admin())
            ->put("/usuarios/{$usuario->id}", [
                'name' => $usuario->name,
                'email' => $usuario->email,
                'role' => User::ROLE_VENDEDOR,
                'password' => 'clave-nueva',
                'password_confirmation' => 'clave-nueva',
            ])
            ->assertRedirect(route('usuarios.index'));

        $this->assertTrue(Hash::check('clave-nueva', $usuario->fresh()->password));
    }

    public function test_editar_muestra_el_modal_con_el_usuario_si_falla_la_validacion(): void
    {
        $usuario = $this->usuario(User::ROLE_VENDEDOR, ['email' => 'modal@italfiat.com']);

        $this->actingAs($this->admin())
            ->from('/usuarios')
            ->put("/usuarios/{$usuario->id}", [
                'name' => '',
                'email' => 'no-es-un-correo',
                'role' => User::ROLE_VENDEDOR,
            ])
            ->assertRedirect('/usuarios')
            ->assertSessionHasErrors(['name', 'email'])
            ->assertSessionHas('usuario_editando', $usuario->id);
    }

    public function test_no_se_puede_degradar_al_ultimo_administrador(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->from('/usuarios')
            ->put("/usuarios/{$admin->id}", [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => User::ROLE_VENDEDOR,
            ])
            ->assertRedirect('/usuarios')
            ->assertSessionHasErrors('role');

        $this->assertSame(User::ROLE_ADMIN, $admin->fresh()->role);
    }

    public function test_no_se_puede_quedar_sin_administradores_por_baja(): void
    {
        $admin = $this->admin();

        // Otro administrador ejecuta la baja para evitar la regla de autoeliminación.
        $otroAdmin = $this->usuario(User::ROLE_ADMIN, ['email' => 'otro-admin@italfiat.com']);

        $this->actingAs($otroAdmin)
            ->delete("/usuarios/{$admin->id}")
            ->assertRedirect(route('usuarios.index'));

        $this->assertDatabaseMissing('users', ['id' => $admin->id]);

        // Ahora queda un solo administrador: no puede eliminarse ni degradarse.
        $this->actingAs($otroAdmin)
            ->from('/usuarios')
            ->delete("/usuarios/{$otroAdmin->id}")
            ->assertRedirect('/usuarios')
            ->assertSessionHasErrors('general');

        $this->assertDatabaseHas('users', ['id' => $otroAdmin->id]);
    }

    public function test_no_se_puede_eliminar_el_propio_usuario(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->from('/usuarios')
            ->delete("/usuarios/{$admin->id}")
            ->assertRedirect('/usuarios')
            ->assertSessionHasErrors('general');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_el_administrador_puede_eliminar_otro_usuario(): void
    {
        $admin = $this->admin();
        $usuario = $this->usuario(User::ROLE_VENDEDOR, ['email' => 'borrar@italfiat.com']);

        $this->actingAs($admin)
            ->delete("/usuarios/{$usuario->id}")
            ->assertRedirect(route('usuarios.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $usuario->id]);
    }

    public function test_buscar_y_filtrar_usuarios(): void
    {
        $admin = $this->admin();
        $this->usuario(User::ROLE_VENDEDOR, ['name' => 'Ana Vendedora', 'email' => 'ana@italfiat.com']);
        $this->usuario(User::ROLE_DEPOSITO, ['name' => 'Bruno Depósito', 'email' => 'bruno@italfiat.com']);

        $this->actingAs($admin)
            ->get('/usuarios?buscar=Ana')
            ->assertOk()
            ->assertSee('Ana Vendedora')
            ->assertDontSee('Bruno Depósito');

        $this->actingAs($admin)
            ->get('/usuarios?rol='.User::ROLE_DEPOSITO)
            ->assertOk()
            ->assertSee('Bruno Depósito')
            ->assertDontSee('Ana Vendedora');
    }

    public function test_vendedor_y_deposito_no_pueden_gestionar_usuarios(): void
    {
        $usuario = $this->usuario(User::ROLE_VENDEDOR, ['email' => 'objetivo@italfiat.com']);

        $this->actingAs($this->usuario(User::ROLE_VENDEDOR))
            ->post('/usuarios', [
                'name' => 'Intruso',
                'email' => 'intruso@italfiat.com',
                'role' => User::ROLE_ADMIN,
                'password' => 'secreto123',
                'password_confirmation' => 'secreto123',
            ])
            ->assertForbidden();

        $this->actingAs($this->usuario(User::ROLE_DEPOSITO))
            ->delete("/usuarios/{$usuario->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $usuario->id]);
        $this->assertDatabaseMissing('users', ['email' => 'intruso@italfiat.com']);
    }

    public function test_cada_rol_accede_solo_a_sus_modulos(): void
    {
        $vendedor = $this->usuario(User::ROLE_VENDEDOR);
        $deposito = $this->usuario(User::ROLE_DEPOSITO);
        $admin = $this->admin();

        // Vendedor: dashboard, productos, stock, ventas y clientes.
        foreach (['/', '/productos', '/stock', '/ventas', '/clientes'] as $ruta) {
            $this->actingAs($vendedor)->get($ruta)->assertOk();
        }

        foreach (['/pedidos', '/reportes', '/usuarios'] as $ruta) {
            $this->actingAs($vendedor)->get($ruta)->assertForbidden();
        }

        // Depósito: dashboard, productos, stock y pedidos.
        foreach (['/', '/productos', '/stock', '/pedidos'] as $ruta) {
            $this->actingAs($deposito)->get($ruta)->assertOk();
        }

        foreach (['/ventas', '/clientes', '/reportes', '/usuarios'] as $ruta) {
            $this->actingAs($deposito)->get($ruta)->assertForbidden();
        }

        // Administrador: accede a todo.
        foreach (['/', '/productos', '/stock', '/pedidos', '/ventas', '/clientes', '/reportes', '/usuarios'] as $ruta) {
            $this->actingAs($admin)->get($ruta)->assertOk();
        }
    }

    public function test_la_navegacion_muestra_usuarios_solo_al_administrador(): void
    {
        $this->actingAs($this->usuario(User::ROLE_VENDEDOR))
            ->get('/')
            ->assertOk()
            ->assertDontSee('Usuarios');

        $this->actingAs($this->admin())
            ->get('/')
            ->assertOk()
            ->assertSee('Usuarios');
    }
}
