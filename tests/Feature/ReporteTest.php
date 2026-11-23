<?php

namespace Tests\Feature;

use App\Models\Movimiento;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReporteTest extends TestCase
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
      
            'descripcion' => 'Filtro de aceite',
            'marca' => 'Fiat',
            'modelo' => 'Palio',
            'precio' => 1000,
            'stock' => 10,
            'stock_minimo' => 3,
        ], $atributos));
    }

    private function movimiento(Producto $producto, array $atributos = []): Movimiento
    {
        return Movimiento::create(array_merge([
            'producto_id' => $producto->id,
            'tipo' => Movimiento::TIPO_ENTRADA,
            'cantidad' => 5,
            'motivo' => 'Compra a proveedor',
        ], $atributos));
    }

    public function test_invitado_es_redirigido_al_login(): void
    {
        $this->get('/reportes')->assertRedirect('/login');
    }

    public function test_vendedor_no_puede_ver_reportes(): void
    {
        $this->actingAs($this->usuario(User::ROLE_VENDEDOR))
            ->get('/reportes')
            ->assertForbidden();
    }

    public function test_administrador_ve_reportes_con_datos_reales(): void
    {
       
        $producto = $this->producto(['precio' => 1500, 'stock' => 4, 'stock_minimo' => 5]);
        $this->movimiento($producto, ['cantidad' => 7, 'motivo' => 'Compra inicial']);
        Pedido::create([
            'codigo' => 'PED-001',
            'producto_id' => $producto->id,
            'cantidad' => 6,
            'proveedor' => 'Repuestos del Norte S.A.',
            'estado' => 'pendiente',
        ]);

        $this->actingAs($this->usuario(User::ROLE_ADMIN))
            ->get('/reportes')
            ->assertOk()
            ->assertSee('Generación de reportes')
            ->assertSee('Reporte de inventario')
            ->assertSee('Reporte de movimientos de stock')
            ->assertSee('Reporte de pedidos a proveedores')
            ->assertSee('Filtro de aceite')
            ->assertSee('Compra inicial')
            //->assertSee('PED-')
            ->assertSee('Repuestos del Norte S.A.')
            ->assertSee('$6.000,00');
    }

    public function test_el_filtro_de_fechas_excluye_movimientos_viejos(): void
    {
        $producto = $this->producto();

        $viejo = $this->movimiento($producto, ['motivo' => 'Movimiento viejo']);
        $viejo->forceFill(['created_at' => now()->subDays(40)])->save();

        $this->movimiento($producto, ['motivo' => 'Movimiento reciente']);

        $this->actingAs($this->usuario(User::ROLE_ADMIN))
            ->get('/reportes?desde='.now()->startOfMonth()->format('Y-m-d').'&hasta='.now()->format('Y-m-d'))
            ->assertOk()
            ->assertSee('Movimiento reciente')
            ->assertDontSee('Movimiento viejo');
    }

    public function test_valida_el_rango_de_fechas(): void
    {
        $this->actingAs($this->usuario(User::ROLE_ADMIN))
            ->get('/reportes?desde=2026-11-20&hasta=2026-11-10')
            ->assertSessionHasErrors('hasta');
    }

    public function test_exporta_el_inventario_en_pdf(): void
    {
        $this->producto(['descripcion' => 'Pastillas de freno']);

        $respuesta = $this->actingAs($this->usuario(User::ROLE_ADMIN))
            ->get(route('reportes.inventario.pdf'));

        $respuesta->assertOk();
        $respuesta->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString(
            'attachment',
            (string) $respuesta->headers->get('content-disposition')
        );
    }

    public function test_exporta_el_inventario_en_excel(): void
    {
        $this->producto();

        $respuesta = $this->actingAs($this->usuario(User::ROLE_ADMIN))
            ->get(route('reportes.inventario.excel'));

        $respuesta->assertOk();
        $respuesta->assertHeader(
            'content-type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
    }

    public function test_exporta_movimientos_y_pedidos(): void
    {
        $producto = $this->producto();
        $this->movimiento($producto);
        Pedido::create([
            'producto_id' => $producto->id,
            'cantidad' => 2,
            'proveedor' => 'Distribuidora Fiat',
            'estado' => 'recibido',
        ]);

        $admin = $this->usuario(User::ROLE_ADMIN);

        $this->actingAs($admin)->get(route('reportes.movimientos.pdf'))->assertOk();
        $this->actingAs($admin)->get(route('reportes.pedidos.excel'))->assertOk();
    }

    public function test_vendedor_no_puede_exportar(): void
    {
        $this->actingAs($this->usuario(User::ROLE_VENDEDOR))
            ->get(route('reportes.inventario.pdf'))
            ->assertForbidden();
    }
}
