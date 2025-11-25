<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\DetalleFactura;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Tienda;
use App\Models\User;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FacturaController extends Controller
{
    /**
     * Listar facturas con filtros
     */
    public function index(Request $request)
    {
        $query = Factura::with(['cliente', 'tienda', 'empleado']);

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por fechas
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_emision', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_emision', '<=', $request->fecha_hasta);
        }

        // Filtro por tienda
        if ($request->filled('tienda_id')) {
            $query->where('tienda_id', $request->tienda_id);
        }

        // Búsqueda por correlativo
        if ($request->filled('buscar')) {
            $query->where('correlativo', 'like', '%' . $request->buscar . '%');
        }

        $facturas = $query->orderBy('fecha_emision', 'desc')->paginate(15);
        $tiendas = Tienda::all();

        return view('facturas.index', compact('facturas', 'tiendas'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $tiendas = Tienda::all();
        $clientes = User::where('rol', 'cliente')->orderBy('name')->get();
        $productos = Producto::where('activo', true)
            ->with('categoria')
            ->orderBy('nombre')
            ->get();

        $siguienteCorrelativo = Factura::siguienteCorrelativo();

        return view('facturas.create', compact('tiendas', 'clientes', 'productos', 'siguienteCorrelativo'));
    }

    /**
     * Almacenar nueva factura
     */
    public function store(Request $request)
    {
        // ============================================
        // VALIDACIÓN DE DATOS
        // ============================================
        $validated = $request->validate([
            'tienda_id' => 'required|exists:tiendas,id',
            'cliente_id' => 'nullable|exists:users,id',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
            'pago_efectivo' => 'nullable|numeric|min:0',
            'pago_cheque' => 'nullable|numeric|min:0',
            'pago_tarjeta' => 'nullable|numeric|min:0',
            'numero_cheque' => 'nullable|string|max:50',
            'referencia_tarjeta' => 'nullable|string|max:50',
            'notas' => 'nullable|string|max:500'
        ]);

        // ============================================
        // CALCULAR TOTALES
        // ============================================
        $subtotal = 0;
        $descuento = 0;

        foreach ($validated['productos'] as $prod) {
            $producto = Producto::find($prod['producto_id']);
            $precioOriginal = $producto->precio * $prod['cantidad'];
            $descuentoProducto = $precioOriginal * ($producto->descuento / 100);
            
            $subtotal += $precioOriginal;
            $descuento += $descuentoProducto;
        }

        $total = $subtotal - $descuento;

        // ============================================
        // VALIDAR MEDIOS DE PAGO
        // ============================================
        $pagoEfectivo = floatval($request->pago_efectivo ?? 0);
        $pagoCheque = floatval($request->pago_cheque ?? 0);
        $pagoTarjeta = floatval($request->pago_tarjeta ?? 0);
        $totalPagos = $pagoEfectivo + $pagoCheque + $pagoTarjeta;

        if (abs($totalPagos - $total) > 0.01) {
            return back()
                ->withInput()
                ->withErrors(['pagos' => 'El total de pagos debe ser igual al total de la factura.']);
        }

        // ============================================
        // VALIDAR STOCK DISPONIBLE
        // ============================================
        foreach ($validated['productos'] as $prod) {
            $inventario = Inventario::where('producto_id', $prod['producto_id'])
                ->where('tienda_id', $validated['tienda_id'])
                ->first();

            if (!$inventario) {
                $producto = Producto::find($prod['producto_id']);
                return back()
                    ->withInput()
                    ->withErrors(['productos' => "El producto '{$producto->nombre}' no tiene inventario en esta tienda."]);
            }

            if ($inventario->existencia < $prod['cantidad']) {
                $producto = Producto::find($prod['producto_id']);
                return back()
                    ->withInput()
                    ->withErrors(['productos' => "Stock insuficiente del producto '{$producto->nombre}'. Disponible: {$inventario->existencia}"]);
            }
        }

        // ============================================
        // CREAR FACTURA CON TRANSACCIÓN
        // ============================================
        DB::beginTransaction();
        try {
            // Crear factura
            $factura = Factura::create([
                'cliente_id' => $validated['cliente_id'],
                'tienda_id' => $validated['tienda_id'],
                'empleado_id' => Auth::id(),
                'correlativo' => Factura::siguienteCorrelativo(),
                'serie' => 'A',
                'fecha_emision' => now(),
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $total,
                'estado' => 'pagada',
                'notas' => $validated['notas']
            ]);

            // Crear detalles y descontar inventario
            foreach ($validated['productos'] as $prod) {
                $producto = Producto::find($prod['producto_id']);
                $precioUnitario = $producto->precio * (1 - $producto->descuento / 100);

                // Crear detalle
                DetalleFactura::create([
                    'factura_id' => $factura->id,
                    'producto_id' => $prod['producto_id'],
                    'cantidad' => $prod['cantidad'],
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $precioUnitario * $prod['cantidad']
                ]);

                // Descontar inventario
                $inventario = Inventario::where('producto_id', $prod['producto_id'])
                    ->where('tienda_id', $validated['tienda_id'])
                    ->first();
                
                $inventario->decrement('existencia', $prod['cantidad']);
            }

            // Crear registros de pagos
            if ($pagoEfectivo > 0) {
                Pago::create([
                    'factura_id' => $factura->id,
                    'tipo_pago' => 'efectivo',
                    'monto' => $pagoEfectivo
                ]);
            }

            if ($pagoCheque > 0) {
                Pago::create([
                    'factura_id' => $factura->id,
                    'tipo_pago' => 'cheque',
                    'monto' => $pagoCheque,
                    'referencia' => $request->numero_cheque
                ]);
            }

            if ($pagoTarjeta > 0) {
                Pago::create([
                    'factura_id' => $factura->id,
                    'tipo_pago' => 'tarjeta',
                    'monto' => $pagoTarjeta,
                    'referencia' => $request->referencia_tarjeta
                ]);
            }

            DB::commit();

            return redirect()
                ->route('facturas.show', $factura)
                ->with('success', 'Factura creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear factura: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear la factura. Por favor intente nuevamente.']);
        }
    }

    /**
     * Mostrar detalle de factura
     */
    public function show(Factura $factura)
    {
        $factura->load(['cliente', 'tienda', 'empleado', 'detalles.producto', 'pagos']);
        return view('facturas.show', compact('factura'));
    }

    /**
     * Anular factura
     */
    public function anular(Factura $factura)
    {
        if ($factura->anular()) {
            return redirect()
                ->route('facturas.show', $factura)
                ->with('success', 'Factura anulada exitosamente.');
        }

        return back()->withErrors(['error' => 'No se pudo anular la factura.']);
    }

    /**
     * Eliminar factura (solo admin)
     */
    public function destroy(Factura $factura)
    {
        if ($factura->estado !== 'anulada') {
            return back()->withErrors(['error' => 'Solo se pueden eliminar facturas anuladas.']);
        }

        $factura->delete();
        
        return redirect()
            ->route('facturas.index')
            ->with('success', 'Factura eliminada exitosamente.');
    }
}
