<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Mostrar lista de usuarios
     */
    public function index()
    {
        $usuarios = User::with('tienda')->orderBy('name')->paginate(15);
        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $tiendas = Tienda::activas()->orderBy('nombre')->get();
        return view('usuarios.create', compact('tiendas'));
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'rol' => 'required|in:admin,digitador,cajero,gerente',
            'tienda_id' => 'nullable|exists:tiendas,id',
            'activo' => 'boolean'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['activo'] = $request->has('activo') ? 1 : 0;

        User::create($validated);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Mostrar detalle del usuario
     */
    public function show($id)
    {
        $usuario = User::with('tienda')->findOrFail($id);
        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $usuario = User::with('tienda')->findOrFail($id);
        $tiendas = Tienda::activas()->orderBy('nombre')->get();
        return view('usuarios.edit', compact('usuario', 'tiendas'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'rol' => 'required|in:admin,digitador,cajero,gerente',
            'tienda_id' => 'nullable|exists:tiendas,id',
            'activo' => 'boolean'
        ]);

        // Solo actualizar password si se proporcionó uno nuevo
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['activo'] = $request->has('activo') ? 1 : 0;

        $usuario->update($validated);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Eliminar usuario
     */
    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        
        // No permitir eliminar al usuario autenticado
        if ($usuario->id == auth()->id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }
        
        try {
            $usuario->delete();
            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')
                ->with('error', 'Error al eliminar el usuario.');
        }
    }
}