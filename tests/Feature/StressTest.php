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
     *
     * @param  int  $desde  Primer índice de código, para sumar lotes sin repetir el `codigo` único.
     */
    private function crearProductosMasivos(int $cantidad, int $desde = 1): void
    {
        $ahora = now();
        $lote = [];

        for ($i = $desde; $i < $desde + $cantidad; $i++) {
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
        $this->crearProductosMasivos(100);
        $this->actingAs($this->admin());

        // Calienta la ruta para que las queries de arranque no contaminen la medición.
        $this->get('/productos')->assertOk();

        $queriesCon100 = $this->queriesDeLaBusqueda();

        // Multiplica el volumen por 20: con N+1 las queries crecerían junto a las filas.
        $this->crearProductosMasivos(self::VOLUMEN_PRODUCTOS - 100, 101);

        $queriesCon2000 = $this->queriesDeLaBusqueda();

        $this->assertSame(
            $queriesCon100,
            $queriesCon2000,
            sprintf(
                'La búsqueda pasó de %d a %d queries al pasar de 100 a %d productos: escala por fila (N+1).',
                $queriesCon100,
                $queriesCon2000,
                self::VOLUMEN_PRODUCTOS
            )
        );

        // El costo debe ser fijo (listado + marcas + modelos), no proporcional a las filas.
        $this->assertLessThan(
            15,
            $queriesCon2000,
            sprintf('La búsqueda disparó %d queries (umbral: 15). Posible N+1.', $queriesCon2000)
        );
    }

    /**
     * Queries disparadas por una búsqueda puntual en el listado de productos.
     */
    private function queriesDeLaBusqueda(): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->get('/productos?buscar=STRESS-00042')
            ->assertOk()
            ->assertSee('STRESS-00042');

        $queries = count(DB::getQueryLog());

        DB::disableQueryLog();
        DB::flushQueryLog();

        return $queries;
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

    public function test_el_listado_de_stock_responde_con_alto_volumen(): void
    {
        $this->crearProductosMasivos(self::VOLUMEN_PRODUCTOS);
        $this->crearMovimientosMasivos(self::VOLUMEN_MOVIMIENTOS);
        $this->actingAs($this->admin());

        $inicio = microtime(true);

        $respuesta = $this->get('/stock');

        $duracion = microtime(true) - $inicio;

        // /stock es la vista más pesada: 2000 filas de productos + historial + fecha relativa por fila.
        $respuesta->assertOk()
            ->assertSee('STRESS-00001')
            ->assertSee('STRESS-02000')
            ->assertSee('Movimiento de stress '.self::VOLUMEN_MOVIMIENTOS);

        $this->assertLessThan(
            5,
            $duracion,
            sprintf('El listado de stock con %d productos y %d movimientos tomó %.2fs (umbral: 5s).', self::VOLUMEN_PRODUCTOS, self::VOLUMEN_MOVIMIENTOS, $duracion)
        );
    }
}
