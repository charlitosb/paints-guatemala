<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoFactura extends Model
{
    use HasFactory;

    protected $fillable = [
        'factura_id',
        'tipo_pago',
        'monto',
        'referencia',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    // Relaciones
    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }
}