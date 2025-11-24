<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Tienda;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    /**
     * Mostrar lista de productos
     */
    public function index(Request $request)
    {
        $query = Producto::with('categoria');
        
        // Filtros
        if ($request->filled('tipo_producto')) {
            $query->where('tipo_producto', $request->tipo_producto);
        }
        
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }
        
        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('codigo', 'like', '%' . $request->buscar . '%')
                  ->orWhere('marca', 'like', '%' . $request->buscar . '%');
            });
        }
        
        $productos = $query->orderBy('nombre')->paginate(20);
        $categorias = Categoria::orderBy('nombre')->get();
        
        return view('productos.index', compact('productos', 'categorias'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('productos.create', compact('categorias'));
    }

    /**
     * Guardar nuevo producto
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|max:50|unique:productos,codigo',
            'nombre' => 'required|max:200',
            'descripcion' => 'nullable',
            'categoria_id' => 'required|exists:categorias,id',
            'tipo_producto' => 'required|in:accesorio,solvente,pintura,barniz',
            'marca' => 'nullable|max:100',
            'precio_venta' => 'required|numeric|min:0',
            'porcentaje_descuento' => 'nullable|numeric|min:0|max:100',
            'existencia_minima' => 'required|integer|min:0',
            
            // Campos condicionales según tipo de producto
            'tamano' => 'nullable|max:50',
            'unidad_medida' => 'nullable|max:20',
            'medida_volumen' => 'nullable|max:20',
            'color' => 'nullable|max:50',
            'base_pintura' => 'nullable|in:agua,aceite',
            'duracion_anos' => 'nullable|integer|min:0',
            'cobertura_m2' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Crear producto
            $producto = Producto::create($validated);
            
            // Crear inventario inicial en todas las tiendas con existencia 0
            $tiendas = Tienda::all();
            foreach ($tiendas as $tienda) {
                Inventario::create([
                    'producto_id' => $producto->id,
                    'tienda_id' => $tienda->id,
                    'existencia' => 0,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('productos.index')
                ->with('success', 'Producto creado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el producto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar un producto específico
     */
    public function show(Producto $producto)
    {
        $producto->load(['categoria', 'inventarios.tienda']);
        return view('productos.show', compact('producto'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    /**
     * Actualizar producto
     */
    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'codigo' => 'required|max:50|unique:productos,codigo,' . $producto->id,
            'nombre' => 'required|max:200',
            'descripcion' => 'nullable',
            'categoria_id' => 'required|exists:categorias,id',
            'tipo_producto' => 'required|in:accesorio,solvente,pintura,barniz',
            'marca' => 'nullable|max:100',
            'precio_venta' => 'required|numeric|min:0',
            'porcentaje_descuento' => 'nullable|numeric|min:0|max:100',
            'existencia_minima' => 'required|integer|min:0',
            
            // Campos condicionales
            'tamano' => 'nullable|max:50',
            'unidad_medida' => 'nullable|max:20',
            'medida_volumen' => 'nullable|max:20',
            'color' => 'nullable|max:50',
            'base_pintura' => 'nullable|in:agua,aceite',
            'duracion_anos' => 'nullable|integer|min:0',
            'cobertura_m2' => 'nullable|numeric|min:0',
            'activo' => 'boolean',
        ]);

        $producto->update($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente');
    }

    /**
     * Eliminar producto
     */
    public function destroy(Producto $producto)
    {
        try {
            $producto->delete();
            return redirect()->route('productos.index')
                ->with('success', 'Producto eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('productos.index')
                ->with('error', 'No se puede eliminar el producto porque tiene registros asociados');
        }
    }
}