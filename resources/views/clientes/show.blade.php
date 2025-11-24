@extends('layouts.app')

@section('title', 'Detalle del Cliente')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-person-circle"></i> Detalle del Cliente
            </h1>
            <p class="text-muted">Información completa del cliente</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person"></i> {{ $cliente->nombre }}
                    @if($cliente->activo)
                        <span class="badge bg-success float-end">Activo</span>
                    @else
                        <span class="badge bg-secondary float-end">Inactivo</span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Información General</h6>
                        
                        <div class="mb-3">
                            <label class="text-muted small">Nombre:</label>
                            <p class="mb-0"><strong>{{ $cliente->nombre }}</strong></p>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small">NIT:</label>
                            <p class="mb-0">
                                @if($cliente->nit)
                                    {{ $cliente->nit }}
                                @else
                                    <span class="text-muted">C/F (Consumidor Final)</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Información de Contacto</h6>
                        
                        @if($cliente->telefono)
                        <div class="mb-3">
                            <label class="text-muted small">Teléfono:</label>
                            <p class="mb-0">
                                <i class="bi bi-telephone"></i> {{ $cliente->telefono }}
                            </p>
                        </div>
                        @endif

                        @if($cliente->email)
                        <div class="mb-3">
                            <label class="text-muted small">Email:</label>
                            <p class="mb-0">
                                <i class="bi bi-envelope"></i> 
                                <a href="mailto:{{ $cliente->email }}">{{ $cliente->email }}</a>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                @if($cliente->direccion)
                <div class="mb-3">
                    <h6 class="text-muted">Dirección:</h6>
                    <p class="mb-0">
                        <i class="bi bi-geo-alt"></i> {{ $cliente->direccion }}
                    </p>
                </div>
                @endif

                <hr>

                <div class="row">
                    @if($cliente->created_at)
                    <div class="col-md-6">
                        <p class="small text-muted mb-0">
                            <i class="bi bi-calendar-plus"></i> 
                            <strong>Registrado:</strong> {{ $cliente->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @endif
                    
                    @if($cliente->updated_at)
                    <div class="col-md-6">
                        <p class="small text-muted mb-0">
                            <i class="bi bi-calendar-check"></i> 
                            <strong>Última actualización:</strong> {{ $cliente->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Historial de Facturas -->
        @if($cliente->facturas && $cliente->facturas->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-receipt"></i> Últimas Facturas
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No. Factura</th>
                                <th>Fecha</th>
                                <th>Tienda</th>
                                <th>Estado</th>
                                <th class="text-end">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cliente->facturas->take(10) as $factura)
                            <tr>
                                <td><strong>{{ $factura->numero_factura }}</strong></td>
                                <td>{{ $factura->fecha_factura->format('d/m/Y') }}</td>
                                <td>{{ $factura->tienda->nombre }}</td>
                                <td>
                                    @if($factura->estado == 'pagada')
                                        <span class="badge bg-success">Pagada</span>
                                    @elseif($factura->estado == 'pendiente')
                                        <span class="badge bg-warning">Pendiente</span>
                                    @else
                                        <span class="badge bg-danger">Anulada</span>
                                    @endif
                                </td>
                                <td class="text-end">Q{{ number_format($factura->total, 2) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('facturas.show', $factura->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($cliente->facturas->count() > 10)
                <p class="text-muted small mb-0 mt-2">
                    Mostrando las últimas 10 facturas de {{ $cliente->facturas->count() }} totales.
                </p>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Estadísticas -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-graph-up"></i> Estadísticas
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Total de Facturas:</label>
                    <h4 class="mb-0">{{ $cliente->facturas->count() ?? 0 }}</h4>
                </div>

                @if($cliente->facturas && $cliente->facturas->count() > 0)
                <div class="mb-3">
                    <label class="text-muted small">Monto Total Comprado:</label>
                    <h4 class="mb-0 text-success">
                        Q{{ number_format($cliente->facturas->sum('total'), 2) }}
                    </h4>
                </div>

                <div class="mb-3">
                    <label class="text-muted small">Última Compra:</label>
                    <p class="mb-0">{{ $cliente->facturas->first()->fecha_factura->format('d/m/Y') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Acciones -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-gear"></i> Acciones
                </h6>
            </div>
            <div class="card-body">
                <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-warning w-100 mb-2">
                    <i class="bi bi-pencil"></i> Editar Cliente
                </a>
                
                <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este cliente?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-trash"></i> Eliminar Cliente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
