<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tienda extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'email',
        'latitud',
        'longitud',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    // Relaciones
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    public function ingresosProductos()
    {
        return $this->hasMany(IngresoProducto::class);
    }

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class);
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }
}