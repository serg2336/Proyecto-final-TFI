<?php

namespace Tests\Feature;

use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoTest extends TestCase
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

    private function producto(array $atributos = []): Producto
    {
        return Producto::create(array_merge([
            'codigo' => 'REP-001',
            'descripcion' => 'Filtro de aceite',
            'marca' => 'Fiat',
            'modelo' => 'Palio',
            'precio' => 12500.50,
            'stock' => 10,
            'stock_minimo' => 3,
        ], $atributos));
    }

    public function test_invitado_es_redirigido_al_login(): void
    {
        $this->get('/productos')->assertRedirect('/login');
    }

    public function test_listado_muestra_los_productos(): void
    {
        $this->actingAs($this->admin());

        $this->producto();

        $this->get('/productos')
            ->assertOk()
            ->assertSee('REP-001')
            ->assertSee('Filtro de aceite')
            ->assertSee('Fiat');
    }

    public function test_listado_muestra_estado_vacio(): void
    {
        $this->actingAs($this->admin());

        $this->get('/productos')
            ->assertOk()
            ->assertSee('Todavía no hay productos')
            ->assertSee('Cuando agregues el primero, va a aparecer acá')
            ->assertSee('Sin marcas registradas');
    }

    public function test_busca_por_codigo_o_descripcion(): void
    {
        $this->actingAs($this->admin());

        $this->producto(['codigo' => 'FIL-001', 'descripcion' => 'Filtro de aceite']);
        $this->producto(['codigo' => 'PAS-002', 'descripcion' => 'Pastillas de freno']);

        $this->get('/productos?buscar=filtro')
            ->assertOk()
            ->assertSee('FIL-001')
            ->assertDontSee('PAS-002');

        $this->get('/productos?buscar=PAS-002')
            ->assertOk()
            ->assertSee('PAS-002')
            ->assertDontSee('FIL-001');
    }

    public function test_filtra_por_marca(): void
    {
        $this->actingAs($this->admin());

        $this->producto(['codigo' => 'FIA-001', 'marca' => 'Fiat']);
        $this->producto(['codigo' => 'VW-001', 'marca' => 'Volkswagen']);

        $this->get('/productos?marca=Volkswagen')
            ->assertOk()
            ->assertSee('VW-001')
            ->assertDontSee('FIA-001');
    }

    public function test_muestra_estado_sin_resultados(): void
    {
        $this->actingAs($this->admin());

        $this->producto();

        $this->get('/productos?buscar=inexistente')
            ->assertOk()
            ->assertSee('No se encontraron resultados')
            ->assertSee('Probá con otro término o revisá los filtros aplicados');
    }

    public function test_se_puede_crear_un_producto(): void
    {
        $this->actingAs($this->admin());

        $this->post('/productos', [
            'codigo' => 'REP-002',
            'descripcion' => 'Pastillas de freno',
            'marca' => 'Fiat',
            'modelo' => 'Uno',
            'precio' => 18200,
            'stock' => 4,
            'stock_minimo' => 5,
        ])
            ->assertRedirect(route('productos.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('productos', [
            'codigo' => 'REP-002',
            'descripcion' => 'Pastillas de freno',
            'stock' => 4,
        ]);
    }

    public function test_crear_valida_los_campos_obligatorios(): void
    {
        $this->actingAs($this->admin());

        $this->post('/productos', [])
            ->assertSessionHasErrors(['codigo', 'descripcion', 'marca', 'precio', 'stock']);

        $this->assertDatabaseCount('productos', 0);
    }

    public function test_crear_rechaza_codigo_duplicado(): void
    {
        $this->actingAs($this->admin());

        $this->producto();

        $this->post('/productos', [
            'codigo' => 'REP-001',
            'descripcion' => 'Otro repuesto',
            'marca' => 'Fiat',
            'precio' => 1000,
            'stock' => 1,
        ])->assertSessionHasErrors('codigo');

        $this->assertDatabaseCount('productos', 1);
    }

    public function test_se_puede_eliminar_un_producto(): void
    {
        $this->actingAs($this->admin());

        $producto = $this->producto();

        $this->delete('/productos/'.$producto->id)
            ->assertRedirect(route('productos.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('productos', ['id' => $producto->id]);
    }

    public function test_dashboard_usa_datos_reales(): void
    {
        $this->actingAs($this->admin());

        $this->producto([
            'codigo' => 'OK-001',
            'descripcion' => 'Repuesto con stock suficiente',
            'stock' => 10,
            'stock_minimo' => 5,
        ]);
        $this->producto([
            'codigo' => 'CRI-001',
            'descripcion' => 'Repuesto en estado crítico',
            'stock' => 2,
            'stock_minimo' => 5,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertViewHas('totalProductos', 2)
            ->assertViewHas('stockCritico', 1)
            ->assertSee('Repuesto en estado crítico')
            ->assertDontSee('Repuesto con stock suficiente')
            ->assertSee('Cada producto, en su lugar.')
            ->assertSee('Información precisa para decisiones precisas.');
    }

    public function test_stock_muestra_existencias_y_estados(): void
    {
        $this->actingAs($this->admin());

        $this->producto(['codigo' => 'AGO-001', 'descripcion' => 'Repuesto agotado', 'stock' => 0]);
        $this->producto(['codigo' => 'BAJ-001', 'descripcion' => 'Repuesto con pocas unidades', 'stock' => 2, 'stock_minimo' => 5]);
        $this->producto(['codigo' => 'DIS-001', 'descripcion' => 'Repuesto disponible', 'stock' => 10, 'stock_minimo' => 5]);

        $this->get('/stock')
            ->assertOk()
            ->assertSee('Stock actual')
            ->assertSee('Revisá existencias y detectá faltantes')
            ->assertSee('Repuesto agotado')
            ->assertSee('Sin stock')
            ->assertSee('Stock bajo')
            ->assertSee('Disponible')
            ->assertSee('Este producto no tiene unidades disponibles')
            ->assertSee('Quedan pocas unidades. Revisá el umbral de reposición');
    }

    public function test_stock_muestra_estado_vacio(): void
    {
        $this->actingAs($this->admin());

        $this->get('/stock')
            ->assertOk()
            ->assertSee('Todavía no hay productos')
            ->assertSee('Cuando agregues el primero, va a aparecer acá');
    }

    public function test_el_listado_ofrece_editar_cada_producto(): void
    {
        $this->actingAs($this->admin());

        $producto = $this->producto(['codigo' => 'EDI-001', 'descripcion' => 'Filtro de aire']);

        $this->get('/productos')
            ->assertOk()
            ->assertSee('Editar')
            ->assertSee('modalEditarProducto', false)
            ->assertSee('data-id="'.$producto->id.'"', false)
            ->assertSee('data-codigo="EDI-001"', false);
    }

    public function test_se_puede_editar_un_producto(): void
    {
        $this->actingAs($this->admin());

        $producto = $this->producto();

        $this->put('/productos/'.$producto->id, [
            'codigo' => 'REP-001',
            'descripcion' => 'Filtro de aceite premium',
            'marca' => 'Fiat',
            'modelo' => 'Siena',
            'precio' => 15900.75,
            'stock' => 8,
            'stock_minimo' => 4,
        ])
            ->assertRedirect(route('productos.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'descripcion' => 'Filtro de aceite premium',
            'modelo' => 'Siena',
            'stock' => 8,
            'stock_minimo' => 4,
        ]);
    }

    public function test_editar_conserva_el_mismo_codigo(): void
    {
        $this->actingAs($this->admin());

        $producto = $this->producto(['codigo' => 'REP-001']);

        $this->put('/productos/'.$producto->id, [
            'codigo' => 'REP-001',
            'descripcion' => 'Filtro de aceite actualizado',
            'marca' => 'Fiat',
            'precio' => 13000,
            'stock' => 10,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'descripcion' => 'Filtro de aceite actualizado',
        ]);
    }

    public function test_editar_rechaza_un_codigo_de_otro_producto(): void
    {
        $this->actingAs($this->admin());

        $this->producto(['codigo' => 'REP-001']);
        $producto = $this->producto(['codigo' => 'REP-002', 'descripcion' => 'Pastillas de freno']);

        $this->put('/productos/'.$producto->id, [
            'codigo' => 'REP-001',
            'descripcion' => 'Pastillas de freno',
            'marca' => 'Fiat',
            'precio' => 1000,
            'stock' => 1,
        ])->assertSessionHasErrors('codigo');

        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'codigo' => 'REP-002',
        ]);
    }

    public function test_editar_valida_los_campos_obligatorios(): void
    {
        $this->actingAs($this->admin());

        $producto = $this->producto();

        $this->put('/productos/'.$producto->id, [])
            ->assertSessionHasErrors(['codigo', 'descripcion', 'marca', 'precio', 'stock']);

        $this->assertDatabaseHas('productos', ['id' => $producto->id, 'codigo' => 'REP-001']);
    }

    public function test_filtra_por_modelo(): void
    {
        $this->actingAs($this->admin());

        $this->producto(['codigo' => 'PAL-001', 'marca' => 'Fiat', 'modelo' => 'Palio']);
        $this->producto(['codigo' => 'SIE-001', 'marca' => 'Fiat', 'modelo' => 'Siena']);

        $this->get('/productos?modelo=Siena')
            ->assertOk()
            ->assertSee('SIE-001')
            ->assertDontSee('PAL-001');
    }

    public function test_el_formulario_incluye_marcas_cargadas_por_otros_medios(): void
    {
        $this->actingAs($this->admin());

        $this->producto(['codigo' => 'CHE-001', 'marca' => 'Chevrolet', 'modelo' => 'Corsa']);

        $this->get('/productos')
            ->assertOk()
            ->assertSee('value="Chevrolet"', false);
    }

    public function test_vendedor_puede_editar_pero_no_eliminar(): void
    {
        $vendedor = User::factory()->create(['role' => User::ROLE_VENDEDOR]);
        $producto = $this->producto();

        $this->actingAs($vendedor)
            ->put('/productos/'.$producto->id, [
                'codigo' => $producto->codigo,
                'descripcion' => 'Filtro de aceite editado por vendedor',
                'marca' => $producto->marca,
                'precio' => $producto->precio,
                'stock' => $producto->stock,
            ])->assertRedirect(route('productos.index'));

        $this->actingAs($vendedor)
            ->delete('/productos/'.$producto->id)
            ->assertForbidden();

        $this->assertDatabaseHas('productos', ['id' => $producto->id]);
    }

    public function test_vendedor_puede_crear_y_editar_productos(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_VENDEDOR]));

        $this->post('/productos', [
            'codigo' => 'VEN-001',
            'descripcion' => 'Bujía NGK',
            'marca' => 'Fiat',
            'precio' => 3500,
            'stock' => 12,
        ])->assertRedirect(route('productos.index'));

        $producto = Producto::where('codigo', 'VEN-001')->firstOrFail();

        $this->put('/productos/'.$producto->id, [
            'codigo' => 'VEN-001',
            'descripcion' => 'Bujía NGK Iridium',
            'marca' => 'Fiat',
            'precio' => 4300,
            'stock' => 10,
        ])->assertRedirect(route('productos.index'));

        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'descripcion' => 'Bujía NGK Iridium',
        ]);
    }

    public function test_crear_producto_registra_movimiento_de_entrada(): void
    {
        $this->actingAs($this->admin());

        $this->post('/productos', [
            'codigo' => 'NUE-001',
            'descripcion' => 'Bomba de agua',
            'marca' => 'Fiat',
            'precio' => 22000,
            'stock' => 7,
        ])->assertRedirect(route('productos.index'));

        $producto = Producto::where('codigo', 'NUE-001')->firstOrFail();

        $this->assertDatabaseHas('movimientos', [
            'producto_id' => $producto->id,
            'tipo' => Movimiento::TIPO_ENTRADA,
            'cantidad' => 7,
        ]);
    }

    public function test_editar_el_stock_registra_el_movimiento_correspondiente(): void
    {
        $this->actingAs($this->admin());

        $producto = $this->producto(['stock' => 10]);

        $this->put('/productos/'.$producto->id, [
            'codigo' => $producto->codigo,
            'descripcion' => $producto->descripcion,
            'marca' => $producto->marca,
            'precio' => $producto->precio,
            'stock' => 6,
        ])->assertRedirect(route('productos.index'));

        $this->assertDatabaseHas('movimientos', [
            'producto_id' => $producto->id,
            'tipo' => Movimiento::TIPO_SALIDA,
            'cantidad' => 4,
        ]);

        $this->put('/productos/'.$producto->id, [
            'codigo' => $producto->codigo,
            'descripcion' => $producto->descripcion,
            'marca' => $producto->marca,
            'precio' => $producto->precio,
            'stock' => 9,
        ])->assertRedirect(route('productos.index'));

        $this->assertDatabaseHas('movimientos', [
            'producto_id' => $producto->id,
            'tipo' => Movimiento::TIPO_ENTRADA,
            'cantidad' => 3,
        ]);
    }

    public function test_editar_sin_cambiar_el_stock_no_registra_movimiento(): void
    {
        $this->actingAs($this->admin());

        $producto = $this->producto(['stock' => 10]);

        $this->put('/productos/'.$producto->id, [
            'codigo' => $producto->codigo,
            'descripcion' => 'Filtro de aceite premium',
            'marca' => $producto->marca,
            'precio' => $producto->precio,
            'stock' => 10,
        ])->assertRedirect(route('productos.index'));

        $this->assertDatabaseCount('movimientos', 0);
    }

    public function test_dashboard_muestra_los_ultimos_movimientos(): void
    {
        $this->actingAs($this->admin());

        $producto = $this->producto(['descripcion' => 'Filtro de aceite']);

        $producto->movimientos()->create([
            'tipo' => Movimiento::TIPO_ENTRADA,
            'cantidad' => 15,
            'motivo' => 'Compra a proveedor',
        ]);
        $producto->movimientos()->create([
            'tipo' => Movimiento::TIPO_SALIDA,
            'cantidad' => 3,
            'motivo' => 'Venta de mostrador',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertViewHas('ultimosMovimientos', fn ($movimientos) => $movimientos->count() === 2)
            ->assertSee('Filtro de aceite')
            ->assertSee('Ingreso')
            ->assertSee('Salida')
            ->assertSee('+ 15')
            ->assertSee('- 3');
    }

    public function test_dashboard_muestra_estado_vacio_de_movimientos(): void
    {
        $this->actingAs($this->admin());

        $this->get('/')
            ->assertOk()
            ->assertSee('Todavía no hay movimientos registrados')
            ->assertSee('Los ingresos y salidas de stock van a aparecer acá.');
    }
}
