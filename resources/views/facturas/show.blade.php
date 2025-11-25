@extends('layouts.app')

@section('title', 'Detalle de Factura')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-receipt"></i> Factura {{ $factura->serie }}-{{ str_pad($factura->correlativo, 6, '0', STR_PAD_LEFT) }}</h2>
        <a href="{{ route('facturas.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        @foreach($errors->all() as $error)
        <div>{{ $error }}</div>
        @endforeach
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Información de la factura -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Información de la Factura</h5>
                    {!! $factura->getBadgeEstado() !!}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Factura:</th>
                                    <td><strong>{{ $factura->serie }}-{{ str_pad($factura->correlativo, 6, '0', STR_PAD_LEFT) }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Fecha:</th>
                                    <td>{{ $factura->fecha_emision->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Cliente:</th>
                                    <td>
                                        @if($factura->cliente)
                                            {{ $factura->cliente->name }}
                                        @else
                                            <span class="text-muted">Cliente general</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Tienda:</th>
                                    <td>{{ $factura->tienda->nombre }}</td>
                                </tr>
                                <tr>
                                    <th>Cajero:</th>
                                    <td>{{ $factura->empleado->name }}</td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td>{!! $factura->getBadgeEstado() !!}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($factura->notas)
                    <div class="alert alert-info mt-3 mb-0">
                        <strong>Notas:</strong> {{ $factura->notas }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Productos -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-cart"></i> Productos</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($factura->detalles as $detalle)
                                <tr>
                                    <td>
                                        <strong>{{ $detalle->producto->nombre }}</strong><br>
                                        <small class="text-muted">{{ $detalle->producto->codigo }}</small>
                                    </td>
                                    <td class="text-center">{{ $detalle->cantidad }}</td>
                                    <td class="text-end">Q{{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td class="text-end"><strong>Q{{ number_format($detalle->subtotal, 2) }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                    <td class="text-end"><strong>Q{{ number_format($factura->subtotal, 2) }}</strong></td>
                                </tr>
                                @if($factura->descuento > 0)
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Descuentos:</strong></td>
                                    <td class="text-end text-danger"><strong>-Q{{ number_format($factura->descuento, 2) }}</strong></td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-end"><h5 class="mb-0">TOTAL:</h5></td>
                                    <td class="text-end"><h5 class="mb-0 text-success">Q{{ number_format($factura->total, 2) }}</h5></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna lateral -->
        <div class="col-lg-4">
            <!-- Medios de pago -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-cash-stack"></i> Medios de Pago</h5>
                </div>
                <div class="card-body">
                    @foreach($factura->pagos as $pago)
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            @if($pago->tipo_pago === 'efectivo')
                                <i class="bi bi-cash text-success"></i> <strong>Efectivo</strong>
                            @elseif($pago->tipo_pago === 'cheque')
                                <i class="bi bi-file-text text-info"></i> <strong>Cheque</strong>
                                @if($pago->referencia)
                                <br><small class="text-muted">No. {{ $pago->referencia }}</small>
                                @endif
                            @else
                                <i class="bi bi-credit-card text-primary"></i> <strong>Tarjeta</strong>
                                @if($pago->referencia)
                                <br><small class="text-muted">Ref. {{ $pago->referencia }}</small>
                                @endif
                            @endif
                        </div>
                        <strong class="text-success">Q{{ number_format($pago->monto, 2) }}</strong>
                    </div>
                    @endforeach

                    <hr>
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-0">Total Pagado:</h5>
                        <h5 class="mb-0 text-success">Q{{ number_format($factura->totalPagado(), 2) }}</h5>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-gear"></i> Acciones</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="window.print()">
                            <i class="bi bi-printer"></i> Imprimir
                        </button>

                        @if($factura->puedeAnularse())
                        <button type="button" 
                                class="btn btn-outline-danger" 
                                data-bs-toggle="modal" 
                                data-bs-target="#anularModal">
                            <i class="bi bi-x-circle"></i> Anular Factura
                        </button>
                        @endif
                    </div>

                    <div class="alert alert-info small mt-3 mb-0">
                        <i class="bi bi-info-circle"></i>
                        @if($factura->estado === 'pagada')
                            Esta factura está pagada y puede ser anulada si es necesario.
                        @elseif($factura->estado === 'anulada')
                            Esta factura ha sido anulada.
                        @else
                            Esta factura está pendiente de pago.
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de anulación -->
@if($factura->puedeAnularse())
<div class="modal fade" id="anularModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Anular Factura</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>¿Está seguro de que desea anular esta factura?</strong></p>
                <p>Esta acción:</p>
                <ul>
                    <li>Devolverá los productos al inventario</li>
                    <li>Cambiará el estado a "Anulada"</li>
                    <li>No se podrá revertir</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <form action="{{ route('facturas.anular', $factura) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Sí, Anular Factura
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<style>
@media print {
    .btn, .modal, nav, .card-header, .alert {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endsection
