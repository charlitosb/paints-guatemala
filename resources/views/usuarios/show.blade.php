@extends('layouts.app')

@section('title', 'Detalle del Usuario')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-person-circle"></i> Detalle del Usuario
            </h1>
            <p class="text-muted">Información completa del usuario</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            @if($usuario->id != auth()->id())
            <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar
            </a>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-badge"></i> {{ $usuario->name }}
                    @if($usuario->activo)
                        <span class="badge bg-success float-end">Activo</span>
                    @else
                        <span class="badge bg-secondary float-end">Inactivo</span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Información Personal</h6>
                        
                        <div class="mb-3">
                            <label class="text-muted small">Nombre:</label>
                            <p class="mb-0"><strong>{{ $usuario->name }}</strong></p>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small">Email:</label>
                            <p class="mb-0">
                                <i class="bi bi-envelope"></i> 
                                <a href="mailto:{{ $usuario->email }}">{{ $usuario->email }}</a>
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Información del Sistema</h6>
                        
                        <div class="mb-3">
                            <label class="text-muted small">Rol:</label>
                            <p class="mb-0">
                                @if($usuario->rol == 'admin')
                                    <span class="badge bg-danger">Administrador</span>
                                @elseif($usuario->rol == 'digitador')
                                    <span class="badge bg-info">Digitador</span>
                                @elseif($usuario->rol == 'cajero')
                                    <span class="badge bg-warning">Cajero</span>
                                @else
                                    <span class="badge bg-success">Gerente</span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small">Tienda Asignada:</label>
                            <p class="mb-0">
                                @if($usuario->tienda)
                                    <i class="bi bi-shop"></i> {{ $usuario->tienda->nombre }}
                                @else
                                    <span class="text-muted">Sin asignar</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    @if($usuario->created_at)
                    <div class="col-md-6">
                        <p class="small text-muted mb-0">
                            <i class="bi bi-calendar-plus"></i> 
                            <strong>Registrado:</strong> {{ $usuario->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @endif
                    
                    @if($usuario->updated_at)
                    <div class="col-md-6">
                        <p class="small text-muted mb-0">
                            <i class="bi bi-calendar-check"></i> 
                            <strong>Última actualización:</strong> {{ $usuario->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Permisos del Rol -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-shield-check"></i> Permisos del Rol
                </h5>
            </div>
            <div class="card-body">
                @if($usuario->rol == 'admin')
                <ul class="mb-0">
                    <li>Acceso total al sistema</li>
                    <li>Gestión de usuarios</li>
                    <li>Configuración del sistema</li>
                    <li>Ver todos los reportes</li>
                    <li>Gestión de inventarios</li>
                    <li>Realizar ventas</li>
                </ul>
                @elseif($usuario->rol == 'digitador')
                <ul class="mb-0">
                    <li>Registrar productos</li>
                    <li>Gestionar inventarios</li>
                    <li>Registrar ingresos de productos</li>
                    <li>Gestionar proveedores y clientes</li>
                </ul>
                @elseif($usuario->rol == 'cajero')
                <ul class="mb-0">
                    <li>Realizar ventas</li>
                    <li>Emitir facturas</li>
                    <li>Gestionar clientes</li>
                    <li>Ver inventario</li>
                </ul>
                @else
                <ul class="mb-0">
                    <li>Ver reportes y estadísticas</li>
                    <li>Ver inventarios</li>
                    <li>Ver ventas</li>
                    <li>Analizar datos</li>
                </ul>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Estado -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle"></i> Estado
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Estado del Usuario:</label>
                    <h5 class="mb-0">
                        @if($usuario->activo)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </h5>
                </div>

                @if($usuario->id == auth()->id())
                <div class="alert alert-info mb-0">
                    <i class="bi bi-person-check"></i> Este es tu usuario
                </div>
                @endif
            </div>
        </div>

        <!-- Acciones -->
        @if($usuario->id != auth()->id())
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-gear"></i> Acciones
                </h6>
            </div>
            <div class="card-body">
                <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-warning w-100 mb-2">
                    <i class="bi bi-pencil"></i> Editar Usuario
                </a>
                
                <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-trash"></i> Eliminar Usuario
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="card mt-3 bg-warning text-dark">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-exclamation-triangle"></i> Advertencia
                </h6>
                <p class="small mb-0">
                    No puedes eliminar tu propio usuario mientras estés conectado.
                </p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
