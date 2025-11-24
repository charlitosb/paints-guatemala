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
            'nombre' => 'required|max:150',
            'email' => 'nullable|email|max:100|unique:clientes,email',
            'telefono' => 'nullable|max:20',
            'direccion' => 'nullable|max:255',
            'nit' => 'nullable|max:20',
            'dpi' => 'nullable|max:20',
            'recibir_promociones' => 'boolean',
        ]);

        $validated['fecha_registro'] = now();
        $validated['activo'] = true;

        Cliente::create($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado exitosamente');
    }

    /**
     * Mostrar un cliente específico
     */
    public function show(Cliente $cliente)
    {
        $cliente->load(['facturas' => function($query) {
            $query->orderByDesc('fecha_emision')->limit(10);
        }]);
        
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Actualizar cliente
     */
    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:150',
            'email' => 'nullable|email|max:100|unique:clientes,email,' . $cliente->id,
            'telefono' => 'nullable|max:20',
            'direccion' => 'nullable|max:255',
            'nit' => 'nullable|max:20',
            'dpi' => 'nullable|max:20',
            'recibir_promociones' => 'boolean',
            'activo' => 'boolean',
        ]);

        $cliente->update($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado exitosamente');
    }

    /**
     * Eliminar cliente
     */
    public function destroy(Cliente $cliente)
    {
        try {
            $cliente->delete();
            return redirect()->route('clientes.index')
                ->with('success', 'Cliente eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('clientes.index')
                ->with('error', 'No se puede eliminar el cliente porque tiene facturas asociadas');
        }
    }
}