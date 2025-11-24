@extends('layouts.app')

@section('title', 'Categorías')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-tags"></i> Categorías
    </h1>
    <a href="{{ route('categorias.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nueva Categoría
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($categorias->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Productos</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categorias as $categoria)
                    <tr>
                        <td><strong>{{ $categoria->nombre }}</strong></td>
                        <td>{{ Str::limit($categoria->descripcion, 50) ?? '-' }}</td>
                        <td>
                            <span class="badge bg-primary">{{ $categoria->productos_count ?? 0 }} productos</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('categorias.show', $categoria->id) }}" class="btn btn-sm btn-info" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('categorias.edit', $categoria->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('categorias.destroy', $categoria->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?')">
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
            {{ $categorias->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
            <p class="text-muted mt-3">No hay categorías registradas</p>
            <a href="{{ route('categorias.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Crear Primera Categoría
            </a>
        </div>
        @endif
    </div>
</div>
@endsection