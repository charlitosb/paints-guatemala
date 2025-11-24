@extends('layouts.app')

@section('title', 'Inventarios')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-clipboard-data"></i> Inventarios
    </h1>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('inventarios.index') }}" class="row g-3">
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
            <div class="col-md-3">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar producto..." value="{{ request('buscar') }}">
            </div>
            <div class="col-md-2">
                <select name="bajo_stock" class="form-select">
                    <option value="">Todos</option>
                    <option value="1" {{ request('bajo_stock') == '1' ? 'selected' : '' }}>Bajo Stock</option>
                    <option value="sin_stock" {{ request('sin_stock') == 'sin_stock' ? 'selected' : '' }}>Sin Stock</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Buscar
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('inventarios.index') }}" class="btn btn-secondary w-100">
                    <i class="bi bi-x-circle"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($inventarios->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Tienda</th>
                        <th class="text-center">Existencia</th>
                        <th class="text-center">Stock Mínimo</th>
                        <th>Estado</th>
                        <th>Última Actualización</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventarios as $inventario)
                    <tr>
                        <td><code>{{ $inventario->producto->codigo }}</code></td>
                        <td><strong>{{ $inventario->producto->nombre }}</strong></td>
                        <td>{{ $inventario->tienda->nombre }}</td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $inventario->existencia }}</span>
                        </td>
                        <td class="text-center">{{ $inventario->producto->existencia_minima }}</td>
                        <td>
                            @if($inventario->existencia <= 0)
                                <span class="badge bg-danger">Sin Stock</span>
                            @elseif($inventario->existencia <= $inventario->producto->existencia_minima)
                                <span class="badge bg-warning text-dark">Bajo Stock</span>
                            @else
                                <span class="badge bg-success">Normal</span>
                            @endif
                        </td>
                        <td>{{ $inventario->ultima_actualizacion ? $inventario->ultima_actualizacion->format('d/m/Y H:i') : '-' }}</td>
                        <td class="text-end">
                            <a href="{{ route('inventarios.show', $inventario->id) }}" class="btn btn-sm btn-info" title="Ver historial">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $inventarios->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
            <p class="text-muted mt-3">No hay registros de inventario</p>
        </div>
        @endif
    </div>
</div>
@endsection