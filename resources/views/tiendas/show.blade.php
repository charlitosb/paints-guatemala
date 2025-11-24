@extends('layouts.app')

@section('title', 'Detalle de la Tienda')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-shop-window"></i> Detalle de la Tienda
            </h1>
            <p class="text-muted">Información completa de la tienda</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tiendas.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <a href="{{ route('tiendas.edit', $tienda->id) }}" class="btn btn-warning">
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
                    <i class="bi bi-shop"></i> {{ $tienda->nombre }}
                    @if($tienda->activa)
                        <span class="badge bg-success float-end">Activa</span>
                    @else
                        <span class="badge bg-secondary float-end">Inactiva</span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-muted mb-2">
                        <i class="bi bi-geo-alt"></i> Dirección:
                    </h6>
                    <p class="mb-0">{{ $tienda->direccion }}</p>
                </div>

                <div class="row mb-4">
                    @if($tienda->telefono)
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">
                            <i class="bi bi-telephone"></i> Teléfono:
                        </h6>
                        <p class="mb-0">{{ $tienda->telefono }}</p>
                    </div>
                    @endif

                    @if($tienda->email)
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">
                            <i class="bi bi-envelope"></i> Email:
                        </h6>
                        <p class="mb-0">
                            <a href="mailto:{{ $tienda->email }}">{{ $tienda->email }}</a>
                        </p>
                    </div>
                    @endif
                </div>

                <hr>

                <div class="row">
                    @if($tienda->created_at)
                    <div class="col-md-6">
                        <p class="small text-muted mb-0">
                            <i class="bi bi-calendar-plus"></i> 
                            <strong>Registrado:</strong> {{ $tienda->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @endif
                    
                    @if($tienda->updated_at)
                    <div class="col-md-6">
                        <p class="small text-muted mb-0">
                            <i class="bi bi-calendar-check"></i> 
                            <strong>Última actualización:</strong> {{ $tienda->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Usuarios de la Tienda -->
        @if($tienda->users && $tienda->users->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-people"></i> Usuarios Asignados
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tienda->users as $user)
                            <tr>
                                <td><strong>{{ $user->name }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->rol == 'admin')
                                        <span class="badge bg-danger">Admin</span>
                                    @elseif($user->rol == 'digitador')
                                        <span class="badge bg-info">Digitador</span>
                                    @elseif($user->rol == 'cajero')
                                        <span class="badge bg-warning">Cajero</span>
                                    @else
                                        <span class="badge bg-success">Gerente</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->activo)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-secondary">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('usuarios.show', $user->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Productos con Bajo Stock -->
        @if($tienda->inventarios && $tienda->inventarios->count() > 0)
        <div class="card mt-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle"></i> Productos con Bajo Stock
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Existencia</th>
                                <th class="text-center">Mínimo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tienda->inventarios as $inventario)
                            <tr>
                                <td><strong>{{ $inventario->producto->nombre }}</strong></td>
                                <td class="text-center">
                                    <span class="badge bg-danger">{{ $inventario->existencia }}</span>
                                </td>
                                <td class="text-center">{{ $inventario->existencia_minima }}</td>
                                <td>
                                    <span class="badge bg-warning">Bajo Stock</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($tienda->inventarios->count() >= 10)
                <p class="text-muted small mb-0 mt-2">
                    Mostrando los primeros 10 productos con bajo stock.
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
                    <label class="text-muted small">Usuarios Asignados:</label>
                    <h4 class="mb-0">{{ $tienda->users_count ?? 0 }}</h4>
                </div>

                <div class="mb-3">
                    <label class="text-muted small">Productos en Inventario:</label>
                    <h4 class="mb-0">{{ $tienda->inventarios_count ?? 0 }}</h4>
                </div>

                @if(isset($tienda->facturas_count))
                <div class="mb-0">
                    <label class="text-muted small">Facturas Emitidas:</label>
                    <h4 class="mb-0 text-success">{{ $tienda->facturas_count }}</h4>
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
                <a href="{{ route('tiendas.edit', $tienda->id) }}" class="btn btn-warning w-100 mb-2">
                    <i class="bi bi-pencil"></i> Editar Tienda
                </a>
                
                @if($tienda->users_count == 0 && $tienda->inventarios_count == 0)
                <form action="{{ route('tiendas.destroy', $tienda->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta tienda?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-trash"></i> Eliminar Tienda
                    </button>
                </form>
                @else
                <button type="button" class="btn btn-danger w-100" disabled>
                    <i class="bi bi-trash"></i> No se puede eliminar
                </button>
                <small class="text-muted mt-2 d-block">
                    @if($tienda->users_count > 0)
                        Tiene usuarios asignados
                    @endif
                    @if($tienda->inventarios_count > 0)
                        Tiene inventario registrado
                    @endif
                </small>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection