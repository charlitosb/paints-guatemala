@extends('layouts.app')

@section('title', 'Cotizaciones')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-file-earmark-text"></i> Cotizaciones
    </h1>
    <a href="{{ route('cotizaciones.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nueva Cotización
    </a>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('cotizaciones.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
            </div>
            <div class="col-md-3">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                    <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                    <option value="facturada" {{ request('estado') == 'facturada' ? 'selected' : '' }}>Facturada</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Buscar
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($cotizaciones->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No. Cotización</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Tienda</th>
                        <th class="text-end">Total</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cotizaciones as $cotizacion)
                    <tr>
                        <td><strong>{{ $cotizacion->numero_cotizacion }}</strong></td>
                        <td>{{ $cotizacion->fecha_cotizacion->format('d/m/Y') }}</td>
                        <td>{{ $cotizacion->nombre_cliente }}</td>
                        <td>{{ $cotizacion->tienda->nombre }}</td>
                        <td class="text-end"><strong>Q{{ number_format($cotizacion->total, 2) }}</strong></td>
                        <td>
                            @if($cotizacion->estado == 'pendiente')
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @elseif($cotizacion->estado == 'aprobada')
                                <span class="badge bg-success">Aprobada</span>
                            @elseif($cotizacion->estado == 'rechazada')
                                <span class="badge bg-danger">Rechazada</span>
                            @else
                                <span class="badge bg-info">Facturada</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('cotizaciones.show', $cotizacion->id) }}" class="btn btn-sm btn-info" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $cotizaciones->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
            <p class="text-muted mt-3">No hay cotizaciones registradas</p>
            <a href="{{ route('cotizaciones.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Crear Primera Cotización
            </a>
        </div>
        @endif
    </div>
</div>
@endsection