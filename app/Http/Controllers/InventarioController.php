<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Tienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    /**
     * Mostrar inventario general
     */
    public function index(Request $request)
    {
        $query = Inventario::with(['producto.categoria', 'tienda']);
        
        // Filtros
        if ($request->filled('tienda_id')) {
            $query->where('tienda_id', $request->tienda_id);
        }
        
        if ($request->filled('bajo_stock')) {
            $query->whereRaw('existencia <= (SELECT existencia_minima FROM productos WHERE productos.id = inventarios.producto_id)');
        }
        
        if ($request->filled('sin_stock')) {
            $query->where('existencia', '<=', 0);
        }
        
        if ($request->filled('buscar')) {
            $query->whereHas('producto', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('codigo', 'like', '%' . $request->buscar . '%');
            });
        }
        
        $inventarios = $query->orderBy('tienda_id')
            ->orderBy('producto_id')
            ->paginate(50);
        
        $tiendas = Tienda::orderBy('nombre')->get();
        
        return view('inventarios.index', compact('inventarios', 'tiendas'));
    }

    /**
     * Mostrar detalle de inventario
     */
    public function show(Inventario $inventario)
    {
        $inventario->load(['producto.categoria', 'tienda']);
        
        // Historial de movimientos (últimos 20)
        $movimientos = DB::table('detalle_facturas')
            ->join('facturas', 'detalle_facturas.factura_id', '=', 'facturas.id')
            ->where('detalle_facturas.producto_id', $inventario->producto_id)
            ->where('facturas.tienda_id', $inventario->tienda_id)
            ->where('facturas.estado', 'pagada')
            ->select(
                'facturas.numero_factura',
                'facturas.fecha_emision',
                'detalle_facturas.cantidad',
                DB::raw("'venta' as tipo")
            )
            ->unionAll(
                DB::table('detalle_ingresos')
                    ->join('ingresos_productos', 'detalle_ingresos.ingreso_id', '=', 'ingresos_productos.id')
                    ->where('detalle_ingresos.producto_id', $inventario->producto_id)
                    ->where('ingresos_productos.tienda_id', $inventario->tienda_id)
                    ->select(
                        'ingresos_productos.numero_ingreso as numero_factura',
                        'ingresos_productos.fecha_ingreso as fecha_emision',
                        'detalle_ingresos.cantidad',
                        DB::raw("'ingreso' as tipo")
                    )
            )
            ->orderByDesc('fecha_emision')
            ->limit(20)
            ->get();
        
        return view('inventarios.show', compact('inventario', 'movimientos'));
    }

    /**
     * Actualizar existencia de inventario
     */
    public function update(Request $request, Inventario $inventario)
    {
        $request->validate([
            'existencia' => 'required|integer|min:0',
        ]);

        $inventario->update([
            'existencia' => $request->existencia,
            'ultima_actualizacion' => now(),
        ]);

        return redirect()->route('inventarios.index')
            ->with('success', 'Inventario actualizado exitosamente');
    }

    /**
     * Transferir productos entre tiendas
     */
    public function transferir(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'tienda_origen_id' => 'required|exists:tiendas,id',
            'tienda_destino_id' => 'required|exists:tiendas,id|different:tienda_origen_id',
            'cantidad' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Inventario origen
            $inventarioOrigen = Inventario::where('producto_id', $request->producto_id)
                ->where('tienda_id', $request->tienda_origen_id)
                ->lockForUpdate()
                ->first();
            
            if (!$inventarioOrigen || $inventarioOrigen->existencia < $request->cantidad) {
                throw new \Exception('No hay suficiente stock en la tienda origen');
            }
            
            // Inventario destino
            $inventarioDestino = Inventario::where('producto_id', $request->producto_id)
                ->where('tienda_id', $request->tienda_destino_id)
                ->lockForUpdate()
                ->first();
            
            // Descontar de origen
            $inventarioOrigen->decrement('existencia', $request->cantidad);
            $inventarioOrigen->update(['ultima_actualizacion' => now()]);
            
            // Sumar a destino
            $inventarioDestino->increment('existencia', $request->cantidad);
            $inventarioDestino->update(['ultima_actualizacion' => now()]);
            
            DB::commit();
            
            return redirect()->route('inventarios.index')
                ->with('success', 'Transferencia realizada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error en la transferencia: ' . $e->getMessage());
        }
    }
}