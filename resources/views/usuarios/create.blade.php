@extends('layouts.app')

@section('title', 'Nuevo Usuario')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-person-plus-fill"></i> Nuevo Usuario
            </h1>
            <p class="text-muted">Registra un nuevo usuario del sistema</p>
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
                <form action="{{ route('usuarios.store') }}" method="POST">
                    @csrf
                    
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
                                   value="{{ old('name') }}"
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
                                   value="{{ old('email') }}"
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
                                Contraseña <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password"
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Mínimo 8 caracteres</small>
                        </div>

                        <!-- Confirmar Password -->
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">
                                Confirmar Contraseña <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation"
                                   required>
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
                                <option value="admin" {{ old('rol') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="digitador" {{ old('rol') == 'digitador' ? 'selected' : '' }}>Digitador</option>
                                <option value="cajero" {{ old('rol') == 'cajero' ? 'selected' : '' }}>Cajero</option>
                                <option value="gerente" {{ old('rol') == 'gerente' ? 'selected' : '' }}>Gerente</option>
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
                                    <option value="{{ $tienda->id }}" {{ old('tienda_id') == $tienda->id ? 'selected' : '' }}>
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
                                   {{ old('activo', true) ? 'checked' : '' }}>
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
                            <i class="bi bi-save"></i> Guardar Usuario
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
                    <i class="bi bi-info-circle"></i> Información de Roles
                </h6>
                <ul class="small mb-0">
                    <li class="mb-2">
                        <strong>Administrador:</strong> Acceso total al sistema
                    </li>
                    <li class="mb-2">
                        <strong>Digitador:</strong> Puede registrar datos y productos
                    </li>
                    <li class="mb-2">
                        <strong>Cajero:</strong> Puede realizar ventas y facturas
                    </li>
                    <li>
                        <strong>Gerente:</strong> Puede ver reportes y estadísticas
                    </li>
                </ul>
            </div>
        </div>

        <div class="card mt-3 bg-warning text-dark">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-exclamation-triangle"></i> Contraseña
                </h6>
                <p class="small mb-0">
                    La contraseña debe tener al menos 8 caracteres. El usuario podrá cambiarla después desde su perfil.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
