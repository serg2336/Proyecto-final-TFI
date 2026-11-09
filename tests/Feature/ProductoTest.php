<?php

namespace Tests\Feature;

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
            ->assertSee('No hay productos cargados todavía');
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
            ->assertDontSee('Repuesto con stock suficiente');
    }
}
