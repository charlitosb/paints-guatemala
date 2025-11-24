<?php

namespace App\Http\Controllers;

use App\Models\Tienda;
use Illuminate\Http\Request;

class TiendaController extends Controller
{
    public function index()
    {
        $tiendas = Tienda::withCount(['users', 'inventarios', 'facturas'])
            ->orderBy('nombre')
            ->paginate(10);
        
        return view('tiendas.index', compact('tiendas'));
    }

    public function create()
    {
        return view('tiendas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:150',
            'direccion' => 'required|max:255',
            'telefono' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'activo' => 'boolean',
        ]);

        Tienda::create($validated);

        return redirect()->route('tiendas.index')
            ->with('success', 'Tienda creada exitosamente');
    }

    public function show(Tienda $tienda)
    {
        $tienda->load(['users', 'inventarios.producto']);
        return view('tiendas.show', compact('tienda'));
    }

    public function edit(Tienda $tienda)
    {
        return view('tiendas.edit', compact('tienda'));
    }

    public function update(Request $request, Tienda $tienda)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:150',
            'direccion' => 'required|max:255',
            'telefono' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'activo' => 'boolean',
        ]);

        $tienda->update($validated);

        return redirect()->route('tiendas.index')
            ->with('success', 'Tienda actualizada exitosamente');
    }

    public function destroy(Tienda $tienda)
    {
        try {
            $tienda->delete();
            return redirect()->route('tiendas.index')
                ->with('success', 'Tienda eliminada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar la tienda porque tiene registros asociados');
        }
    }
}