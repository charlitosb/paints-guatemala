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
            'nombre' => 'required|max:150',
            'empresa' => 'nullable|max:150',
            'telefono' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|max:255',
            'nit' => 'nullable|max:20',
            'activo' => 'boolean',
        ]);

        Proveedor::create($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado exitosamente');
    }

    /**
     * Mostrar un proveedor específico
     */
    public function show(Proveedor $proveedor)
    {
        $proveedor->load('ingresosProductos.tienda');
        return view('proveedores.show', compact('proveedor'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    /**
     * Actualizar proveedor
     */
    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:150',
            'empresa' => 'nullable|max:150',
            'telefono' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|max:255',
            'nit' => 'nullable|max:20',
            'activo' => 'boolean',
        ]);

        $proveedor->update($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado exitosamente');
    }

    /**
     * Eliminar proveedor
     */
    public function destroy(Proveedor $proveedor)
    {
        try {
            $proveedor->delete();
            return redirect()->route('proveedores.index')
                ->with('success', 'Proveedor eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('proveedores.index')
                ->with('error', 'No se puede eliminar el proveedor porque tiene registros asociados');
        }
    }
}