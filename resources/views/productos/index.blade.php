@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-box-seam"></i> Productos
    </h1>
    <a href="{{ route('productos.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Producto
    </a>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('productos.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre o código..." value="{{ request('buscar') }}">
            </div>
            <div class="col-md-3">
                <select name="tipo_producto" class="form-select">
                    <option value="">Todos los tipos</option>
                    <option value="accesorio" {{ request('tipo_producto') == 'accesorio' ? 'selected' : '' }}>Accesorios</option>
                    <option value="solvente" {{ request('tipo_producto') == 'solvente' ? 'selected' : '' }}>Solventes</option>
                    <option value="pintura" {{ request('tipo_producto') == 'pintura' ? 'selected' : '' }}>Pinturas</option>
                    <option value="barniz" {{ request('tipo_producto') == 'barniz' ? 'selected' : '' }}>Barnices</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="categoria_id" class="form-select">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>
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
        @if($productos->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Tipo</th>
                        <th>Marca</th>
                        <th class="text-end">Precio</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $producto)
                    <tr>
                        <td><code>{{ $producto->codigo }}</code></td>
                        <td><strong>{{ $producto->nombre }}</strong></td>
                        <td>{{ $producto->categoria->nombre }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ ucfirst($producto->tipo_producto) }}</span>
                        </td>
                        <td>{{ $producto->marca ?? '-' }}</td>
                        <td class="text-end">Q{{ number_format($producto->precio_venta, 2) }}</td>
                        <td>
                            @if($producto->activo)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('productos.show', $producto->id) }}" class="btn btn-sm btn-info" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $productos->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
            <p class="text-muted mt-3">No hay productos registrados</p>
            <a href="{{ route('productos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Crear Primer Producto
            </a>
        </div>
        @endif
    </div>
</div>
@endsection