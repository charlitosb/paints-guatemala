@extends('layouts.app')

@section('title', 'Ingresos de Productos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-box-arrow-in-down"></i> Ingresos de Productos
    </h1>
    <a href="{{ route('ingresos.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Ingreso
    </a>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('ingresos.index') }}" class="row g-3">
            <div class="col-md-3">
                <select name="proveedor_id" class="form-select">
                    <option value="">Todos los proveedores</option>
                    @foreach($proveedores as $prov)
                        <option value="{{ $prov->id }}" {{ request('proveedor_id') == $prov->id ? 'selected' : '' }}>
                            {{ $prov->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="tienda_id" class="form-select">
                    <option value="">Todas las tiendas</option>
                    @foreach($tiendas as $tienda)
                        <option value="{{ $tienda->id }}" {{ request('tienda_id') == $tienda->id ? 'selected' : '' }}>
                            {{ $tienda->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
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
        @if($ingresos->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No. Ingreso</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>Tienda</th>
                        <th>Usuario</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ingresos as $ingreso)
                    <tr>
                        <td><strong>{{ $ingreso->numero_ingreso }}</strong></td>
                        <td>{{ $ingreso->fecha_ingreso->format('d/m/Y') }}</td>
                        <td>{{ $ingreso->proveedor->nombre }}</td>
                        <td>{{ $ingreso->tienda->nombre }}</td>
                        <td>{{ $ingreso->user->name }}</td>
                        <td class="text-end"><strong>Q{{ number_format($ingreso->total, 2) }}</strong></td>
                        <td class="text-end">
                            <a href="{{ route('ingresos.show', $ingreso->id) }}" class="btn btn-sm btn-info" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $ingresos->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
            <p class="text-muted mt-3">No hay ingresos registrados</p>
            <a href="{{ route('ingresos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Crear Primer Ingreso
            </a>
        </div>
        @endif
    </div>
</div>
@endsection