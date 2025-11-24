<?php

namespace App\Http\Controllers;

use App\Models\IngresoProducto;
use App\Models\DetalleIngreso;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\Tienda;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngresoProductoController extends Controller
{
    /**
     * Mostrar lista de ingresos
     */
    public function index(Request $request)
    {
        $query = IngresoProducto::with(['proveedor', 'tienda', 'user']);
        
        // Filtros
        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }
        
        if ($request->filled('tienda_id')) {
            $query->where('tienda_id', $request->tienda_id);
        }
        
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_ingreso', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_ingreso', '<=', $request->fecha_hasta);
        }
        
        $ingresos = $query->orderByDesc('fecha_ingreso')->paginate(20);
        $proveedores = Proveedor::activos()->orderBy('nombre')->get();
        $tiendas = Tienda::activas()->orderBy('nombre')->get();
        
        return view('ingresos.index', compact('ingresos', 'proveedores', 'tiendas'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $proveedores = Proveedor::activos()->orderBy('nombre')->get();
        $tiendas = Tienda::activas()->orderBy('nombre')->get();
        $productos = Producto::activos()->orderBy('nombre')->get();
        
        // Generar número de ingreso
        $ultimoIngreso = IngresoProducto::latest('id')->first();
        $correlativo = $ultimoIngreso ? ($ultimoIngreso->id + 1) : 1;
        $numeroIngreso = 'ING-' . date('Ymd') . '-' . str_pad($correlativo, 4, '0', STR_PAD_LEFT);
        
        return view('ingresos.create', compact('proveedores', 'tiendas', 'productos', 'numeroIngreso'));
    }

    /**
     * Guardar nuevo ingreso
     */
    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'tienda_id' => 'required|exists:tiendas,id',
            'fecha_ingreso' => 'required|date',
            'observaciones' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_compra' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Generar número de ingreso
            $ultimoIngreso = IngresoProducto::latest('id')->first();
            $correlativo = $ultimoIngreso ? ($ultimoIngreso->id + 1) : 1;
            $numeroIngreso = 'ING-' . date('Ymd') . '-' . str_pad($correlativo, 4, '0', STR_PAD_LEFT);
            
            // Calcular total
            $total = 0;
            foreach ($request->productos as $prod) {
                $total += $prod['cantidad'] * $prod['precio_compra'];
            }
            
            // Crear ingreso
            $ingreso = IngresoProducto::create([
                'numero_ingreso' => $numeroIngreso,
                'fecha_ingreso' => $request->fecha_ingreso,
                'proveedor_id' => $request->proveedor_id,
                'tienda_id' => $request->tienda_id,
                'user_id' => auth()->id(),
                'total' => $total,
                'observaciones' => $request->observaciones,
            ]);
            
            // Crear detalles y actualizar inventario
            foreach ($request->productos as $prod) {
                $producto = Producto::findOrFail($prod['id']);
                $subtotal = $prod['cantidad'] * $prod['precio_compra'];
                
                // Crear detalle
                DetalleIngreso::create([
                    'ingreso_id' => $ingreso->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $prod['cantidad'],
                    'precio_compra' => $prod['precio_compra'],
                    'subtotal' => $subtotal,
                ]);
                
                // Actualizar inventario
                $inventario = Inventario::where('producto_id', $producto->id)
                    ->where('tienda_id', $request->tienda_id)
                    ->lockForUpdate()
                    ->first();
                
                if ($inventario) {
                    $inventario->increment('existencia', $prod['cantidad']);
                    $inventario->update(['ultima_actualizacion' => now()]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('ingresos.show', $ingreso->id)
                ->with('success', 'Ingreso registrado exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar el ingreso: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalle de ingreso
     */
    public function show(IngresoProducto $ingreso)
    {
        $ingreso->load(['proveedor', 'tienda', 'user', 'detalles.producto']);
        return view('ingresos.show', compact('ingreso'));
    }
}