<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_factura',
        'serie',
        'correlativo',
        'fecha_emision',
        'cliente_id',
        'tienda_id',
        'user_id',
        'subtotal',
        'descuento',
        'total',
        'estado',
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
        'correlativo' => 'integer',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleFactura::class);
    }

    public function pagos()
    {
        return $this->hasMany(PagoFactura::class);
    }

    // Scopes
    public function scopePagadas($query)
    {
        return $query->where('estado', 'pagada');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAnuladas($query)
    {
        return $query->where('estado', 'anulada');
    }

    // Accessors
    public function getTotalPagosAttribute()
    {
        return $this->pagos()->sum('monto');
    }
}