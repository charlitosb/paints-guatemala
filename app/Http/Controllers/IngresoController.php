<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\DetalleIngreso;
use App\Models\Proveedor;
use App\Models\Tienda;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngresoController extends Controller
{
    /**
     * Mostrar listado de ingresos
     */
    public function index(Request $request)
    {
        $query = Ingreso::with(['proveedor', 'tienda', 'usuario']);

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tienda_id')) {
            $query->where('tienda_id', $request->tienda_id);
        }

        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_ingreso', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_ingreso', '<=', $request->fecha_hasta);
        }

        $ingresos = $query->orderByDesc('fecha_ingreso')->paginate(15);

        // Contadores
        $pendientes = Ingreso::pendientes()->count();
        $recibidas = Ingreso::recibidas()->count();
        $canceladas = Ingreso::canceladas()->count();

        // Datos para filtros
        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();
        $tiendas = Tienda::where('activo', true)->orderBy('nombre')->get();

        return view('ingresos.index', compact('ingresos', 'pendientes', 'recibidas', 'canceladas', 'proveedores', 'tiendas'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();
        $tiendas = Tienda::where('activo', true)->orderBy('nombre')->get();
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        $siguienteNumero = Ingreso::siguienteNumeroIngreso();

        return view('ingresos.create', compact('proveedores', 'tiendas', 'productos', 'siguienteNumero'));
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
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_compra' => 'required|numeric|min:0',
        ], [
            'proveedor_id.required' => 'Debe seleccionar un proveedor',
            'tienda_id.required' => 'Debe seleccionar una tienda',
            'productos.required' => 'Debe agregar al menos un producto',
            'productos.min' => 'Debe agregar al menos un producto',
            'productos.*.cantidad.min' => 'La cantidad mínima es 1',
            'productos.*.precio_compra.min' => 'El precio mínimo es 0',
        ]);

        DB::beginTransaction();
        try {
            // Calcular total
            $total = 0;
            foreach ($request->productos as $prod) {
                $total += $prod['cantidad'] * $prod['precio_compra'];
            }

            // Crear ingreso
            $ingreso = Ingreso::create([
                'proveedor_id' => $request->proveedor_id,
                'tienda_id' => $request->tienda_id,
                'usuario_id' => auth()->id(),
                'numero_ingreso' => Ingreso::siguienteNumeroIngreso(),
                'fecha_ingreso' => $request->fecha_ingreso,
                'total' => $total,
                'estado' => 'pendiente',
                'notas' => $request->notas,
            ]);

            // Crear detalles
            foreach ($request->productos as $prod) {
                DetalleIngreso::create([
                    'ingreso_id' => $ingreso->id,
                    'producto_id' => $prod['producto_id'],
                    'cantidad' => $prod['cantidad'],
                    'precio_compra' => $prod['precio_compra'],
                ]);
            }

            DB::commit();

            return redirect()->route('ingresos.show', $ingreso)
                ->with('success', 'Ingreso creado exitosamente. Estado: PENDIENTE');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el ingreso: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Mostrar detalle del ingreso
     */
    public function show(Ingreso $ingreso)
    {
        $ingreso->load(['proveedor', 'tienda', 'usuario', 'detalles.producto']);
        return view('ingresos.show', compact('ingreso'));
    }

    /**
     * Mostrar formulario de edición (solo para ingresos pendientes)
     */
    public function edit(Ingreso $ingreso)
    {
        if ($ingreso->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden editar ingresos pendientes');
        }

        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();
        $tiendas = Tienda::where('activo', true)->orderBy('nombre')->get();
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();

        return view('ingresos.edit', compact('ingreso', 'proveedores', 'tiendas', 'productos'));
    }

    /**
     * Actualizar ingreso (solo para ingresos pendientes)
     */
    public function update(Request $request, Ingreso $ingreso)
    {
        if ($ingreso->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden editar ingresos pendientes');
        }

        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'tienda_id' => 'required|exists:tiendas,id',
            'fecha_ingreso' => 'required|date',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_compra' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Calcular total
            $total = 0;
            foreach ($request->productos as $prod) {
                $total += $prod['cantidad'] * $prod['precio_compra'];
            }

            // Actualizar ingreso
            $ingreso->update([
                'proveedor_id' => $request->proveedor_id,
                'tienda_id' => $request->tienda_id,
                'fecha_ingreso' => $request->fecha_ingreso,
                'total' => $total,
                'notas' => $request->notas,
            ]);

            // Eliminar detalles anteriores y crear nuevos
            $ingreso->detalles()->delete();

            foreach ($request->productos as $prod) {
                DetalleIngreso::create([
                    'ingreso_id' => $ingreso->id,
                    'producto_id' => $prod['producto_id'],
                    'cantidad' => $prod['cantidad'],
                    'precio_compra' => $prod['precio_compra'],
                ]);
            }

            DB::commit();

            return redirect()->route('ingresos.show', $ingreso)
                ->with('success', 'Ingreso actualizado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el ingreso: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Confirmar recepción del ingreso
     */
    public function confirmar(Ingreso $ingreso)
    {
        if (!$ingreso->puedeConfirmarse()) {
            return back()->with('error', 'Este ingreso no puede ser confirmado');
        }

        if ($ingreso->confirmarRecepcion()) {
            return redirect()->route('ingresos.show', $ingreso)
                ->with('success', 'Ingreso confirmado exitosamente. El inventario ha sido actualizado.');
        }

        return back()->with('error', 'Error al confirmar el ingreso');
    }

    /**
     * Cancelar el ingreso
     */
    public function cancelar(Ingreso $ingreso)
    {
        if (!$ingreso->puedeCancelarse()) {
            return back()->with('error', 'Este ingreso no puede ser cancelado');
        }

        if ($ingreso->cancelar()) {
            return redirect()->route('ingresos.show', $ingreso)
                ->with('success', 'Ingreso cancelado exitosamente.');
        }

        return back()->with('error', 'Error al cancelar el ingreso');
    }

    /**
     * Eliminar ingreso (solo cancelados y solo admin)
     */
    public function destroy(Ingreso $ingreso)
    {
        if (auth()->user()->rol !== 'admin') {
            return back()->with('error', 'No tienes permisos para eliminar ingresos');
        }

        if ($ingreso->estado !== 'cancelada') {
            return back()->with('error', 'Solo se pueden eliminar ingresos cancelados');
        }

        $ingreso->detalles()->delete();
        $ingreso->delete();

        return redirect()->route('ingresos.index')
            ->with('success', 'Ingreso eliminado exitosamente');
    }
}
