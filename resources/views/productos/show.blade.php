@extends('layouts.app')

@section('title', 'Detalle del Producto')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-box-seam-fill"></i> Detalle del Producto
            </h1>
            <p class="text-muted">Información completa del producto</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-warning">
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
                    <i class="bi bi-box"></i> {{ $producto->nombre }}
                    @if($producto->activo)
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
                        
                        <div class="mb-2">
                            <label class="text-muted small">Código:</label>
                            <p class="mb-0"><strong>{{ $producto->codigo }}</strong></p>
                        </div>

                        <div class="mb-2">
                            <label class="text-muted small">Categoría:</label>
                            <p class="mb-0">{{ $producto->categoria->nombre ?? 'N/A' }}</p>
                        </div>

                        <div class="mb-2">
                            <label class="text-muted small">Tipo:</label>
                            <p class="mb-0">
                                @if($producto->tipo == 'accesorio')
                                    <span class="badge bg-info">Accesorio</span>
                                @elseif($producto->tipo == 'solvente')
                                    <span class="badge bg-warning">Solvente</span>
                                @elseif($producto->tipo == 'pintura')
                                    <span class="badge bg-primary">Pintura</span>
                                @else
                                    <span class="badge bg-secondary">Barniz</span>
                                @endif
                            </p>
                        </div>

                        @if($producto->marca)
                        <div class="mb-2">
                            <label class="text-muted small">Marca:</label>
                            <p class="mb-0">{{ $producto->marca }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Detalles Específicos</h6>
                        
                        @if($producto->tipo == 'accesorio' && $producto->tamano)
                            <div class="mb-2">
                                <label class="text-muted small">Tamaño:</label>
                                <p class="mb-0">{{ $producto->tamano }}</p>
                            </div>
                        @endif

                        @if($producto->tipo == 'pintura' && $producto->color)
                            <div class="mb-2">
                                <label class="text-muted small">Color:</label>
                                <p class="mb-0">{{ $producto->color }}</p>
                            </div>
                        @endif

                        @if($producto->unidad_medida)
                            <div class="mb-2">
                                <label class="text-muted small">Unidad de Medida:</label>
                                <p class="mb-0">{{ $producto->unidad_medida }}</p>
                            </div>
                        @endif

                        @if(in_array($producto->tipo, ['pintura', 'barniz']))
                            @if($producto->duracion_anios)
                            <div class="mb-2">
                                <label class="text-muted small">Duración:</label>
                                <p class="mb-0">{{ $producto->duracion_anios }} años</p>
                            </div>
                            @endif

                            @if($producto->cobertura_m2)
                            <div class="mb-2">
                                <label class="text-muted small">Cobertura:</label>
                                <p class="mb-0">{{ number_format($producto->cobertura_m2, 2) }} m²</p>
                            </div>
                            @endif
                        @endif
                    </div>
                </div>

                @if($producto->descripcion)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Descripción:</h6>
                    <p class="mb-0">{{ $producto->descripcion }}</p>
                </div>
                @endif

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="text-muted small">Precio de Venta:</label>
                        <h4 class="mb-0 text-success">Q {{ number_format($producto->precio_venta, 2) }}</h4>
                    </div>

                    @if($producto->descuento > 0)
                    <div class="col-md-4">
                        <label class="text-muted small">Descuento:</label>
                        <h4 class="mb-0 text-danger">{{ $producto->descuento }}%</h4>
                    </div>

                    <div class="col-md-4">
                        <label class="text-muted small">Precio con Descuento:</label>
                        <h4 class="mb-0 text-primary">
                            Q {{ number_format($producto->precio_venta * (1 - $producto->descuento / 100), 2) }}
                        </h4>
                    </div>
                    @endif
                </div>

                <hr>

                <div class="row">
                    @if($producto->created_at)
                    <div class="col-md-6">
                        <p class="small text-muted mb-0">
                            <i class="bi bi-calendar-plus"></i> 
                            <strong>Registrado:</strong> {{ $producto->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @endif
                    
                    @if($producto->updated_at)
                    <div class="col-md-6">
                        <p class="small text-muted mb-0">
                            <i class="bi bi-calendar-check"></i> 
                            <strong>Última actualización:</strong> {{ $producto->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Inventario por Tienda -->
        @if($producto->inventarios && $producto->inventarios->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-buildings"></i> Inventario por Tienda
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tienda</th>
                                <th class="text-center">Existencia</th>
                                <th class="text-center">Mínimo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($producto->inventarios as $inventario)
                            <tr>
                                <td><strong>{{ $inventario->tienda->nombre ?? 'N/A' }}</strong></td>
                                <td class="text-center">
                                    <span class="badge {{ $inventario->existencia <= $producto->existencia_minima ? 'bg-danger' : 'bg-success' }}">
                                        {{ $inventario->existencia }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $producto->existencia_minima }}</td>
                                <td>
                                    @if($inventario->existencia <= 0)
                                        <span class="badge bg-dark">Sin Stock</span>
                                    @elseif($inventario->existencia <= $producto->existencia_minima)
                                        <span class="badge bg-warning">Bajo Stock</span>
                                    @else
                                        <span class="badge bg-success">Disponible</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
                    <label class="text-muted small">Stock Mínimo:</label>
                    <h4 class="mb-0">{{ $producto->existencia_minima }}</h4>
                </div>

                @if($producto->inventarios && $producto->inventarios->count() > 0)
                <div class="mb-3">
                    <label class="text-muted small">Total en Inventario:</label>
                    <h4 class="mb-0 text-success">{{ $producto->inventarios->sum('existencia') }}</h4>
                </div>

                <div class="mb-0">
                    <label class="text-muted small">Tiendas con Stock:</label>
                    <h4 class="mb-0">{{ $producto->inventarios->where('existencia', '>', 0)->count() }}</h4>
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
                <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-warning w-100 mb-2">
                    <i class="bi bi-pencil"></i> Editar Producto
                </a>
                
                @php
                    $totalStock = $producto->inventarios ? $producto->inventarios->sum('existencia') : 0;
                @endphp
                
                @if($totalStock == 0)
                <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-trash"></i> Eliminar Producto
                    </button>
                </form>
                @else
                <button type="button" class="btn btn-danger w-100" disabled>
                    <i class="bi bi-trash"></i> No se puede eliminar
                </button>
                <small class="text-muted mt-2 d-block">
                    El producto tiene {{ $totalStock }} unidades en inventario
                </small>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection