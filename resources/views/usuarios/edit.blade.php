@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-person-gear"></i> Editar Usuario
            </h1>
            <p class="text-muted">Modificar información del usuario</p>
        </div>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">
                                Nombre Completo <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $usuario->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $usuario->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Password -->
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">
                                Nueva Contraseña
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Dejar vacío para mantener la actual</small>
                        </div>

                        <!-- Confirmar Password -->
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">
                                Confirmar Nueva Contraseña
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation">
                        </div>
                    </div>

                    <div class="row">
                        <!-- Rol -->
                        <div class="col-md-6 mb-3">
                            <label for="rol" class="form-label">
                                Rol <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('rol') is-invalid @enderror" 
                                    id="rol" 
                                    name="rol" 
                                    required>
                                <option value="">Seleccionar rol...</option>
                                <option value="admin" {{ old('rol', $usuario->rol) == 'admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="digitador" {{ old('rol', $usuario->rol) == 'digitador' ? 'selected' : '' }}>Digitador</option>
                                <option value="cajero" {{ old('rol', $usuario->rol) == 'cajero' ? 'selected' : '' }}>Cajero</option>
                                <option value="gerente" {{ old('rol', $usuario->rol) == 'gerente' ? 'selected' : '' }}>Gerente</option>
                            </select>
                            @error('rol')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tienda -->
                        <div class="col-md-6 mb-3">
                            <label for="tienda_id" class="form-label">Tienda Asignada</label>
                            <select class="form-select @error('tienda_id') is-invalid @enderror" 
                                    id="tienda_id" 
                                    name="tienda_id">
                                <option value="">Sin asignar</option>
                                @foreach($tiendas as $tienda)
                                    <option value="{{ $tienda->id }}" {{ old('tienda_id', $usuario->tienda_id) == $tienda->id ? 'selected' : '' }}>
                                        {{ $tienda->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tienda_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Activo -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="activo" 
                                   name="activo" 
                                   value="1"
                                   {{ old('activo', $usuario->activo) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">
                                Usuario activo
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Actualizar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-info-circle"></i> Información
                </h6>
                
                @if($usuario->created_at)
                <p class="small text-muted mb-2">
                    <strong>Creado:</strong> {{ $usuario->created_at->format('d/m/Y H:i') }}
                </p>
                @endif
                
                @if($usuario->updated_at)
                <p class="small text-muted mb-2">
                    <strong>Última modificación:</strong> {{ $usuario->updated_at->format('d/m/Y H:i') }}
                </p>
                @endif
                
                <hr>
                <p class="small text-muted mb-0">
                    Los cambios se aplicarán inmediatamente después de guardar.
                </p>
            </div>
        </div>

        @if($usuario->id == auth()->id())
        <div class="card mt-3 bg-warning text-dark">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-exclamation-triangle"></i> Advertencia
                </h6>
                <p class="small mb-0">
                    Estás editando tu propio usuario. Ten cuidado al cambiar tu rol o estado.
                </p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
