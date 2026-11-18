<?php

namespace Tests\Feature;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PedidoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function usuario(string $rol): User
    {
        return User::factory()->create([
            'role' => $rol,
            'email' => $rol.'@italfiat.com',
        ]);
    }

    private function producto(array $atributos = []): Producto
    {
        return Producto::create(array_merge([
            'codigo' => 'REP-001',
            'descripcion' => 'Filtro de aceite',
            'marca' => 'Fiat',
            'modelo' => 'Palio',
            'precio' => 12500.50,
            'stock' => 2,
            'stock_minimo' => 5,
        ], $atributos));
    }

    private function pedido(Producto $producto, array $atributos = []): Pedido
    {
        return Pedido::create(array_merge([
            'producto_id' => $producto->id,
            'cantidad' => 8,
            'proveedor' => 'Repuestos del Norte S.A.',
            'estado' => 'pendiente',
        ], $atributos));
    }

    public function test_invitado_es_redirigido_al_login(): void
    {
        $this->get('/pedidos')->assertRedirect('/login');
    }

    public function test_administrador_ve_el_listado_de_pedidos(): void
    {
        $producto = $this->producto();
        $this->pedido($producto);

        $this->actingAs($this->usuario(User::ROLE_ADMIN))
            ->get('/pedidos')
            ->assertOk()
            ->assertSee('PED-001')
            ->assertSee('Filtro de aceite')
            ->assertSee('Repuestos del Norte S.A.')
            ->assertSee('Recibir pedido');
    }

    public function test_deposito_puede_ver_los_pedidos(): void
    {
        $this->actingAs($this->usuario(User::ROLE_DEPOSITO))
            ->get('/pedidos')
            ->assertOk()
            ->assertSee('Todavía no hay pedidos');
    }

    public function test_vendedor_no_puede_acceder_a_pedidos(): void
    {
        $this->actingAs($this->usuario(User::ROLE_VENDEDOR))
            ->get('/pedidos')
            ->assertForbidden();
    }

    public function test_se_puede_registrar_un_pedido(): void
    {
        $producto = $this->producto();

        $this->actingAs($this->usuario(User::ROLE_ADMIN))
            ->post('/pedidos', [
                'producto_id' => $producto->id,
                'cantidad' => 6,
                'proveedor' => 'Distribuidora Fiat',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pedidos', [
            'producto_id' => $producto->id,
            'cantidad' => 6,
            'proveedor' => 'Distribuidora Fiat',
            'estado' => 'pendiente',
        ]);
    }

    public function test_el_pedido_valida_producto_y_cantidad(): void
    {
        $this->actingAs($this->usuario(User::ROLE_ADMIN))
            ->post('/pedidos', [
                'producto_id' => 999,
                'cantidad' => 0,
            ])
            ->assertSessionHasErrors(['producto_id', 'cantidad']);
    }

    public function test_recibir_un_pedido_suma_stock_y_cambia_el_estado(): void
    {
        $producto = $this->producto(['stock' => 2]);
        $pedido = $this->pedido($producto, ['cantidad' => 8]);

        $this->actingAs($this->usuario(User::ROLE_ADMIN))
            ->patch(route('pedidos.recibir', $pedido))
            ->assertRedirect();

        $this->assertSame(10, $producto->fresh()->stock);
        $this->assertSame('recibido', $pedido->fresh()->estado);
    }

    public function test_recibir_dos_veces_no_duplica_el_stock(): void
    {
        $producto = $this->producto(['stock' => 2]);
        $pedido = $this->pedido($producto, ['cantidad' => 8]);
        $admin = $this->usuario(User::ROLE_ADMIN);

        $this->actingAs($admin)->patch(route('pedidos.recibir', $pedido));
        $this->actingAs($admin)->patch(route('pedidos.recibir', $pedido));

        $this->assertSame(10, $producto->fresh()->stock);
    }
}
