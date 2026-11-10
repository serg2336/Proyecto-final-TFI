<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    // 1. Mostrar la lista de productos (con buscador y filtro por marca)
    public function index(Request $request)
    {
        $buscar = $request->query('buscar');
        $buscar = is_string($buscar) ? trim($buscar) : '';

        $marca = $request->query('marca');
        $marca = is_string($marca) ? trim($marca) : '';

        $productos = Producto::query()
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($query) use ($buscar) {
                    $query->where('codigo', 'like', "%{$buscar}%")
                        ->orWhere('descripcion', 'like', "%{$buscar}%");
                });
            })
            ->when($marca !== '', fn ($query) => $query->where('marca', $marca))
            ->orderBy('descripcion')
            ->get();

        $marcas = Producto::query()
            ->select('marca')
            ->distinct()
            ->orderBy('marca')
            ->pluck('marca');

        return view('productos', compact('productos', 'marcas'));
    }

    // 2. Guardar un producto nuevo
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|unique:productos,codigo',
            'descripcion' => 'required',
            'marca' => 'required',
            'precio' => 'required|numeric',
            'stock' => 'required|integer',
        ], [
            'codigo.required' => 'El código del repuesto es obligatorio.',
            'codigo.unique' => 'Ya existe un producto con ese código.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'marca.required' => 'La marca es obligatoria.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
        ]);

        Producto::create($request->all());

        return redirect()->route('productos.index')->with('success', 'Producto creado exitosamente.');
    }

    // 3. Eliminar un producto (Protegido por el Middleware)
    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }
}