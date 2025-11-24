<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'session_id',
        'estado',
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCarrito::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    // Accessors
    public function getTotalAttribute()
    {
        return $this->detalles->sum(function ($detalle) {
            return $detalle->cantidad * $detalle->precio_unitario;
        });
    }
}