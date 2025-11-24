<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Mostrar lista de categorías
     */
    public function index()
    {
        $categorias = Categoria::withCount('productos')->orderBy('nombre')->paginate(15);
        return view('categorias.index', compact('categorias'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('categorias.create');
    }

    /**
     * Guardar nueva categoría
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:100|unique:categorias,nombre',
            'descripcion' => 'nullable',
        ]);

        Categoria::create($validated);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría creada exitosamente');
    }

    /**
     * Mostrar una categoría específica
     */
    public function show(Categoria $categoria)
    {
        $categoria->load(['productos' => function($query) {
            $query->where('activo', true)->orderBy('nombre');
        }]);
        
        return view('categorias.show', compact('categoria'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Actualizar categoría
     */
    public function update(Request $request, Categoria $categoria)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:100|unique:categorias,nombre,' . $categoria->id,
            'descripcion' => 'nullable',
        ]);

        $categoria->update($validated);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada exitosamente');
    }

    /**
     * Eliminar categoría
     */
    public function destroy(Categoria $categoria)
    {
        if ($categoria->productos()->count() > 0) {
            return redirect()->route('categorias.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene productos asociados');
        }

        $categoria->delete();
        
        return redirect()->route('categorias.index')
            ->with('success', 'Categoría eliminada exitosamente');
    }
}