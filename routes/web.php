<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\IngresoProductoController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReporteController;

// Ruta principal
Route::get('/', function () {
    return redirect()->route('login');
});

// Autenticación (sin middleware)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

Route::post('logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    
    // CRUD básicos (sin restricción de rol para desarrollo)
    Route::resource('proveedores', ProveedorController::class);
    Route::resource('clientes', ClienteController::class);
    Route::resource('categorias', CategoriaController::class);
    Route::resource('productos', ProductoController::class);
    Route::resource('ingresos', IngresoProductoController::class);
    Route::resource('facturas', FacturaController::class);
    Route::resource('cotizaciones', CotizacionController::class);
    Route::resource('usuarios', UserController::class);
    Route::resource('tiendas', TiendaController::class);
    
    // Inventarios
    Route::get('inventarios', [InventarioController::class, 'index'])->name('inventarios.index');
    Route::get('inventarios/{inventario}', [InventarioController::class, 'show'])->name('inventarios.show');
    
    // Reportes
    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('reportes/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
    Route::get('reportes/productos-mas-vendidos', [ReporteController::class, 'productosMasVendidos'])->name('reportes.productos-mas-vendidos');
    Route::get('reportes/inventario', [ReporteController::class, 'inventario'])->name('reportes.inventario');
    Route::get('reportes/bajo-stock', [ReporteController::class, 'bajoStock'])->name('reportes.bajo-stock');
});