<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';  // ← IMPORTANTE

    protected $fillable = [
        'factura_id',
        'tipo_pago',
        'monto',
        'referencia'
    ];

    protected $casts = [
        'monto' => 'decimal:2'
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }
}