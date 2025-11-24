<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'empresa',
        'telefono',
        'email',
        'direccion',
        'nit',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones
    public function ingresosProductos()
    {
        return $this->hasMany(IngresoProducto::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}