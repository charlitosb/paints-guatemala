@extends('layouts.app')

@section('title', 'Facturas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-receipt"></i> Facturas
    </h1>
    <a href="{{ route('facturas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nueva Factura
    </a>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('facturas.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
            </div>
            <div class="col-md-2">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="pagada" {{ request('estado') == 'pagada' ? 'selected' : '' }}>Pagada</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="anulada" {{ request('estado') == 'anulada' ? 'selected' : '' }}>Anulada</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="numero_factura" class="form-control" placeholder="No. Factura" value="{{ request('numero_factura') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Buscar
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($facturas->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No. Factura</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Tienda</th>
                        <th class="text-end">Total</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($facturas as $factura)
                    <tr>
                        <td><strong>{{ $factura->numero_factura }}</strong></td>
                        <td>{{ $factura->fecha_emision->format('d/m/Y') }}</td>
                        <td>{{ $factura->cliente->nombre ?? 'C/F' }}</td>
                        <td>{{ $factura->tienda->nombre }}</td>
                        <td class="text-end"><strong>Q{{ number_format($factura->total, 2) }}</strong></td>
                        <td>
                            @if($factura->estado == 'pagada')
                                <span class="badge bg-success">Pagada</span>
                            @elseif($factura->estado == 'pendiente')
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @else
                                <span class="badge bg-danger">Anulada</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('facturas.show', $factura->id) }}" class="btn btn-sm btn-info" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($factura->estado != 'anulada')
                            <form action="{{ route('facturas.destroy', $factura->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de anular esta factura?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Anular">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $facturas->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
            <p class="text-muted mt-3">No hay facturas registradas</p>
            <a href="{{ route('facturas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Crear Primera Factura
            </a>
        </div>
        @endif
    </div>
</div>
@endsection