@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-people"></i> Clientes
    </h1>
    <a href="{{ route('clientes.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Cliente
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($clientes->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>NIT</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->nombre }}</td>
                        <td>{{ $cliente->email ?? '-' }}</td>
                        <td>{{ $cliente->telefono ?? '-' }}</td>
                        <td>{{ $cliente->nit ?? 'C/F' }}</td>
                        <td>
                            @if($cliente->activo)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('clientes.show', $cliente->id) }}" class="btn btn-sm btn-info" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este cliente?')">
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
            {{ $clientes->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
            <p class="text-muted mt-3">No hay clientes registrados</p>
            <a href="{{ route('clientes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Crear Primer Cliente
            </a>
        </div>
        @endif
    </div>
</div>
@endsection