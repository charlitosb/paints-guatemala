<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'cotizaciones';

    protected $fillable = [
        'numero_cotizacion',
        'fecha_cotizacion',
        'cliente_id',
        'nombre_cliente',
        'email_cliente',
        'tienda_id',
        'subtotal',
        'descuento',
        'total',
        'estado',
    ];

    protected $casts = [
        'fecha_cotizacion' => 'datetime',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function tienda()
    {
        return $this->belongsTo(Tienda::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCotizacion::class);
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }
}