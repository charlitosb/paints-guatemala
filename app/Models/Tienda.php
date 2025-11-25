<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tienda extends Model
{
    use HasFactory;

    protected $table = 'tiendas';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'email',
        'latitud',
        'longitud',
        'activo',
    ];

    protected $casts = [
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
        'activo' => 'boolean',
    ];

    // ============================================
    // RELACIONES
    // ============================================

    /**
     * Inventarios de esta tienda
     */
    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }

    /**
     * Ingresos de productos a esta tienda
     */
    public function ingresos()
    {
        return $this->hasMany(Ingreso::class);
    }

    /**
     * Facturas emitidas en esta tienda
     */
    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Solo tiendas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    // ============================================
    // MÉTODOS
    // ============================================

    /**
     * Obtener existencia de un producto en esta tienda
     */
    public function getExistenciaProducto($productoId)
    {
        $inventario = $this->inventarios()
            ->where('producto_id', $productoId)
            ->first();

        return $inventario ? $inventario->existencia : 0;
    }

    /**
     * Verificar si tiene stock de un producto
     */
    public function tieneStock($productoId, $cantidad = 1)
    {
        return $this->getExistenciaProducto($productoId) >= $cantidad;
    }

    /**
     * Obtener productos con bajo stock
     */
    public function productosBajoStock()
    {
        return $this->inventarios()
            ->whereColumn('existencia', '<=', 'existencia_minima')
            ->with('producto')
            ->get();
    }

    /**
     * Obtener productos sin stock
     */
    public function productosSinStock()
    {
        return $this->inventarios()
            ->where('existencia', 0)
            ->with('producto')
            ->get();
    }

    /**
     * Calcular valor total del inventario
     */
    public function valorInventario()
    {
        return $this->inventarios()
            ->join('productos', 'inventarios.producto_id', '=', 'productos.id')
            ->selectRaw('SUM(inventarios.existencia * productos.precio_venta) as total')
            ->value('total') ?? 0;
    }

    /**
     * Obtener coordenadas para mapa
     */
    public function getCoordenadas()
    {
        if ($this->latitud && $this->longitud) {
            return [
                'lat' => (float) $this->latitud,
                'lng' => (float) $this->longitud,
            ];
        }
        return null;
    }

    /**
     * Verificar si tiene coordenadas GPS
     */
    public function tieneCoordenadas()
    {
        return $this->latitud !== null && $this->longitud !== null;
    }
}