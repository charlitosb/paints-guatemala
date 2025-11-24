<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id',
        'tienda_id',
        'existencia',
        'ultima_actualizacion',
    ];

    protected $casts = [
        'existencia' => 'integer',
        'ultima_actualizacion' => 'datetime',
    ];

    // Relaciones
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function tienda()
    {
        return $this->belongsTo(Tienda::class);
    }

    // Scopes
    public function scopeBajoStock($query)
    {
        return $query->whereColumn('existencia', '<', 'producto.existencia_minima');
    }

    public function scopeSinStock($query)
    {
        return $query->where('existencia', '<=', 0);
    }
}