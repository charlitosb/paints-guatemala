<?php

namespace App\Http\Controllers;

use App\Models\Tienda;
use Illuminate\Http\Request;

class TiendaController extends Controller
{
    /**
     * Mostrar lista de tiendas
     */
    public function index()
    {
        $tiendas = Tienda::withCount(['users', 'inventarios'])
            ->orderBy('nombre')
            ->paginate(15);
        
        return view('tiendas.index', compact('tiendas'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('tiendas.create');
    }

    /**
     * Guardar nueva tienda
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'activa' => 'boolean'
        ]);

        $validated['activa'] = $request->has('activa') ? 1 : 0;

        Tienda::create($validated);

        return redirect()->route('tiendas.index')
            ->with('success', 'Tienda creada exitosamente.');
    }

    /**
     * Mostrar detalle de la tienda
     */
    public function show($id)
    {
        $tienda = Tienda::withCount(['users', 'inventarios', 'facturas'])
            ->findOrFail($id);
        
        // Cargar usuarios de la tienda
        $tienda->load(['users' => function($query) {
            $query->where('activo', 1)->latest();
        }]);
        
        // Cargar productos con bajo stock en esta tienda
        // Cambiado: usar whereHas con join para comparar con existencia_minima de productos
        $tienda->load(['inventarios' => function($query) {
            $query->with('producto')
                ->whereHas('producto', function($q) {
                    $q->whereRaw('inventarios.existencia <= productos.existencia_minima');
                })
                ->limit(10);
        }]);
        
        return view('tiendas.show', compact('tienda'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $tienda = Tienda::findOrFail($id);
        return view('tiendas.edit', compact('tienda'));
    }

    /**
     * Actualizar tienda
     */
    public function update(Request $request, $id)
    {
        $tienda = Tienda::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'activa' => 'boolean'
        ]);

        $validated['activa'] = $request->has('activa') ? 1 : 0;

        $tienda->update($validated);

        return redirect()->route('tiendas.index')
            ->with('success', 'Tienda actualizada exitosamente.');
    }

    /**
     * Eliminar tienda
     */
    public function destroy($id)
    {
        $tienda = Tienda::findOrFail($id);
        
        // Verificar si tiene usuarios asignados
        if ($tienda->users()->count() > 0) {
            return redirect()->route('tiendas.index')
                ->with('error', 'No se puede eliminar la tienda porque tiene usuarios asignados.');
        }
        
        // Verificar si tiene inventario
        if ($tienda->inventarios()->count() > 0) {
            return redirect()->route('tiendas.index')
                ->with('error', 'No se puede eliminar la tienda porque tiene inventario registrado.');
        }
        
        try {
            $tienda->delete();
            return redirect()->route('tiendas.index')
                ->with('success', 'Tienda eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('tiendas.index')
                ->with('error', 'Error al eliminar la tienda.');
        }
    }
}