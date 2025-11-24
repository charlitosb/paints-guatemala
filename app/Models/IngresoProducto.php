<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngresoProducto extends Model
{
    protected $table = 'ingresos_productos'; // ← Con esta linea se ajusta el nombre del modelo por conflico con el plural de ingresos
    
    protected $fillable = [
        'numero_ingreso',
        'proveedor_id',
        'tienda_id',
        'user_id',
        'fecha_ingreso',
        'total',
        'observaciones'
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'total' => 'decimal:2'
    ];

    // Relaciones
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
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
        return $this->hasMany(DetalleIngresoProducto::class, 'ingreso_producto_id');
    }
}