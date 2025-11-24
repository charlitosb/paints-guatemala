<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        // Validar datos
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Verificar si el usuario existe y está activo
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors([
                'email' => 'No existe un usuario con este correo electrónico.',
            ])->onlyInput('email');
        }
        
        if (!$user->activo) {
            return back()->withErrors([
                'email' => 'Tu cuenta está desactivada. Contacta al administrador.',
            ])->onlyInput('email');
        }

        // Intentar autenticar
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            return redirect()->intended('dashboard')
                ->with('success', '¡Bienvenido, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'Sesión cerrada correctamente');
    }
}