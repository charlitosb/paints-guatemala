@extends('layouts.app')

@section('title', 'Nueva Categoría')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-tag"></i> Nueva Categoría
            </h1>
            <p class="text-muted">Registra una nueva categoría de productos</p>
        </div>
        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('categorias.store') }}" method="POST">
                    @csrf
                    
                    <!-- Nombre -->
                    <div class="mb-3">
                        <label for="nombre" class="form-label">
                            Nombre de la Categoría <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nombre') is-invalid @enderror" 
                               id="nombre" 
                               name="nombre" 
                               value="{{ old('nombre') }}"
                               placeholder="Ej: Pinturas, Accesorios, Solventes"
                               required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                  id="descripcion" 
                                  name="descripcion" 
                                  rows="4"
                                  placeholder="Describe el tipo de productos que pertenecen a esta categoría">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Categoría
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
                    <strong>Ejemplos de categorías:</strong>
                </p>
                <ul class="small text-muted mb-0">
                    <li>Pinturas para interiores</li>
                    <li>Pinturas para exteriores</li>
                    <li>Accesorios de pintura</li>
                    <li>Solventes y diluyentes</li>
                    <li>Barnices y selladores</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
