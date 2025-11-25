<?php

namespace App\Http\Controllers;

use App\Models\Tienda;
use Illuminate\Http\Request;

class TiendaController extends Controller
{
    /**
     * Mostrar listado de tiendas
     */
    public function index()
    {
        // Removido withCount(['users']) porque users no tiene tienda_id
        $tiendas = Tienda::withCount(['inventarios'])
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
        $request->validate([
            'nombre' => 'required|string|max:255|unique:tiendas',
            'direccion' => 'required|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
        ], [
            'nombre.required' => 'El nombre de la tienda es obligatorio',
            'nombre.unique' => 'Ya existe una tienda con este nombre',
            'direccion.required' => 'La dirección es obligatoria',
            'latitud.between' => 'La latitud debe estar entre -90 y 90',
            'longitud.between' => 'La longitud debe estar entre -180 y 180',
        ]);

        Tienda::create([
            'nombre' => $request->nombre,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'latitud' => $request->latitud,
            'longitud' => $request->longitud,
            'activo' => true,
        ]);

        return redirect()->route('tiendas.index')
            ->with('success', 'Tienda creada exitosamente');
    }

    /**
     * Mostrar detalle de tienda
     */
    public function show(Tienda $tienda)
    {
        // Cargar inventarios con productos
        $tienda->load(['inventarios.producto']);
        
        // Estadísticas de la tienda
        $estadisticas = [
            'total_productos' => $tienda->inventarios->count(),
            'productos_bajo_stock' => $tienda->inventarios->filter(function($inv) {
                return $inv->existencia <= $inv->existencia_minima;
            })->count(),
            'productos_sin_stock' => $tienda->inventarios->where('existencia', 0)->count(),
            'valor_inventario' => $tienda->inventarios->sum(function($inv) {
                return $inv->existencia * ($inv->producto->precio_venta ?? 0);
            }),
        ];

        return view('tiendas.show', compact('tienda', 'estadisticas'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Tienda $tienda)
    {
        return view('tiendas.edit', compact('tienda'));
    }

    /**
     * Actualizar tienda
     */
    public function update(Request $request, Tienda $tienda)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:tiendas,nombre,' . $tienda->id,
            'direccion' => 'required|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'activo' => 'boolean',
        ]);

        $tienda->update([
            'nombre' => $request->nombre,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'latitud' => $request->latitud,
            'longitud' => $request->longitud,
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('tiendas.index')
            ->with('success', 'Tienda actualizada exitosamente');
    }

    /**
     * Eliminar tienda
     */
    public function destroy(Tienda $tienda)
    {
        // Verificar si tiene inventarios
        if ($tienda->inventarios()->count() > 0) {
            return back()->with('error', 'No se puede eliminar la tienda porque tiene inventarios asociados');
        }

        // Verificar si tiene ingresos
        if ($tienda->ingresos()->count() > 0) {
            return back()->with('error', 'No se puede eliminar la tienda porque tiene ingresos asociados');
        }

        // Verificar si tiene facturas
        if ($tienda->facturas()->count() > 0) {
            return back()->with('error', 'No se puede eliminar la tienda porque tiene facturas asociadas');
        }

        $tienda->delete();

        return redirect()->route('tiendas.index')
            ->with('success', 'Tienda eliminada exitosamente');
    }

    /**
     * Activar/Desactivar tienda
     */
    public function toggleActivo(Tienda $tienda)
    {
        $tienda->update(['activo' => !$tienda->activo]);

        $mensaje = $tienda->activo ? 'Tienda activada' : 'Tienda desactivada';

        return back()->with('success', $mensaje);
    }
}