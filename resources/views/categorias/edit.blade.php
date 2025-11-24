@extends('layouts.app')

@section('title', 'Editar Categoría')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-tag"></i> Editar Categoría
            </h1>
            <p class="text-muted">Modificar información de la categoría</p>
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
                <form action="{{ route('categorias.update', $categoria->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Nombre -->
                    <div class="mb-3">
                        <label for="nombre" class="form-label">
                            Nombre de la Categoría <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nombre') is-invalid @enderror" 
                               id="nombre" 
                               name="nombre" 
                               value="{{ old('nombre', $categoria->nombre) }}"
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
                                  rows="4">{{ old('descripcion', $categoria->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Actualizar Categoría
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
                
                @if($categoria->created_at)
                <p class="small text-muted mb-2">
                    <strong>Creado:</strong> {{ $categoria->created_at->format('d/m/Y H:i') }}
                </p>
                @endif
                
                @if($categoria->updated_at)
                <p class="small text-muted mb-2">
                    <strong>Última modificación:</strong> {{ $categoria->updated_at->format('d/m/Y H:i') }}
                </p>
                @endif
                
                <hr>
                <p class="small text-muted mb-0">
                    Los cambios se aplicarán inmediatamente después de guardar.
                </p>
            </div>
        </div>

        @if($categoria->productos_count > 0)
        <div class="card mt-3 bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="bi bi-exclamation-circle"></i> Productos Asociados
                </h6>
                <p class="small mb-0">
                    Esta categoría tiene <strong>{{ $categoria->productos_count }} productos</strong> asociados.
                </p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
