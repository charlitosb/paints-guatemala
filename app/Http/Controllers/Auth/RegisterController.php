<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    /**
     * Mostrar formulario de registro
     */
    public function showRegistrationForm()
    {
        $tiendas = Tienda::activas()->get();
        return view('auth.register', compact('tiendas'));
    }

    /**
     * Procesar registro
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:digitador,cajero,gerente'],
            'tienda_id' => ['nullable', 'exists:tiendas,id'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'tienda_id' => $request->tienda_id,
            'activo' => true,
        ]);

        auth()->login($user);

        return redirect()->route('dashboard')
            ->with('success', '¡Cuenta creada exitosamente!');
    }
}