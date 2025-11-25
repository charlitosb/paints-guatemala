<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\User;
use App\Models\Inventario;
use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Mostrar el dashboard principal
     */
    public function index()
    {
        $user = auth()->user();
        
        // Estadísticas generales
        $totalProductos = Producto::count();
        
        // Total de clientes (usuarios con rol 'cliente')
        $totalClientes = User::where('rol', 'cliente')->count();
        
        // Productos con bajo stock
        $bajosStock = Inventario::whereHas('producto', function($query) {
            $query->whereRaw('inventarios.existencia <= productos.existencia_minima');
        })->count();
        
        // Ventas del mes actual
        $ventasMesActual = Factura::whereMonth('fecha_emision', date('m'))
            ->whereYear('fecha_emision', date('Y'))
            ->where('estado', 'pagada')
            ->sum('total');

        // Productos más vendidos (top 5)
        $productosMasVendidos = DB::table('detalle_facturas')
            ->join('productos', 'detalle_facturas.producto_id', '=', 'productos.id')
            ->join('facturas', 'detalle_facturas.factura_id', '=', 'facturas.id')
            ->where('facturas.estado', 'pagada')
            ->select('productos.nombre', DB::raw('SUM(detalle_facturas.cantidad) as total_vendido'))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get();

        // Últimas facturas
        $ultimasFacturas = Factura::with(['cliente', 'tienda', 'empleado'])
            ->orderByDesc('fecha_emision')
            ->limit(10)
            ->get();
        
        return view('dashboard', compact(
            'totalProductos',
            'totalClientes',
            'bajosStock',
            'ventasMesActual',
            'productosMasVendidos',
            'ultimasFacturas'
        ));
    }
}