<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Producto;
use App\Models\Inventario;
use App\Models\IngresoProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Página principal de reportes
     */
    public function index()
    {
        return view('reportes.index');
    }

    /**
     * Reporte de ventas por fecha
     */
    public function ventas(Request $request)
    {
        $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
        ]);

        $facturas = Factura::with(['detalles.producto', 'pagos'])
            ->whereBetween('fecha_emision', [$request->fecha_desde, $request->fecha_hasta])
            ->where('estado', 'pagada')
            ->get();

        // Calcular totales por tipo de pago
        $totalEfectivo = $facturas->flatMap->pagos->where('tipo_pago', 'efectivo')->sum('monto');
        $totalCheque = $facturas->flatMap->pagos->where('tipo_pago', 'cheque')->sum('monto');
        $totalTarjeta = $facturas->flatMap->pagos->where('tipo_pago', 'tarjeta')->sum('monto');
        $totalGeneral = $facturas->sum('total');

        return view('reportes.ventas', compact(
            'facturas',
            'totalEfectivo',
            'totalCheque',
            'totalTarjeta',
            'totalGeneral'
        ));
    }

    /**
     * Productos más vendidos
     */
    public function productosMasVendidos(Request $request)
    {
        $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
            'tipo' => 'required|in:dinero,cantidad',
        ]);

        if ($request->tipo === 'dinero') {
            // Por dinero generado
            $productos = DB::table('detalle_facturas')
                ->join('facturas', 'detalle_facturas.factura_id', '=', 'facturas.id')
                ->join('productos', 'detalle_facturas.producto_id', '=', 'productos.id')
                ->whereBetween('facturas.fecha_emision', [$request->fecha_desde, $request->fecha_hasta])
                ->where('facturas.estado', 'pagada')
                ->select(
                    'productos.nombre',
                    'productos.codigo',
                    DB::raw('SUM(detalle_facturas.subtotal) as total_vendido')
                )
                ->groupBy('productos.id', 'productos.nombre', 'productos.codigo')
                ->orderByDesc('total_vendido')
                ->limit(20)
                ->get();
        } else {
            // Por cantidad vendida
            $productos = DB::table('detalle_facturas')
                ->join('facturas', 'detalle_facturas.factura_id', '=', 'facturas.id')
                ->join('productos', 'detalle_facturas.producto_id', '=', 'productos.id')
                ->whereBetween('facturas.fecha_emision', [$request->fecha_desde, $request->fecha_hasta])
                ->where('facturas.estado', 'pagada')
                ->select(
                    'productos.nombre',
                    'productos.codigo',
                    'productos.unidad_medida',
                    'productos.medida_volumen',
                    DB::raw('SUM(detalle_facturas.cantidad) as cantidad_vendida')
                )
                ->groupBy('productos.id', 'productos.nombre', 'productos.codigo', 'productos.unidad_medida', 'productos.medida_volumen')
                ->orderByDesc('cantidad_vendida')
                ->limit(20)
                ->get();
        }

        return view('reportes.productos-mas-vendidos', compact('productos'));
    }

    /**
     * Reporte de inventario general
     */
    public function inventario()
    {
        $inventarios = Inventario::with(['producto.categoria', 'tienda'])
            ->orderBy('tienda_id')
            ->orderBy('producto_id')
            ->get();

        $resumenPorTienda = $inventarios->groupBy('tienda.nombre')
            ->map(function ($items) {
                return [
                    'total_productos' => $items->count(),
                    'valor_total' => $items->sum(function ($inv) {
                        return $inv->existencia * $inv->producto->precio_venta;
                    }),
                ];
            });

        return view('reportes.inventario', compact('inventarios', 'resumenPorTienda'));
    }

    /**
     * Productos con bajo stock
     */
    public function bajoStock()
    {
        $productosBajoStock = Inventario::with(['producto', 'tienda'])
            ->whereRaw('existencia <= (SELECT existencia_minima FROM productos WHERE productos.id = inventarios.producto_id)')
            ->orderBy('existencia', 'asc')
            ->get();

        return view('reportes.bajo-stock', compact('productosBajoStock'));
    }
}