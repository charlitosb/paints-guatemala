<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with('tienda')->orderBy('name')->paginate(15);
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $tiendas = Tienda::activas()->orderBy('nombre')->get();
        return view('usuarios.create', compact('tiendas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:admin,digitador,cajero,gerente',
            'tienda_id' => 'nullable|exists:tiendas,id',
            'activo' => 'boolean',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'tienda_id' => $request->tienda_id,
            'activo' => $request->activo ?? true,
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado exitosamente');
    }

    public function show(User $usuario)
    {
        $usuario->load(['tienda', 'facturas', 'ingresosProductos']);
        return view('usuarios.show', compact('usuario'));
    }

    public function edit(User $usuario)
    {
        $tiendas = Tienda::activas()->orderBy('nombre')->get();
        return view('usuarios.edit', compact('usuario', 'tiendas'));
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:admin,digitador,cajero,gerente',
            'tienda_id' => 'nullable|exists:tiendas,id',
            'activo' => 'boolean',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'tienda_id' => $request->tienda_id,
            'activo' => $request->activo ?? $usuario->activo,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta');
        }

        try {
            $usuario->delete();
            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario eliminado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar el usuario porque tiene registros asociados');
        }
    }
}