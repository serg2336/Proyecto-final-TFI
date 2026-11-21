<?php

namespace Tests\Feature;

use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovimientoStockTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function usuario(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function producto(array $atributos = []): Producto
    {
        return Producto::create(array_merge([
            'codigo' => 'REP-001',
            'descripcion' => 'Filtro de aceite',
            'marca' => 'Fiat',
            'modelo' => 'Palio',
            'precio' => 12500.50,
            'stock' => 10,
            'stock_minimo' => 5,
        ], $atributos));
    }

    public function test_invitado_no_puede_registrar_movimientos(): void
    {
        $producto = $this->producto();

        $this->post('/stock/'.$producto->id.'/movimientos', [
            'tipo' => Movimiento::TIPO_ENTRADA,
            'cantidad' => 5,
        ])->assertRedirect('/login');

        $this->assertDatabaseCount('movimientos', 0);
    }

    public function test_deposito_registra_una_entrada_y_actualiza_el_stock(): void
    {
        $producto = $this->producto(['stock' => 10]);

        $this->actingAs($this->usuario(User::ROLE_DEPOSITO))
            ->from('/stock')
            ->post('/stock/'.$producto->id.'/movimientos', [
                'tipo' => Movimiento::TIPO_ENTRADA,
                'cantidad' => 5,
                'motivo' => 'Compra a proveedor',
            ])
            ->assertRedirect('/stock')
            ->assertSessionHas('success');

        $this->assertSame(15, $producto->fresh()->stock);

        $this->assertDatabaseHas('movimientos', [
            'producto_id' => $producto->id,
            'tipo' => Movimiento::TIPO_ENTRADA,
            'cantidad' => 5,
            'motivo' => 'Compra a proveedor',
        ]);
    }

    public function test_deposito_registra_una_salida_y_actualiza_el_stock(): void
    {
        $producto = $this->producto(['stock' => 10]);

        $this->actingAs($this->usuario(User::ROLE_DEPOSITO))
            ->from('/stock')
            ->post('/stock/'.$producto->id.'/movimientos', [
                'tipo' => Movimiento::TIPO_SALIDA,
                'cantidad' => 4,
                'motivo' => 'Venta de mostrador',
            ])
            ->assertRedirect('/stock')
            ->assertSessionHas('success');

        $this->assertSame(6, $producto->fresh()->stock);

        $this->assertDatabaseHas('movimientos', [
            'producto_id' => $producto->id,
            'tipo' => Movimiento::TIPO_SALIDA,
            'cantidad' => 4,
        ]);
    }

    public function test_no_se_puede_sacar_mas_stock_del_disponible(): void
    {
        $producto = $this->producto(['stock' => 3]);

        $this->actingAs($this->usuario(User::ROLE_DEPOSITO))
            ->from('/stock')
            ->post('/stock/'.$producto->id.'/movimientos', [
                'tipo' => Movimiento::TIPO_SALIDA,
                'cantidad' => 5,
            ])
            ->assertRedirect('/stock')
            ->assertSessionHasErrors('cantidad');

        $this->assertSame(3, $producto->fresh()->stock);
        $this->assertDatabaseCount('movimientos', 0);
    }

    public function test_la_cantidad_debe_ser_un_entero_positivo(): void
    {
        $producto = $this->producto();

        $this->actingAs($this->usuario(User::ROLE_DEPOSITO))
            ->from('/stock')
            ->post('/stock/'.$producto->id.'/movimientos', [
                'tipo' => Movimiento::TIPO_ENTRADA,
                'cantidad' => 0,
            ])
            ->assertSessionHasErrors('cantidad');

        $this->actingAs($this->usuario(User::ROLE_DEPOSITO))
            ->from('/stock')
            ->post('/stock/'.$producto->id.'/movimientos', [
                'tipo' => 'otro',
                'cantidad' => 2,
            ])
            ->assertSessionHasErrors('tipo');

        $this->assertSame(10, $producto->fresh()->stock);
        $this->assertDatabaseCount('movimientos', 0);
    }

    public function test_vendedor_no_puede_registrar_movimientos(): void
    {
        $producto = $this->producto();

        $this->actingAs($this->usuario(User::ROLE_VENDEDOR))
            ->post('/stock/'.$producto->id.'/movimientos', [
                'tipo' => Movimiento::TIPO_ENTRADA,
                'cantidad' => 1,
            ])
            ->assertForbidden();

        $this->assertSame(10, $producto->fresh()->stock);
        $this->assertDatabaseCount('movimientos', 0);
    }

    public function test_stock_muestra_la_alerta_de_stock_bajo(): void
    {
        $this->actingAs($this->usuario(User::ROLE_DEPOSITO));

        $this->producto(['descripcion' => 'Pastillas de freno', 'stock' => 2, 'stock_minimo' => 5]);
        $this->producto(['codigo' => 'OK-001', 'descripcion' => 'Filtro de aire', 'stock' => 20, 'stock_minimo' => 5]);

        $this->get('/stock')
            ->assertOk()
            ->assertSee('1 repuesto(s) con stock bajo o agotado')
            ->assertSee('Pastillas de freno')
            ->assertSee('Stock bajo');
    }

    public function test_stock_muestra_el_historial_de_movimientos(): void
    {
        $this->actingAs($this->usuario(User::ROLE_DEPOSITO));

        $producto = $this->producto(['descripcion' => 'Correa Poly-V']);

        $producto->movimientos()->create([
            'tipo' => Movimiento::TIPO_ENTRADA,
            'cantidad' => 12,
            'motivo' => 'Compra a proveedor',
        ]);

        $this->get('/stock')
            ->assertOk()
            ->assertSee('Historial de movimientos')
            ->assertSee('Correa Poly-V')
            ->assertSee('Compra a proveedor')
            ->assertSee('+ 12');
    }

    public function test_stock_muestra_estado_vacio_del_historial(): void
    {
        $this->actingAs($this->usuario(User::ROLE_DEPOSITO));

        $this->get('/stock')
            ->assertOk()
            ->assertSee('Todavía no hay movimientos registrados')
            ->assertSee('Registrá una entrada o salida para ver el historial.');
    }

    public function test_vendedor_ve_el_stock_pero_no_el_formulario_de_movimientos(): void
    {
        $this->actingAs($this->usuario(User::ROLE_VENDEDOR));

        $this->producto(['descripcion' => 'Filtro de aceite']);

        $this->get('/stock')
            ->assertOk()
            ->assertSee('Filtro de aceite')
            ->assertDontSee('Registrar movimiento');
    }
}
