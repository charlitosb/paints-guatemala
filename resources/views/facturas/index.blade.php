@extends('layouts.app')

@section('title', 'Facturas')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-receipt"></i> Gestión de Facturas</h2>
        @if(auth()->user()->rol === 'cajero' || auth()->user()->rol === 'admin')
        <a href="{{ route('facturas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nueva Factura
        </a>
        @endif
    </div>

    <!-- Mensajes -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('facturas.index') }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="pagada" {{ request('estado') == 'pagada' ? 'selected' : '' }}>Pagada</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="anulada" {{ request('estado') == 'anulada' ? 'selected' : '' }}>Anulada</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Tienda</label>
                    <select name="tienda_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($tiendas as $tienda)
                        <option value="{{ $tienda->id }}" {{ request('tienda_id') == $tienda->id ? 'selected' : '' }}>
                            {{ $tienda->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="buscar" class="form-control" placeholder="Correlativo..." value="{{ request('buscar') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de facturas -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Factura</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Tienda</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($facturas as $factura)
                        <tr>
                            <td>
                                <strong>{{ $factura->serie }}-{{ str_pad($factura->correlativo, 6, '0', STR_PAD_LEFT) }}</strong>
                            </td>
                            <td>{{ $factura->fecha_emision->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($factura->cliente)
                                    {{ $factura->cliente->name }}
                                @else
                                    <span class="text-muted">Cliente general</span>
                                @endif
                            </td>
                            <td>{{ $factura->tienda->nombre }}</td>
                            <td><strong>Q{{ number_format($factura->total, 2) }}</strong></td>
                            <td>{!! $factura->getBadgeEstado() !!}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('facturas.show', $factura) }}" 
                                       class="btn btn-outline-primary" 
                                       title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if($factura->puedeAnularse())
                                    <form action="{{ route('facturas.anular', $factura) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('¿Está seguro de anular esta factura?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger" title="Anular">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <p class="mb-0">No hay facturas registradas</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-3">
                {{ $facturas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
