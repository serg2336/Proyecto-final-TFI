<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    // 1. Mostrar la lista de productos (con buscador y filtro por marca/modelo)
    public function index(Request $request)
    {
        $buscar = $request->query('buscar');
        $buscar = is_string($buscar) ? trim($buscar) : '';

        $marca = $request->query('marca');
        $marca = is_string($marca) ? trim($marca) : '';

        $modelo = $request->query('modelo');
        $modelo = is_string($modelo) ? trim($modelo) : '';

        $productos = Producto::query()
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($query) use ($buscar) {
                    $query->where('codigo', 'like', "%{$buscar}%")
                        ->orWhere('descripcion', 'like', "%{$buscar}%")
                        ->orWhere('modelo', 'like', "%{$buscar}%");
                });
            })
            ->when($marca !== '', fn ($query) => $query->where('marca', $marca))
            ->when($modelo !== '', fn ($query) => $query->where('modelo', 'like', "%{$modelo}%"))
            ->orderBy('descripcion')
            ->get();

        $marcas = Producto::query()
            ->select('marca')
            ->distinct()
            ->orderBy('marca')
            ->pluck('marca');

        $modelos = Producto::query()
            ->whereNotNull('modelo')
            ->where('modelo', '!=', '')
            ->select('modelo')
            ->distinct()
            ->orderBy('modelo')
            ->pluck('modelo');

        return view('productos', compact('productos', 'marcas', 'modelos'));
    }

    // 2. Guardar un producto nuevo
    public function store(Request $request)
    {
        Producto::create($this->validar($request));

        return redirect()->route('productos.index')->with('success', 'Producto creado exitosamente.');
    }

    // 3. Actualizar un producto existente
    public function update(Request $request, Producto $producto)
    {
        $producto->update($this->validar($request, $producto));

        return redirect()->route('productos.index')->with('success', 'Producto actualizado exitosamente.');
    }

    // 4. Eliminar un producto (Protegido por el Middleware)
    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }

    /**
     * Reglas de validación compartidas por alta y edición.
     *
     * @return array<string, mixed>
     */
    private function validar(Request $request, ?Producto $producto = null): array
    {
        return $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:255',
                Rule::unique('productos', 'codigo')->ignore($producto?->id),
            ],
            'descripcion' => 'required|string|max:255',
            'marca' => 'required|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
        ], [
            'codigo.required' => 'El código del repuesto es obligatorio.',
            'codigo.unique' => 'Ya existe un producto con ese código.',
            'codigo.max' => 'El código no puede superar los 255 caracteres.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.max' => 'La descripción no puede superar los 255 caracteres.',
            'marca.required' => 'La marca es obligatoria.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio no puede ser negativo.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser negativo.',
            'stock_minimo.integer' => 'El stock mínimo debe ser un número entero.',
            'stock_minimo.min' => 'El stock mínimo no puede ser negativo.',
        ]);
    }
}
