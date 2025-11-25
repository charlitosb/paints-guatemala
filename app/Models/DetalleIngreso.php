<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleIngreso extends Model
{
    use HasFactory;

    protected $table = 'detalle_ingresos';

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

    // ============================================
    // RELACIONES
    // ============================================

    public function ingreso()
    {
        return $this->belongsTo(Ingreso::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    // ============================================
    // MÉTODOS
    // ============================================

    /**
     * Calcular el subtotal automáticamente
     */
    public function calcularSubtotal()
    {
        return $this->cantidad * $this->precio_compra;
    }

    /**
     * Boot method para calcular subtotal automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detalle) {
            if (!$detalle->subtotal) {
                $detalle->subtotal = $detalle->calcularSubtotal();
            }
        });

        static::updating(function ($detalle) {
            $detalle->subtotal = $detalle->calcularSubtotal();
        });
    }
}
