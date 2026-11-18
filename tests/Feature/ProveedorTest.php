<?php

namespace Tests\Feature;

use App\Models\Proveedor;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProveedorTest extends TestCase
{
    use RefreshDatabase;

    public function test_se_puede_crear_un_proveedor(): void
    {
        Proveedor::create([
            'nombre' => 'Repuestos del Norte S.A.',
            'email' => 'ventas@repuestosdelnorte.com',
            'telefono' => '11-5555-0101',
        ]);

        $this->assertDatabaseHas('proveedores', [
            'nombre' => 'Repuestos del Norte S.A.',
            'email' => 'ventas@repuestosdelnorte.com',
            'telefono' => '11-5555-0101',
        ]);
    }

    public function test_los_campos_de_contacto_pueden_quedar_vacios(): void
    {
        Proveedor::create(['nombre' => 'Distribuidora Fiat']);

        $this->assertDatabaseHas('proveedores', [
            'nombre' => 'Distribuidora Fiat',
            'email' => null,
            'telefono' => null,
        ]);
    }

    public function test_no_admite_dos_proveedores_con_el_mismo_nombre(): void
    {
        $this->expectException(QueryException::class);

        Proveedor::create(['nombre' => 'Distribuidora Fiat']);
        Proveedor::create(['nombre' => 'Distribuidora Fiat']);
    }
}
