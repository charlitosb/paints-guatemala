<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Tienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CotizacionController extends Controller
{
    /**
     * Mostrar lista de cotizaciones
     */
    public function index(Request $request)
    {
        $query = Cotizacion::with(['cliente', 'tienda']);
        
        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_cotizacion', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_cotizacion', '<=', $request->fecha_hasta);
        }
        
        $cotizaciones = $query->orderByDesc('fecha_cotizacion')->paginate(20);
        
        return view('cotizaciones.index', compact('cotizaciones'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $clientes = Cliente::activos()->orderBy('nombre')->get();
        $tiendas = Tienda::activas()->orderBy('nombre')->get();
        
        // Generar número de cotización
        $ultimaCotizacion = Cotizacion::latest('id')->first();
        $correlativo = $ultimaCotizacion ? ($ultimaCotizacion->id + 1) : 1;
        $numeroCotizacion = 'COT-' . date('Ymd') . '-' . str_pad($correlativo, 4, '0', STR_PAD_LEFT);
        
        return view('cotizaciones.create', compact('clientes', 'tiendas', 'numeroCotizacion'));
    }

    /**
     * Guardar nueva cotización
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'nombre_cliente' => 'required|string|max:150',
            'email_cliente' => 'nullable|email|max:100',
            'tienda_id' => 'required|exists:tiendas,id',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0',
            'productos.*.descuento' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Generar número de cotización
            $ultimaCotizacion = Cotizacion::latest('id')->first();
            $correlativo = $ultimaCotizacion ? ($ultimaCotizacion->id + 1) : 1;
            $numeroCotizacion = 'COT-' . date('Ymd') . '-' . str_pad($correlativo, 4, '0', STR_PAD_LEFT);
            
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
            
            // Crear cotización
            $cotizacion = Cotizacion::create([
                'numero_cotizacion' => $numeroCotizacion,
                'fecha_cotizacion' => now(),
                'cliente_id' => $request->cliente_id,
                'nombre_cliente' => $request->nombre_cliente,
                'email_cliente' => $request->email_cliente,
                'tienda_id' => $request->tienda_id,
                'subtotal' => $subtotal,
                'descuento' => $descuentoTotal,
                'total' => $total,
                'estado' => 'pendiente',
            ]);
            
            // Crear detalles
            foreach ($request->productos as $prod) {
                $producto = Producto::findOrFail($prod['id']);
                $subtotalProducto = $prod['cantidad'] * $prod['precio'];
                $descuentoProducto = $prod['descuento'] ?? 0;
                
                DetalleCotizacion::create([
                    'cotizacion_id' => $cotizacion->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $prod['cantidad'],
                    'precio_unitario' => $prod['precio'],
                    'descuento' => $descuentoProducto,
                    'subtotal' => $subtotalProducto - $descuentoProducto,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('cotizaciones.show', $cotizacion->id)
                ->with('success', 'Cotización creada exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la cotización: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalle de cotización
     */
    public function show(Cotizacion $cotizacion)
    {
        $cotizacion->load(['cliente', 'tienda', 'detalles.producto']);
        return view('cotizaciones.show', compact('cotizacion'));
    }

    /**
     * Generar PDF de cotización
     */
    public function generarPdf(Cotizacion $cotizacion)
    {
        $cotizacion->load(['cliente', 'tienda', 'detalles.producto.categoria']);
        
        $pdf = Pdf::loadView('cotizaciones.pdf', compact('cotizacion'));
        
        return $pdf->download('cotizacion-' . $cotizacion->numero_cotizacion . '.pdf');
    }

    /**
     * Cambiar estado de cotización
     */
    public function cambiarEstado(Request $request, Cotizacion $cotizacion)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,aprobada,rechazada,facturada',
        ]);

        $cotizacion->update(['estado' => $request->estado]);

        return back()->with('success', 'Estado actualizado exitosamente');
    }
}