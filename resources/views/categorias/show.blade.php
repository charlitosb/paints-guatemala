@extends('layouts.app')

@section('title', 'Detalle de la Categoría')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-tag"></i> Detalle de la Categoría
            </h1>
            <p class="text-muted">Información completa de la categoría</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <a href="{{ route('categorias.edit', $categoria->id) }}" class="btn btn-warning">
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
                    <i class="bi bi-tag-fill"></i> {{ $categoria->nombre }}
                    <span class="badge bg-light text-primary float-end">
                        {{ $categoria->productos_count }} Productos
                    </span>
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Nombre:</h6>
                    <p class="mb-0"><strong>{{ $categoria->nombre }}</strong></p>
                </div>

                @if($categoria->descripcion)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Descripción:</h6>
                    <p class="mb-0">{{ $categoria->descripcion }}</p>
                </div>
                @endif

                <hr>

                <div class="row">
                    @if($categoria->created_at)
                    <div class="col-md-6">
                        <p class="small text-muted mb-0">
                            <i class="bi bi-calendar-plus"></i> 
                            <strong>Registrado:</strong> {{ $categoria->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @endif
                    
                    @if($categoria->updated_at)
                    <div class="col-md-6">
                        <p class="small text-muted mb-0">
                            <i class="bi bi-calendar-check"></i> 
                            <strong>Última actualización:</strong> {{ $categoria->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Productos de la Categoría -->
        @if($categoria->productos && $categoria->productos->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-box-seam"></i> Productos en esta Categoría
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Marca</th>
                                <th class="text-end">Precio</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categoria->productos as $producto)
                            <tr>
                                <td><code>{{ $producto->codigo }}</code></td>
                                <td><strong>{{ $producto->nombre }}</strong></td>
                                <td>
                                    @if($producto->tipo_producto == 'pintura')
                                        <span class="badge bg-primary">Pintura</span>
                                    @elseif($producto->tipo_producto == 'solvente')
                                        <span class="badge bg-warning">Solvente</span>
                                    @elseif($producto->tipo_producto == 'accesorio')
                                        <span class="badge bg-info">Accesorio</span>
                                    @else
                                        <span class="badge bg-secondary">Barniz</span>
                                    @endif
                                </td>
                                <td>{{ $producto->marca }}</td>
                                <td class="text-end">Q{{ number_format($producto->precio_venta, 2) }}</td>
                                <td>
                                    @if($producto->activo)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-secondary">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('productos.show', $producto->id) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($categoria->productos_count > 20)
                <p class="text-muted small mb-0 mt-2">
                    Mostrando los primeros 20 productos de {{ $categoria->productos_count }} totales.
                </p>
                @endif
            </div>
        </div>
        @else
        <div class="card mt-4">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="text-muted mt-3 mb-0">
                    Esta categoría aún no tiene productos asociados.
                </p>
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
                    <label class="text-muted small">Total de Productos:</label>
                    <h4 class="mb-0">{{ $categoria->productos_count }}</h4>
                </div>

                @if($categoria->productos && $categoria->productos->count() > 0)
                <div class="mb-3">
                    <label class="text-muted small">Productos Activos:</label>
                    <h4 class="mb-0 text-success">
                        {{ $categoria->productos->where('activo', 1)->count() }}
                    </h4>
                </div>

                <div class="mb-0">
                    <label class="text-muted small">Productos Inactivos:</label>
                    <h4 class="mb-0 text-secondary">
                        {{ $categoria->productos->where('activo', 0)->count() }}
                    </h4>
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
                <a href="{{ route('categorias.edit', $categoria->id) }}" 
                   class="btn btn-warning w-100 mb-2">
                    <i class="bi bi-pencil"></i> Editar Categoría
                </a>
                
                @if($categoria->productos_count == 0)
                <form action="{{ route('categorias.destroy', $categoria->id) }}" 
                      method="POST" 
                      onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-trash"></i> Eliminar Categoría
                    </button>
                </form>
                @else
                <button type="button" class="btn btn-danger w-100" disabled>
                    <i class="bi bi-trash"></i> No se puede eliminar
                </button>
                <small class="text-muted mt-2 d-block">
                    Esta categoría tiene productos asociados
                </small>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
