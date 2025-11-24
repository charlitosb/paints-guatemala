<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    
    protected $fillable = [
        'nombre',
        'empresa',
        'telefono',
        'email',
        'direccion',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    // Relación con ingresos de productos
    public function ingresos()
    {
        return $this->hasMany(\App\Models\IngresoProducto::class, 'proveedor_id');
    }

    // Scope para proveedores activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}