<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ingreso extends Model
{
    use HasFactory;

    protected $table = 'ingresos';

    protected $fillable = [
        'proveedor_id',
        'tienda_id',
        'usuario_id',
        'numero_ingreso',
        'fecha_ingreso',
        'fecha_recepcion',
        'total',
        'estado',
        'notas',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'fecha_recepcion' => 'date',
        'total' => 'decimal:2',
    ];

    // ============================================
    // RELACIONES
    // ============================================

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function tienda()
    {
        return $this->belongsTo(Tienda::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleIngreso::class);
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeRecibidas($query)
    {
        return $query->where('estado', 'recibida');
    }

    public function scopeCanceladas($query)
    {
        return $query->where('estado', 'cancelada');
    }

    // ============================================
    // MÉTODOS
    // ============================================

    /**
     * Generar el siguiente número de ingreso
     */
    public static function siguienteNumeroIngreso()
    {
        $año = date('Y');
        $ultimoIngreso = self::where('numero_ingreso', 'like', "ING-{$año}-%")
            ->orderByDesc('id')
            ->first();

        if ($ultimoIngreso) {
            $partes = explode('-', $ultimoIngreso->numero_ingreso);
            $numero = intval($partes[2]) + 1;
        } else {
            $numero = 1;
        }

        return sprintf('ING-%s-%03d', $año, $numero);
    }

    /**
     * Verificar si el ingreso puede confirmarse
     */
    public function puedeConfirmarse()
    {
        return $this->estado === 'pendiente';
    }

    /**
     * Verificar si el ingreso puede cancelarse
     */
    public function puedeCancelarse()
    {
        return $this->estado === 'pendiente';
    }

    /**
     * Confirmar recepción del ingreso y actualizar inventario
     */
    public function confirmarRecepcion()
    {
        if (!$this->puedeConfirmarse()) {
            return false;
        }

        DB::beginTransaction();
        try {
            // Actualizar inventario por cada producto
            foreach ($this->detalles as $detalle) {
                $inventario = Inventario::where('producto_id', $detalle->producto_id)
                    ->where('tienda_id', $this->tienda_id)
                    ->first();

                if ($inventario) {
                    $inventario->increment('existencia', $detalle->cantidad);
                } else {
                    // Si no existe el inventario, crearlo
                    Inventario::create([
                        'producto_id' => $detalle->producto_id,
                        'tienda_id' => $this->tienda_id,
                        'existencia' => $detalle->cantidad,
                        'existencia_minima' => $detalle->producto->existencia_minima ?? 10,
                    ]);
                }
            }

            // Actualizar estado del ingreso
            $this->update([
                'estado' => 'recibida',
                'fecha_recepcion' => now(),
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Cancelar el ingreso
     */
    public function cancelar()
    {
        if (!$this->puedeCancelarse()) {
            return false;
        }

        return $this->update(['estado' => 'cancelada']);
    }

    /**
     * Obtener el badge HTML según el estado
     */
    public function getBadgeEstado()
    {
        $badges = [
            'pendiente' => '<span class="badge bg-warning text-dark"><i class="bi bi-clock-history"></i> Pendiente</span>',
            'recibida' => '<span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Recibida</span>',
            'cancelada' => '<span class="badge bg-danger"><i class="bi bi-x-circle-fill"></i> Cancelada</span>',
        ];

        return $badges[$this->estado] ?? '<span class="badge bg-secondary">Desconocido</span>';
    }

    /**
     * Obtener el ícono según el estado
     */
    public function getIconoEstado()
    {
        $iconos = [
            'pendiente' => '<i class="bi bi-clock-history text-warning"></i>',
            'recibida' => '<i class="bi bi-check-circle-fill text-success"></i>',
            'cancelada' => '<i class="bi bi-x-circle-fill text-danger"></i>',
        ];

        return $iconos[$this->estado] ?? '<i class="bi bi-question-circle"></i>';
    }
}
