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
        'tipo_producto',
        'marca',
        'precio_venta',
        'porcentaje_descuento',
        'existencia_minima',
        'tamano',
        'unidad_medida',
        'medida_volumen',
        'color',
        'base_pintura',
        'duracion_anos',
        'cobertura_m2',
        'activo',
    ];

    protected $casts = [
        'precio_venta' => 'decimal:2',
        'porcentaje_descuento' => 'decimal:2',
        'existencia_minima' => 'integer',
        'duracion_anos' => 'integer',
        'cobertura_m2' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }

    public function detalleFacturas()
    {
        return $this->hasMany(DetalleFactura::class);
    }

    public function detalleIngresos()
    {
        return $this->hasMany(DetalleIngreso::class);
    }

    public function detalleCotizaciones()
    {
        return $this->hasMany(DetalleCotizacion::class);
    }

    public function detalleCarritos()
    {
        return $this->hasMany(DetalleCarrito::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_producto', $tipo);
    }

    public function scopeConDescuento($query)
    {
        return $query->where('porcentaje_descuento', '>', 0);
    }

    // Accessors
    public function getPrecioConDescuentoAttribute()
    {
        return $this->precio_venta - ($this->precio_venta * ($this->porcentaje_descuento / 100));
    }

    public function getTotalInventarioAttribute()
    {
        return $this->inventarios()->sum('existencia');
    }
}