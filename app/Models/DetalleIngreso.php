<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleIngreso extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingreso_id',
        'producto_id',
        'cantidad',
        'precio_compra',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_compra' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relaciones
    public function ingreso()
    {
        return $this->belongsTo(IngresoProducto::class, 'ingreso_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}