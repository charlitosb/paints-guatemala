@extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-box-seam"></i> Editar Producto
            </h1>
            <p class="text-muted">Modificar información del producto</p>
        </div>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('productos.update', $producto->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('codigo') is-invalid @enderror" 
                                   id="codigo" name="codigo" value="{{ old('codigo', $producto->codigo) }}" required>
                            @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="categoria_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                            <select class="form-select @error('categoria_id') is-invalid @enderror" 
                                    id="categoria_id" name="categoria_id" required>
                                <option value="">Seleccionar categoría...</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                               id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
                        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                  id="descripcion" name="descripcion" rows="2">{{ old('descripcion', $producto->descripcion) }}</textarea>
                        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipo" class="form-label">Tipo de Producto <span class="text-danger">*</span></label>
                            <select class="form-select @error('tipo') is-invalid @enderror" id="tipo" name="tipo" required>
                                <option value="">Seleccionar tipo...</option>
                                <option value="accesorio" {{ old('tipo', $producto->tipo) == 'accesorio' ? 'selected' : '' }}>Accesorio</option>
                                <option value="solvente" {{ old('tipo', $producto->tipo) == 'solvente' ? 'selected' : '' }}>Solvente</option>
                                <option value="pintura" {{ old('tipo', $producto->tipo) == 'pintura' ? 'selected' : '' }}>Pintura</option>
                                <option value="barniz" {{ old('tipo', $producto->tipo) == 'barniz' ? 'selected' : '' }}>Barniz</option>
                            </select>
                            @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="marca" class="form-label">Marca</label>
                            <input type="text" class="form-control @error('marca') is-invalid @enderror" 
                                   id="marca" name="marca" value="{{ old('marca', $producto->marca) }}">
                            @error('marca')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Campos dinámicos -->
                    <div id="camposAccesorio" class="campos-tipo" style="display:none;">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tamano" class="form-label">Tamaño</label>
                                <input type="text" class="form-control" id="tamano" name="tamano" 
                                       value="{{ old('tamano', $producto->tamano) }}">
                            </div>
                        </div>
                    </div>

                    <div id="camposSolvente" class="campos-tipo" style="display:none;">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="unidad_medida_solvente" class="form-label">Unidad de Medida</label>
                                <select class="form-select" id="unidad_medida_solvente" name="unidad_medida">
                                    <option value="">Seleccionar...</option>
                                    <option value="1/32 galón" {{ old('unidad_medida', $producto->unidad_medida) == '1/32 galón' ? 'selected' : '' }}>1/32 galón</option>
                                    <option value="1/16 galón" {{ old('unidad_medida', $producto->unidad_medida) == '1/16 galón' ? 'selected' : '' }}>1/16 galón</option>
                                    <option value="1/8 galón" {{ old('unidad_medida', $producto->unidad_medida) == '1/8 galón' ? 'selected' : '' }}>1/8 galón</option>
                                    <option value="1/4 galón" {{ old('unidad_medida', $producto->unidad_medida) == '1/4 galón' ? 'selected' : '' }}>1/4 galón</option>
                                    <option value="1/2 galón" {{ old('unidad_medida', $producto->unidad_medida) == '1/2 galón' ? 'selected' : '' }}>1/2 galón</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="camposPintura" class="campos-tipo" style="display:none;">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="color" class="form-label">Color</label>
                                <input type="text" class="form-control" id="color" name="color" 
                                       value="{{ old('color', $producto->color) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="unidad_medida_pintura" class="form-label">Unidad de Medida</label>
                                <select class="form-select" id="unidad_medida_pintura" name="unidad_medida">
                                    <option value="">Seleccionar...</option>
                                    <option value="1/32 galón" {{ old('unidad_medida', $producto->unidad_medida) == '1/32 galón' ? 'selected' : '' }}>1/32 galón</option>
                                    <option value="1/16 galón" {{ old('unidad_medida', $producto->unidad_medida) == '1/16 galón' ? 'selected' : '' }}>1/16 galón</option>
                                    <option value="1/8 galón" {{ old('unidad_medida', $producto->unidad_medida) == '1/8 galón' ? 'selected' : '' }}>1/8 galón</option>
                                    <option value="1/4 galón" {{ old('unidad_medida', $producto->unidad_medida) == '1/4 galón' ? 'selected' : '' }}>1/4 galón</option>
                                    <option value="1/2 galón" {{ old('unidad_medida', $producto->unidad_medida) == '1/2 galón' ? 'selected' : '' }}>1/2 galón</option>
                                    <option value="1 galón" {{ old('unidad_medida', $producto->unidad_medida) == '1 galón' ? 'selected' : '' }}>1 galón</option>
                                    <option value="1 cubeta" {{ old('unidad_medida', $producto->unidad_medida) == '1 cubeta' ? 'selected' : '' }}>1 cubeta</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="duracion_anios_pintura" class="form-label">Duración (años)</label>
                                <input type="number" class="form-control" id="duracion_anios_pintura" name="duracion_anios" 
                                       value="{{ old('duracion_anios', $producto->duracion_anios) }}" min="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cobertura_m2_pintura" class="form-label">Cobertura (m²)</label>
                                <input type="number" step="0.01" class="form-control" id="cobertura_m2_pintura" name="cobertura_m2" 
                                       value="{{ old('cobertura_m2', $producto->cobertura_m2) }}" min="0">
                            </div>
                        </div>
                    </div>

                    <div id="camposBarniz" class="campos-tipo" style="display:none;">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="unidad_medida_barniz" class="form-label">Unidad de Medida</label>
                                <select class="form-select" id="unidad_medida_barniz" name="unidad_medida">
                                    <option value="">Seleccionar...</option>
                                    <option value="1/32 galón" {{ old('unidad_medida', $producto->unidad_medida) == '1/32 galón' ? 'selected' : '' }}>1/32 galón</option>
                                    <option value="1/16 galón" {{ old('unidad_medida', $producto->unidad_medida) == '1/16 galón' ? 'selected' : '' }}>1/16 galón</option>
                                    <option value="1/8 galón" {{ old('unidad_medida', $producto->unidad_medida) == '1/8 galón' ? 'selected' : '' }}>1/8 galón</option>
                                    <option value="1/4 galón" {{ old('unidad_medida', $producto->unidad_medida) == '1/4 galón' ? 'selected' : '' }}>1/4 galón</option>
                                    <option value="1/2 galón" {{ old('unidad_medida', $producto->unidad_medida) == '1/2 galón' ? 'selected' : '' }}>1/2 galón</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="duracion_anios_barniz" class="form-label">Duración (años)</label>
                                <input type="number" class="form-control" id="duracion_anios_barniz" name="duracion_anios" 
                                       value="{{ old('duracion_anios', $producto->duracion_anios) }}" min="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="cobertura_m2_barniz" class="form-label">Cobertura (m²)</label>
                                <input type="number" step="0.01" class="form-control" id="cobertura_m2_barniz" name="cobertura_m2" 
                                       value="{{ old('cobertura_m2', $producto->cobertura_m2) }}" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="precio_venta" class="form-label">Precio de Venta <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Q</span>
                                <input type="number" step="0.01" class="form-control @error('precio_venta') is-invalid @enderror" 
                                       id="precio_venta" name="precio_venta" value="{{ old('precio_venta', $producto->precio_venta) }}" required>
                                @error('precio_venta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="descuento" class="form-label">Descuento (%)</label>
                            <input type="number" step="0.01" class="form-control @error('descuento') is-invalid @enderror" 
                                   id="descuento" name="descuento" value="{{ old('descuento', $producto->descuento) }}" min="0" max="100">
                            @error('descuento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="existencia_minima" class="form-label">Stock Mínimo <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('existencia_minima') is-invalid @enderror" 
                                   id="existencia_minima" name="existencia_minima" value="{{ old('existencia_minima', $producto->existencia_minima) }}" required>
                            @error('existencia_minima')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" 
                                   {{ old('activo', $producto->activo) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">Producto activo</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Actualizar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title"><i class="bi bi-info-circle"></i> Información</h6>
                @if($producto->created_at)
                <p class="small text-muted mb-2"><strong>Creado:</strong> {{ $producto->created_at->format('d/m/Y H:i') }}</p>
                @endif
                @if($producto->updated_at)
                <p class="small text-muted mb-0"><strong>Última modificación:</strong> {{ $producto->updated_at->format('d/m/Y H:i') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoSelect = document.getElementById('tipo');
    
    function mostrarCamposPorTipo() {
        document.querySelectorAll('.campos-tipo').forEach(el => el.style.display = 'none');
        
        switch(tipoSelect.value) {
            case 'accesorio': document.getElementById('camposAccesorio').style.display = 'block'; break;
            case 'solvente': document.getElementById('camposSolvente').style.display = 'block'; break;
            case 'pintura': document.getElementById('camposPintura').style.display = 'block'; break;
            case 'barniz': document.getElementById('camposBarniz').style.display = 'block'; break;
        }
    }
    
    tipoSelect.addEventListener('change', mostrarCamposPorTipo);
    mostrarCamposPorTipo(); // Ejecutar al cargar
});
</script>
@endsection