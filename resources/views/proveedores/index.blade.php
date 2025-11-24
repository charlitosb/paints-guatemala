@extends('layouts.app')

@section('title', 'Proveedores')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-truck"></i> Proveedores
    </h1>
    <a href="{{ route('proveedores.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Proveedor
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($proveedores->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Empresa</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($proveedores as $proveedor)
                    <tr>
                        <td>{{ $proveedor->nombre }}</td>
                        <td>{{ $proveedor->empresa ?? '-' }}</td>
                        <td>{{ $proveedor->telefono ?? '-' }}</td>
                        <td>{{ $proveedor->email ?? '-' }}</td>
                        <td>
                            @if($proveedor->activo)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('proveedores.show', $proveedor->id) }}" class="btn btn-sm btn-info" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('proveedores.edit', $proveedor->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('proveedores.destroy', $proveedor->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este proveedor?')">
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
            {{ $proveedores->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
            <p class="text-muted mt-3">No hay proveedores registrados</p>
            <a href="{{ route('proveedores.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Crear Primer Proveedor
            </a>
        </div>
        @endif
    </div>
</div>
@endsection