<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Mostrar lista de clientes
     */
    public function index()
    {
        $clientes = Cliente::orderBy('nombre')->paginate(15);
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Guardar nuevo cliente
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'nit' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:clientes,email',
            'direccion' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        $validated['activo'] = $request->has('activo') ? 1 : 0;

        Cliente::create($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    /**
     * Mostrar detalle del cliente
     */
    public function show($id)
    {
        $cliente = Cliente::findOrFail($id);
        
        // Cargar facturas solo si existen
        $cliente->load(['facturas' => function($query) {
            $query->with('tienda')->latest()->limit(10);
        }]);
        
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Actualizar cliente
     */
    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'nit' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:clientes,email,' . $id,
            'direccion' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        $validated['activo'] = $request->has('activo') ? 1 : 0;

        $cliente->update($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    /**
     * Eliminar cliente
     */
    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);
        
        try {
            $cliente->delete();
            return redirect()->route('clientes.index')
                ->with('success', 'Cliente eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('clientes.index')
                ->with('error', 'No se puede eliminar el cliente porque tiene registros relacionados.');
        }
    }
}