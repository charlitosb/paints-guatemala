@extends('layouts.app')

@section('title', 'Nueva Tienda')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-shop"></i> Nueva Tienda
            </h1>
            <p class="text-muted">Registra una nueva sucursal</p>
        </div>
        <a href="{{ route('tiendas.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('tiendas.store') }}" method="POST">
                    @csrf
                    
                    <!-- Nombre -->
                    <div class="mb-3">
                        <label for="nombre" class="form-label">
                            Nombre de la Tienda <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nombre') is-invalid @enderror" 
                               id="nombre" 
                               name="nombre" 
                               value="{{ old('nombre') }}"
                               placeholder="Ej: Pradera Chimaltenango"
                               required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dirección -->
                    <div class="mb-3">
                        <label for="direccion" class="form-label">
                            Dirección <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('direccion') is-invalid @enderror" 
                                  id="direccion" 
                                  name="direccion" 
                                  rows="2"
                                  required>{{ old('direccion') }}</textarea>
                        @error('direccion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Teléfono -->
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" 
                                   class="form-control @error('telefono') is-invalid @enderror" 
                                   id="telefono" 
                                   name="telefono" 
                                   value="{{ old('telefono') }}"
                                   placeholder="1234-5678">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   placeholder="tienda@paints.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Activa -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="activa" 
                                   name="activa" 
                                   value="1"
                                   {{ old('activa', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activa">
                                Tienda activa
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('tiendas.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Tienda
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
                <p class="small text-muted mb-2">
                    Los campos marcados con <span class="text-danger">*</span> son obligatorios.
                </p>
                <hr>
                <p class="small text-muted mb-2">
                    <strong>Tiendas existentes:</strong>
                </p>
                <ul class="small text-muted mb-0">
                    <li>Pradera Chimaltenango</li>
                    <li>Pradera Escuintla</li>
                    <li>Las Américas - Mazatenango</li>
                    <li>La Trinidad - Coatepeque</li>
                    <li>Pradera Xela - Quetzaltenango</li>
                    <li>Miraflores - Ciudad Guatemala</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
