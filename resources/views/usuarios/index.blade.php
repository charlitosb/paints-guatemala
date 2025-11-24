@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-person-badge"></i> Usuarios
    </h1>
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Usuario
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($usuarios->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Tienda</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                    <tr>
                        <td><strong>{{ $usuario->name }}</strong></td>
                        <td>{{ $usuario->email }}</td>
                        <td>
                            <span class="badge bg-info">{{ ucfirst($usuario->role) }}</span>
                        </td>
                        <td>{{ $usuario->tienda->nombre ?? 'Sin asignar' }}</td>
                        <td>
                            @if($usuario->activo)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('usuarios.show', $usuario->id) }}" class="btn btn-sm btn-info" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($usuario->id != auth()->id())
                            <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $usuarios->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
            <p class="text-muted mt-3">No hay usuarios registrados</p>
        </div>
        @endif
    </div>
</div>
@endsection