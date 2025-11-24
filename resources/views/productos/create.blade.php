<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Mostrar lista de productos
     */
    public function index()
    {
        $productos = Producto::with('categoria')
            ->orderBy('nombre')
            ->paginate(15);
        
        return view('productos.index', compact('productos'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $categorias = Categoria::activas()->orderBy('nombre')->get();
        return view('productos.create', compact('categorias'));
    }

    /**
     * Guardar nuevo producto
     */
    public function store(Request $request)
    {
        $rules = [
            'codigo' => 'required|string|max:50|unique:productos,codigo',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'tipo' => 'required|in:accesorio,solvente,pintura,barniz',
            'marca' => 'nullable|string|max:100',
            'precio_venta' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0|max:100',
            'existencia_minima' => 'required|integer|min:0',
            'activo' => 'boolean'
        ];

        // Campos específicos según tipo
        if (in_array($request->tipo, ['pintura', 'barniz'])) {
            $rules['duracion_anios'] = 'nullable|integer|min:0';
            $rules['cobertura_m2'] = 'nullable|numeric|min:0';
        }

        if ($request->tipo === 'pintura') {
            $rules['color'] = 'nullable|string|max:50';
        }

        if (in_array($request->tipo, ['solvente', 'pintura', 'barniz'])) {
            $rules['unidad_medida'] = 'nullable|string|max:50';
        }

        if ($request->tipo === 'accesorio') {
            $rules['tamano'] = 'nullable|string|max:50';
        }

        $validated = $request->validate($rules);
        $validated['activo'] = $request->has('activo') ? 1 : 0;

        Producto::create($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Mostrar detalle del producto
     */
    public function show($id)
    {
        $producto = Producto::with(['categoria', 'inventarios.tienda'])
            ->findOrFail($id);
        
        return view('productos.show', compact('producto'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $producto = Producto::with('categoria')->findOrFail($id);
        $categorias = Categoria::activas()->orderBy('nombre')->get();
        
        return view('productos.edit', compact('producto', 'categorias'));
    }

    /**
     * Actualizar producto
     */
    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        
        $rules = [
            'codigo' => 'required|string|max:50|unique:productos,codigo,' . $id,
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'tipo' => 'required|in:accesorio,solvente,pintura,barniz',
            'marca' => 'nullable|string|max:100',
            'precio_venta' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0|max:100',
            'existencia_minima' => 'required|integer|min:0',
            'activo' => 'boolean'
        ];

        // Campos específicos según tipo
        if (in_array($request->tipo, ['pintura', 'barniz'])) {
            $rules['duracion_anios'] = 'nullable|integer|min:0';
            $rules['cobertura_m2'] = 'nullable|numeric|min:0';
        }

        if ($request->tipo === 'pintura') {
            $rules['color'] = 'nullable|string|max:50';
        }

        if (in_array($request->tipo, ['solvente', 'pintura', 'barniz'])) {
            $rules['unidad_medida'] = 'nullable|string|max:50';
        }

        if ($request->tipo === 'accesorio') {
            $rules['tamano'] = 'nullable|string|max:50';
        }

        $validated = $request->validate($rules);
        $validated['activo'] = $request->has('activo') ? 1 : 0;

        $producto->update($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Eliminar producto
     */
    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        
        // Verificar si tiene inventario
        if ($producto->inventarios()->sum('existencia') > 0) {
            return redirect()->route('productos.index')
                ->with('error', 'No se puede eliminar el producto porque tiene existencias en inventario.');
        }
        
        try {
            $producto->delete();
            return redirect()->route('productos.index')
                ->with('success', 'Producto eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('productos.index')
                ->with('error', 'Error al eliminar el producto.');
        }
    }
}