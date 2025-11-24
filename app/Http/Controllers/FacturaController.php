<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\DetalleFactura;
use App\Models\PagoFactura;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Tienda;
use App\Models\Inventario;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacturaController extends Controller
{
    /**
     * Mostrar lista de facturas
     */
    public function index(Request $request)
    {
        $query = Factura::with(['cliente', 'tienda', 'user']);
        
        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_emision', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_emision', '<=', $request->fecha_hasta);
        }
        
        if ($request->filled('numero_factura')) {
            $query->where('numero_factura', 'like', '%' . $request->numero_factura . '%');
        }
        
        $facturas = $query->orderByDesc('fecha_emision')->paginate(20);
        
        return view('facturas.index', compact('facturas'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $clientes = Cliente::activos()->orderBy('nombre')->get();
        $tiendas = Tienda::activas()->orderBy('nombre')->get();
        
        // Obtener siguiente número de factura
        $serie = Configuracion::get('serie_factura', 'A');
        $correlativo = (int) Configuracion::get('correlativo_factura', 1000) + 1;
        $numeroFactura = $serie . '-' . str_pad($correlativo, 6, '0', STR_PAD_LEFT);
        
        return view('facturas.create', compact('clientes', 'tiendas', 'numeroFactura', 'serie', 'correlativo'));
    }

    /**
     * Guardar nueva factura
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'tienda_id' => 'required|exists:tiendas,id',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0',
            'productos.*.descuento' => 'nullable|numeric|min:0',
            'pagos' => 'required|array|min:1',
            'pagos.*.tipo' => 'required|in:efectivo,cheque,tarjeta',
            'pagos.*.monto' => 'required|numeric|min:0',
            'pagos.*.referencia' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Generar número de factura
            $serie = Configuracion::get('serie_factura', 'A');
            $correlativo = (int) Configuracion::get('correlativo_factura', 1000) + 1;
            $numeroFactura = $serie . '-' . str_pad($correlativo, 6, '0', STR_PAD_LEFT);
            
            // Calcular totales
            $subtotal = 0;
            $descuentoTotal = 0;
            
            foreach ($request->productos as $prod) {
                $subtotalProducto = $prod['cantidad'] * $prod['precio'];
                $descuentoProducto = $prod['descuento'] ?? 0;
                $subtotal += $subtotalProducto;
                $descuentoTotal += $descuentoProducto;
            }
            
            $total = $subtotal - $descuentoTotal;
            
            // Verificar que el total de pagos coincida
            $totalPagos = collect($request->pagos)->sum('monto');
            if (abs($totalPagos - $total) > 0.01) {
                throw new \Exception('El total de los pagos no coincide con el total de la factura');
            }
            
            // Crear factura
            $factura = Factura::create([
                'numero_factura' => $numeroFactura,
                'serie' => $serie,
                'correlativo' => $correlativo,
                'fecha_emision' => now(),
                'cliente_id' => $request->cliente_id,
                'tienda_id' => $request->tienda_id,
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'descuento' => $descuentoTotal,
                'total' => $total,
                'estado' => 'pagada',
            ]);
            
            // Crear detalles y actualizar inventario
            foreach ($request->productos as $prod) {
                $producto = Producto::findOrFail($prod['id']);
                $subtotalProducto = $prod['cantidad'] * $prod['precio'];
                $descuentoProducto = $prod['descuento'] ?? 0;
                
                // Crear detalle de factura
                DetalleFactura::create([
                    'factura_id' => $factura->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $prod['cantidad'],
                    'precio_unitario' => $prod['precio'],
                    'descuento' => $descuentoProducto,
                    'subtotal' => $subtotalProducto - $descuentoProducto,
                ]);
                
                // Actualizar inventario
                $inventario = Inventario::where('producto_id', $producto->id)
                    ->where('tienda_id', $request->tienda_id)
                    ->lockForUpdate()
                    ->first();
                
                if (!$inventario || $inventario->existencia < $prod['cantidad']) {
                    throw new \Exception("No hay suficiente stock de: {$producto->nombre}");
                }
                
                $inventario->decrement('existencia', $prod['cantidad']);
                $inventario->update(['ultima_actualizacion' => now()]);
            }
            
            // Crear pagos
            foreach ($request->pagos as $pago) {
                PagoFactura::create([
                    'factura_id' => $factura->id,
                    'tipo_pago' => $pago['tipo'],
                    'monto' => $pago['monto'],
                    'referencia' => $pago['referencia'] ?? null,
                ]);
            }
            
            // Actualizar correlativo
            Configuracion::set('correlativo_factura', $correlativo);
            
            DB::commit();
            
            return redirect()->route('facturas.show', $factura->id)
                ->with('success', 'Factura creada exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la factura: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalle de factura
     */
    public function show(Factura $factura)
    {
        $factura->load(['cliente', 'tienda', 'user', 'detalles.producto', 'pagos']);
        return view('facturas.show', compact('factura'));
    }

    /**
     * Anular factura
     */
    public function destroy(Factura $factura)
    {
        if ($factura->estado === 'anulada') {
            return back()->with('error', 'La factura ya está anulada');
        }

        DB::beginTransaction();
        try {
            // Devolver productos al inventario
            foreach ($factura->detalles as $detalle) {
                $inventario = Inventario::where('producto_id', $detalle->producto_id)
                    ->where('tienda_id', $factura->tienda_id)
                    ->lockForUpdate()
                    ->first();
                
                if ($inventario) {
                    $inventario->increment('existencia', $detalle->cantidad);
                    $inventario->update(['ultima_actualizacion' => now()]);
                }
            }
            
            // Anular factura
            $factura->update(['estado' => 'anulada']);
            
            DB::commit();
            
            return redirect()->route('facturas.index')
                ->with('success', 'Factura anulada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al anular la factura: ' . $e->getMessage());
        }
    }

    /**
     * Buscar productos para agregar a factura (AJAX)
     */
    public function buscarProductos(Request $request)
    {
        $query = $request->get('q', '');
        $tiendaId = $request->get('tienda_id');
        
        $productos = Producto::with(['categoria', 'inventarios' => function($q) use ($tiendaId) {
                $q->where('tienda_id', $tiendaId);
            }])
            ->where('activo', true)
            ->where(function($q) use ($query) {
                $q->where('nombre', 'like', "%{$query}%")
                  ->orWhere('codigo', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();
        
        return response()->json($productos);
    }
}