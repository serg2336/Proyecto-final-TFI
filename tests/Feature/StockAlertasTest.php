<?php

namespace Tests\Feature;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockAlertasTest extends TestCase
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
            'role' => User::ROLE_ADMIN,
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
            'stock_minimo' => 5,
        ], $atributos));
    }

    public function test_muestra_alerta_roja_cuando_hay_stock_bajo(): void
    {
        $this->producto([
            'codigo' => 'REP-002',
            'descripcion' => 'Pastillas de freno',
            'stock' => 2,
            'stock_minimo' => 5,
        ]);

        $this->actingAs($this->admin())
            ->get('/stock')
            ->assertOk()
            ->assertSee('repuesto(s) con stock bajo o agotado')
            ->assertSee('Pastillas de freno (2 un.)')
            ->assertSee('bg-red-600', false)
            ->assertSee('bg-red-800', false)
            ->assertSee('text-white', false)
            ->assertSee('Stock bajo');
    }

    public function test_muestra_sin_stock_con_rojo_solido(): void
    {
        $this->producto([
            'codigo' => 'REP-003',
            'descripcion' => 'Correa Poly-V',
            'stock' => 0,
            'stock_minimo' => 3,
        ]);

        $this->actingAs($this->admin())
            ->get('/stock')
            ->assertOk()
            ->assertSee('Sin stock')
            ->assertSee('text-white bg-red-800', false);
    }

    public function test_no_muestra_alerta_roja_si_todo_esta_sobre_el_minimo(): void
    {
        $this->producto(['stock' => 10, 'stock_minimo' => 5]);

        $this->actingAs($this->admin())
            ->get('/stock')
            ->assertOk()
            ->assertDontSee('repuesto(s) con stock bajo o agotado')
            ->assertDontSee('bg-red-600', false)
            ->assertSee('Disponible');
    }
}
