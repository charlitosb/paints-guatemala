<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'tienda_id',
        'empleado_id',
        'correlativo',
        'serie',
        'fecha_emision',
        'subtotal',
        'descuento',
        'total',
        'estado', // pagada, pendiente, anulada
        'notas'
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    // ============================================
    // RELACIONES
    // ============================================

    /**
     * Cliente que realizó la compra (puede ser null)
     */
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    /**
     * Tienda donde se realizó la venta
     */
    public function tienda()
    {
        return $this->belongsTo(Tienda::class);
    }

    /**
     * Empleado (cajero) que realizó la venta
     */
    public function empleado()
    {
        return $this->belongsTo(User::class, 'empleado_id');
    }

    /**
     * Productos vendidos en esta factura
     */
    public function detalles()
    {
        return $this->hasMany(DetalleFactura::class);
    }

    /**
     * Medios de pago utilizados
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    // ============================================
    // SCOPES (CONSULTAS REUTILIZABLES)
    // ============================================

    /**
     * Facturas pagadas
     */
    public function scopePagadas($query)
    {
        return $query->where('estado', 'pagada');
    }

    /**
     * Facturas pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Facturas anuladas
     */
    public function scopeAnuladas($query)
    {
        return $query->where('estado', 'anulada');
    }

    /**
     * Facturas por fecha
     */
    public function scopePorFecha($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_emision', [$fechaInicio, $fechaFin]);
    }

    /**
     * Facturas por tienda
     */
    public function scopePorTienda($query, $tiendaId)
    {
        return $query->where('tienda_id', $tiendaId);
    }

    // ============================================
    // MÉTODOS AUXILIARES
    // ============================================

    /**
     * Generar siguiente número de correlativo
     */
    public static function siguienteCorrelativo()
    {
        $ultimo = self::orderBy('correlativo', 'desc')->first();
        return $ultimo ? $ultimo->correlativo + 1 : 1;
    }

    /**
     * Verificar si la factura puede ser anulada
     */
    public function puedeAnularse()
    {
        return $this->estado === 'pagada';
    }

    /**
     * Anular factura y devolver productos al inventario
     */
    public function anular()
    {
        if (!$this->puedeAnularse()) {
            return false;
        }

        \DB::beginTransaction();
        try {
            // Devolver productos al inventario
            foreach ($this->detalles as $detalle) {
                $inventario = Inventario::where('producto_id', $detalle->producto_id)
                    ->where('tienda_id', $this->tienda_id)
                    ->first();

                if ($inventario) {
                    $inventario->increment('existencia', $detalle->cantidad);
                }
            }

            // Cambiar estado
            $this->update(['estado' => 'anulada']);

            \DB::commit();
            return true;
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error al anular factura: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Calcular total de pagos recibidos
     */
    public function totalPagado()
    {
        return $this->pagos()->sum('monto');
    }

    /**
     * Obtener badge HTML según estado
     */
    public function getBadgeEstado()
    {
        $badges = [
            'pagada' => '<span class="badge bg-success">Pagada</span>',
            'pendiente' => '<span class="badge bg-warning">Pendiente</span>',
            'anulada' => '<span class="badge bg-danger">Anulada</span>'
        ];

        return $badges[$this->estado] ?? '<span class="badge bg-secondary">Desconocido</span>';
    }
}
