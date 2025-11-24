<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'categoria_id',
        'tipo',
        'marca',
        'tamano',
        'color',
        'unidad_medida',
        'duracion_anios',
        'cobertura_m2',
        'precio_venta',
        'descuento',
        'existencia_minima',
        'activo'
    ];

    protected $casts = [
        'precio_venta' => 'decimal:2',
        'descuento' => 'decimal:2',
        'cobertura_m2' => 'decimal:2',
        'duracion_anios' => 'integer',
        'existencia_minima' => 'integer',
        'activo' => 'boolean'
    ];

    /**
     * Relación con categoría
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Relación con inventarios
     */
    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }

    /**
     * Scope para productos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope por tipo
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}