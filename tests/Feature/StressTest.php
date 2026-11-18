<?php

namespace Tests\Feature;

use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Pruebas de stress: alto volumen de datos y respuesta de los listados.
 *
 * Correr solo estas: php artisan test --group=stress
 */
#[Group('stress')]
class StressTest extends TestCase
{
    use RefreshDatabase;

    private const VOLUMEN_PRODUCTOS = 2000;

    private const VOLUMEN_MOVIMIENTOS = 1000;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function admin(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
    }

    /**
     * Alta masiva por lotes (evita 2000 inserts individuales de Eloquent).
     */
    private function crearProductosMasivos(int $cantidad): void
    {
        $ahora = now();
        $lote = [];

        for ($i = 1; $i <= $cantidad; $i++) {
            $lote[] = [
                'codigo' => sprintf('STRESS-%05d', $i),
                'descripcion' => 'Repuesto de prueba masiva '.$i,
                'marca' => $i % 2 === 0 ? 'Fiat' : 'Volkswagen',
                'modelo' => 'Modelo '.($i % 50),
                'precio' => 1000 + $i,
                'stock' => $i % 100,
                'stock_minimo' => 5,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ];

            if (count($lote) === 500) {
                DB::table('productos')->insert($lote);
                $lote = [];
            }
        }

        if ($lote !== []) {
            DB::table('productos')->insert($lote);
        }
    }

    private function crearMovimientosMasivos(int $cantidad): void
    {
        $producto = Producto::create([
            'codigo' => 'STRESS-MOV',
            'descripcion' => 'Producto con historial masivo',
            'marca' => 'Fiat',
            'precio' => 5000,
            'stock' => 500,
            'stock_minimo' => 5,
        ]);

        $ahora = now();
        $lote = [];

        for ($i = 1; $i <= $cantidad; $i++) {
            $lote[] = [
                'producto_id' => $producto->id,
                'tipo' => $i % 2 === 0 ? Movimiento::TIPO_ENTRADA : Movimiento::TIPO_SALIDA,
                'cantidad' => ($i % 10) + 1,
                'motivo' => 'Movimiento de stress '.$i,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ];

            if (count($lote) === 500) {
                DB::table('movimientos')->insert($lote);
                $lote = [];
            }
        }

        if ($lote !== []) {
            DB::table('movimientos')->insert($lote);
        }
    }

    public function test_la_carga_masiva_de_productos_completa_en_tiempo_razonable(): void
    {
        $inicio = microtime(true);

        $this->crearProductosMasivos(self::VOLUMEN_PRODUCTOS);

        $duracion = microtime(true) - $inicio;

        $this->assertSame(self::VOLUMEN_PRODUCTOS, Producto::count());
        $this->assertLessThan(
            10,
            $duracion,
            sprintf('Cargar %d productos tomó %.2fs (umbral: 10s).', self::VOLUMEN_PRODUCTOS, $duracion)
        );
    }

    public function test_el_listado_de_productos_responde_con_alto_volumen(): void
    {
        $this->crearProductosMasivos(self::VOLUMEN_PRODUCTOS);
        $this->actingAs($this->admin());

        $inicio = microtime(true);

        $respuesta = $this->get('/productos');

        $duracion = microtime(true) - $inicio;

        $respuesta->assertOk()->assertSee('STRESS-00001')->assertSee('STRESS-02000');
        $this->assertLessThan(
            5,
            $duracion,
            sprintf('El listado con %d productos tomó %.2fs (umbral: 5s).', self::VOLUMEN_PRODUCTOS, $duracion)
        );
    }

    public function test_la_busqueda_no_escala_en_queries_por_producto(): void
    {
        $this->crearProductosMasivos(self::VOLUMEN_PRODUCTOS);
        $this->actingAs($this->admin());

        DB::enableQueryLog();

        $this->get('/productos?buscar=STRESS-00042')
            ->assertOk()
            ->assertSee('STRESS-00042');

        $queries = count(DB::getQueryLog());

        DB::disableQueryLog();

        // El costo debe ser fijo (listado + marcas + modelos + sesión), no proporcional a 2000 filas.
        $this->assertLessThan(
            15,
            $queries,
            sprintf('La búsqueda disparó %d queries (umbral: 15). Posible N+1.', $queries)
        );
    }

    public function test_el_dashboard_responde_con_alto_volumen_de_productos_y_movimientos(): void
    {
        $this->crearProductosMasivos(self::VOLUMEN_PRODUCTOS);
        $this->crearMovimientosMasivos(self::VOLUMEN_MOVIMIENTOS);
        $this->actingAs($this->admin());

        $inicio = microtime(true);

        $respuesta = $this->get('/');

        $duracion = microtime(true) - $inicio;

        $respuesta->assertOk()->assertViewHas('totalProductos', self::VOLUMEN_PRODUCTOS + 1);
        $this->assertLessThan(
            5,
            $duracion,
            sprintf('El dashboard con %d productos y %d movimientos tomó %.2fs (umbral: 5s).', self::VOLUMEN_PRODUCTOS, self::VOLUMEN_MOVIMIENTOS, $duracion)
        );
    }
}
