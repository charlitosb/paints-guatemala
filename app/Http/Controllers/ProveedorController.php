<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    /**
     * Mostrar lista de proveedores
     */
    public function index()
    {
        $proveedores = Proveedor::orderBy('nombre')->paginate(15);
        return view('proveedores.index', compact('proveedores'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('proveedores.create');
    }

    /**
     * Guardar nuevo proveedor
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'empresa' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        $validated['activo'] = $request->has('activo') ? 1 : 0;

        Proveedor::create($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado exitosamente.');
    }

    /**
     * Mostrar detalle del proveedor
     */
    public function show($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        
        // Cargar ingresos solo si existen
        $proveedor->load(['ingresos' => function($query) {
            $query->with('tienda')->latest()->limit(10);
        }]);
        
        return view('proveedores.show', compact('proveedor'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        return view('proveedores.edit', compact('proveedor'));
    }

    /**
     * Actualizar proveedor
     */
    public function update(Request $request, $id)
    {
        $proveedor = Proveedor::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'empresa' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        $validated['activo'] = $request->has('activo') ? 1 : 0;

        $proveedor->update($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    /**
     * Eliminar proveedor
     */
    public function destroy($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        
        try {
            $proveedor->delete();
            return redirect()->route('proveedores.index')
                ->with('success', 'Proveedor eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('proveedores.index')
                ->with('error', 'No se puede eliminar el proveedor porque tiene registros relacionados.');
        }
    }
}