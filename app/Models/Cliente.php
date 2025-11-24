<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'direccion',
        'nit',
        'dpi',
        'fecha_registro',
        'recibir_promociones',
        'activo',
    ];

    protected $casts = [
        'fecha_registro' => 'date',
        'recibir_promociones' => 'boolean',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class);
    }

    public function carritos()
    {
        return $this->hasMany(Carrito::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeConPromociones($query)
    {
        return $query->where('recibir_promociones', true);
    }
}