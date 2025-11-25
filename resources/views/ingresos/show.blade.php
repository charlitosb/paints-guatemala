@extends('layouts.app')

@section('title', 'Detalle de Ingreso ' . $ingreso->numero_ingreso)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-box-arrow-in-down"></i> Ingreso {{ $ingreso->numero_ingreso }}</h2>
        <a href="{{ route('ingresos.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Listado
        </a>
    </div>

    <!-- Mensajes -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Información Principal -->
        <div class="col-md-8">
            <!-- Datos del Ingreso -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-info-circle"></i> Información del Ingreso</span>
                    {!! $ingreso->getBadgeEstado() !!}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th class="text-muted" style="width: 40%">Número:</th>
                                    <td><strong>{{ $ingreso->numero_ingreso }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Fecha Ingreso:</th>
                                    <td>{{ $ingreso->fecha_ingreso->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Fecha Recepción:</th>
                                    <td>
                                        @if($ingreso->fecha_recepcion)
                                            {{ $ingreso->fecha_recepcion->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">Pendiente</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Creado por:</th>
                                    <td>{{ $ingreso->usuario->name }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th class="text-muted" style="width: 40%">Proveedor:</th>
                                    <td>
                                        <strong>{{ $ingreso->proveedor->nombre }}</strong>
                                        @if($ingreso->proveedor->telefono)
                                            <br><small class="text-muted"><i class="bi bi-telephone"></i> {{ $ingreso->proveedor->telefono }}</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Tienda Destino:</th>
                                    <td>
                                        <strong>{{ $ingreso->tienda->nombre }}</strong>
                                        @if($ingreso->tienda->direccion)
                                            <br><small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $ingreso->tienda->direccion }}</small>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($ingreso->notas)
                    <hr>
                    <div class="mb-0">
                        <strong><i class="bi bi-sticky"></i> Notas:</strong>
                        <p class="mb-0 mt-2">{{ $ingreso->notas }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Detalle de Productos -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-box-seam"></i> Productos del Ingreso
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Compra</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ingreso->detalles as $index => $detalle)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $detalle->producto->nombre }}</strong>
                                        @if($detalle->producto->codigo)
                                            <br><small class="text-muted">Código: {{ $detalle->producto->codigo }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $detalle->cantidad }}</span>
                                    </td>
                                    <td class="text-end">Q {{ number_format($detalle->precio_compra, 2) }}</td>
                                    <td class="text-end"><strong>Q {{ number_format($detalle->subtotal, 2) }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="text-end"><strong>Total de productos:</strong></td>
                                    <td class="text-center"><strong>{{ $ingreso->detalles->count() }}</strong></td>
                                    <td class="text-end"><strong>Unidades:</strong></td>
                                    <td class="text-end"><strong>{{ $ingreso->detalles->sum('cantidad') }}</strong></td>
                                </tr>
                                <tr class="table-success">
                                    <td colspan="4" class="text-end"><strong class="h5">TOTAL:</strong></td>
                                    <td class="text-end"><strong class="h5">Q {{ number_format($ingreso->total, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="col-md-4">
            <!-- Acciones -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <i class="bi bi-gear"></i> Acciones
                </div>
                <div class="card-body">
                    @if($ingreso->estado === 'pendiente')
                        @if(auth()->user()->rol === 'digitador' || auth()->user()->rol === 'admin')
                        <div class="d-grid gap-2">
                            <!-- Confirmar Recepción -->
                            <form action="{{ route('ingresos.confirmar', $ingreso) }}" method="POST" 
                                  onsubmit="return confirm('¿Confirmar la recepción de este ingreso?\n\nEsto actualizará el inventario de la tienda.')">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="bi bi-check-circle"></i> Confirmar Recepción
                                </button>
                            </form>

                            <!-- Cancelar -->
                            <form action="{{ route('ingresos.cancelar', $ingreso) }}" method="POST"
                                  onsubmit="return confirm('¿Está seguro de cancelar este ingreso?\n\nEsta acción no se puede deshacer.')">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="bi bi-x-circle"></i> Cancelar Ingreso
                                </button>
                            </form>
                        </div>

                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="bi bi-exclamation-triangle"></i>
                            <small>Este ingreso está <strong>PENDIENTE</strong>. 
                            Confirme la recepción para actualizar el inventario.</small>
                        </div>
                        @else
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i>
                            Solo el digitador o administrador puede confirmar o cancelar ingresos.
                        </div>
                        @endif
                    @elseif($ingreso->estado === 'recibida')
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle-fill"></i>
                            <strong>Ingreso Confirmado</strong><br>
                            <small>El inventario fue actualizado el {{ $ingreso->fecha_recepcion->format('d/m/Y') }}</small>
                        </div>
                    @elseif($ingreso->estado === 'cancelada')
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-x-circle-fill"></i>
                            <strong>Ingreso Cancelado</strong><br>
                            <small>Este ingreso fue cancelado y no afectó el inventario.</small>
                        </div>

                        @if(auth()->user()->rol === 'admin')
                        <hr>
                        <form action="{{ route('ingresos.destroy', $ingreso) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar permanentemente este ingreso?\n\nEsta acción no se puede deshacer.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash"></i> Eliminar Registro
                            </button>
                        </form>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Resumen -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-calculator"></i> Resumen
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Productos:</span>
                        <strong>{{ $ingreso->detalles->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Unidades:</span>
                        <strong>{{ $ingreso->detalles->sum('cantidad') }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="h5">Total:</span>
                        <strong class="h4 text-success">Q {{ number_format($ingreso->total, 2) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Información de Auditoría -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-clock-history"></i> Auditoría
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <div class="mb-2">
                            <strong>Creado:</strong><br>
                            {{ $ingreso->created_at->format('d/m/Y H:i:s') }}
                        </div>
                        <div>
                            <strong>Última actualización:</strong><br>
                            {{ $ingreso->updated_at->format('d/m/Y H:i:s') }}
                        </div>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
