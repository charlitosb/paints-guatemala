@extends('layouts.app')

@section('title', 'Ingresos de Productos')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-box-arrow-in-down"></i> Ingresos de Productos</h2>
        @if(auth()->user()->rol === 'digitador' || auth()->user()->rol === 'admin')
        <a href="{{ route('ingresos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Ingreso
        </a>
        @endif
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

    <!-- Contadores de Estado -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h3 class="text-warning mb-0">{{ $pendientes }}</h3>
                    <p class="text-muted mb-0"><i class="bi bi-clock-history"></i> Pendientes</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h3 class="text-success mb-0">{{ $recibidas }}</h3>
                    <p class="text-muted mb-0"><i class="bi bi-check-circle-fill"></i> Recibidas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h3 class="text-danger mb-0">{{ $canceladas }}</h3>
                    <p class="text-muted mb-0"><i class="bi bi-x-circle-fill"></i> Canceladas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('ingresos.index') }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="recibida" {{ request('estado') == 'recibida' ? 'selected' : '' }}>Recibida</option>
                        <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>

                <div class="col-md-3">
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

                <div class="col-md-3">
                    <label class="form-label">Proveedor</label>
                    <select name="proveedor_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}" {{ request('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                            {{ $proveedor->nombre }}
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

                <div class="col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="{{ route('ingresos.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Ingresos -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Número Ingreso</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Tienda</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Creado por</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ingresos as $ingreso)
                        <tr>
                            <td>
                                <strong>{{ $ingreso->numero_ingreso }}</strong>
                            </td>
                            <td>{{ $ingreso->fecha_ingreso->format('d/m/Y') }}</td>
                            <td>{{ $ingreso->proveedor->nombre }}</td>
                            <td>{{ $ingreso->tienda->nombre }}</td>
                            <td><strong>Q{{ number_format($ingreso->total, 2) }}</strong></td>
                            <td>
                                {!! $ingreso->getBadgeEstado() !!}
                                @if($ingreso->estado === 'recibida' && $ingreso->fecha_recepcion)
                                <br><small class="text-muted">Recibido: {{ $ingreso->fecha_recepcion->format('d/m/Y') }}</small>
                                @endif
                            </td>
                            <td>{{ $ingreso->usuario->name }}</td>
                            <td>
                                <a href="{{ route('ingresos.show', $ingreso) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <p class="mb-0">No hay ingresos registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($ingresos->hasPages())
            <div class="mt-3">
                {{ $ingresos->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
