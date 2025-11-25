@extends('layouts.app')

@section('title', 'Nuevo Ingreso de Productos')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-box-arrow-in-down"></i> Nuevo Ingreso de Productos</h2>
        <a href="{{ route('ingresos.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <!-- Errores -->
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <strong><i class="bi bi-exclamation-triangle"></i> Error:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('ingresos.store') }}" method="POST" id="formIngreso">
        @csrf

        <div class="row">
            <!-- Datos del Ingreso -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-info-circle"></i> Información del Ingreso
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Número de Ingreso</label>
                                <input type="text" class="form-control" value="{{ $siguienteNumero }}" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Fecha de Ingreso <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_ingreso" class="form-control" 
                                       value="{{ old('fecha_ingreso', date('Y-m-d')) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Estado</label>
                                <input type="text" class="form-control bg-warning text-dark" value="PENDIENTE" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Proveedor <span class="text-danger">*</span></label>
                                <select name="proveedor_id" class="form-select" required>
                                    <option value="">-- Seleccionar Proveedor --</option>
                                    @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                        {{ $proveedor->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tienda Destino <span class="text-danger">*</span></label>
                                <select name="tienda_id" class="form-select" required>
                                    <option value="">-- Seleccionar Tienda --</option>
                                    @foreach($tiendas as $tienda)
                                    <option value="{{ $tienda->id }}" {{ old('tienda_id') == $tienda->id ? 'selected' : '' }}>
                                        {{ $tienda->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Notas / Observaciones</label>
                                <textarea name="notas" class="form-control" rows="2" 
                                          placeholder="Observaciones adicionales...">{{ old('notas') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Productos -->
                <div class="card">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-box-seam"></i> Productos del Ingreso</span>
                        <button type="button" class="btn btn-light btn-sm" onclick="agregarProducto()">
                            <i class="bi bi-plus-circle"></i> Agregar Producto
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tablaProductos">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40%">Producto</th>
                                        <th style="width: 15%">Cantidad</th>
                                        <th style="width: 20%">Precio Compra</th>
                                        <th style="width: 20%">Subtotal</th>
                                        <th style="width: 5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="productosBody">
                                    <!-- Fila inicial -->
                                    <tr class="producto-row" data-index="0">
                                        <td>
                                            <select name="productos[0][producto_id]" class="form-select producto-select" required onchange="actualizarPrecio(this)">
                                                <option value="">-- Seleccionar --</option>
                                                @foreach($productos as $producto)
                                                <option value="{{ $producto->id }}" data-precio="{{ $producto->precio_compra ?? $producto->precio_venta * 0.7 }}">
                                                    {{ $producto->nombre }} - {{ $producto->codigo ?? 'S/C' }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="productos[0][cantidad]" class="form-control cantidad-input" 
                                                   min="1" value="1" required onchange="calcularSubtotal(this)">
                                        </td>
                                        <td>
                                            <input type="number" name="productos[0][precio_compra]" class="form-control precio-input" 
                                                   step="0.01" min="0" value="0.00" required onchange="calcularSubtotal(this)">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control subtotal-display" value="Q 0.00" readonly>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)" disabled>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen -->
            <div class="col-md-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header bg-dark text-white">
                        <i class="bi bi-calculator"></i> Resumen del Ingreso
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total de Productos:</span>
                            <strong id="totalProductos">0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total de Unidades:</span>
                            <strong id="totalUnidades">0</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5">Total:</span>
                            <strong class="h4 text-success" id="totalGeneral">Q 0.00</strong>
                        </div>

                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle"></i>
                            <small>El ingreso se creará en estado <strong>PENDIENTE</strong>. 
                            Deberá confirmarse para actualizar el inventario.</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save"></i> Guardar Ingreso
                            </button>
                            <a href="{{ route('ingresos.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let productoIndex = 0;

// Productos disponibles para JavaScript
const productosData = @json($productos);

function agregarProducto() {
    productoIndex++;
    const tbody = document.getElementById('productosBody');
    
    let optionsHtml = '<option value="">-- Seleccionar --</option>';
    productosData.forEach(producto => {
        const precio = producto.precio_compra || (producto.precio_venta * 0.7);
        optionsHtml += `<option value="${producto.id}" data-precio="${precio}">${producto.nombre} - ${producto.codigo || 'S/C'}</option>`;
    });

    const newRow = `
        <tr class="producto-row" data-index="${productoIndex}">
            <td>
                <select name="productos[${productoIndex}][producto_id]" class="form-select producto-select" required onchange="actualizarPrecio(this)">
                    ${optionsHtml}
                </select>
            </td>
            <td>
                <input type="number" name="productos[${productoIndex}][cantidad]" class="form-control cantidad-input" 
                       min="1" value="1" required onchange="calcularSubtotal(this)">
            </td>
            <td>
                <input type="number" name="productos[${productoIndex}][precio_compra]" class="form-control precio-input" 
                       step="0.01" min="0" value="0.00" required onchange="calcularSubtotal(this)">
            </td>
            <td>
                <input type="text" class="form-control subtotal-display" value="Q 0.00" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    tbody.insertAdjacentHTML('beforeend', newRow);
    actualizarTotales();
    actualizarBotonesEliminar();
}

function eliminarFila(btn) {
    const row = btn.closest('tr');
    row.remove();
    actualizarTotales();
    actualizarBotonesEliminar();
}

function actualizarPrecio(select) {
    const row = select.closest('tr');
    const selectedOption = select.options[select.selectedIndex];
    const precio = selectedOption.dataset.precio || 0;
    
    row.querySelector('.precio-input').value = parseFloat(precio).toFixed(2);
    calcularSubtotal(select);
}

function calcularSubtotal(element) {
    const row = element.closest('tr');
    const cantidad = parseFloat(row.querySelector('.cantidad-input').value) || 0;
    const precio = parseFloat(row.querySelector('.precio-input').value) || 0;
    const subtotal = cantidad * precio;
    
    row.querySelector('.subtotal-display').value = 'Q ' + subtotal.toFixed(2);
    actualizarTotales();
}

function actualizarTotales() {
    const rows = document.querySelectorAll('.producto-row');
    let totalProductos = 0;
    let totalUnidades = 0;
    let totalGeneral = 0;

    rows.forEach(row => {
        const select = row.querySelector('.producto-select');
        const cantidad = parseFloat(row.querySelector('.cantidad-input').value) || 0;
        const precio = parseFloat(row.querySelector('.precio-input').value) || 0;

        if (select.value) {
            totalProductos++;
            totalUnidades += cantidad;
            totalGeneral += cantidad * precio;
        }
    });

    document.getElementById('totalProductos').textContent = totalProductos;
    document.getElementById('totalUnidades').textContent = totalUnidades;
    document.getElementById('totalGeneral').textContent = 'Q ' + totalGeneral.toFixed(2);
}

function actualizarBotonesEliminar() {
    const rows = document.querySelectorAll('.producto-row');
    const btns = document.querySelectorAll('.producto-row .btn-danger');
    
    btns.forEach(btn => {
        btn.disabled = rows.length <= 1;
    });
}

// Validación antes de enviar
document.getElementById('formIngreso').addEventListener('submit', function(e) {
    const selects = document.querySelectorAll('.producto-select');
    let hayProducto = false;
    
    selects.forEach(select => {
        if (select.value) hayProducto = true;
    });
    
    if (!hayProducto) {
        e.preventDefault();
        alert('Debe agregar al menos un producto');
        return false;
    }
});

// Inicializar
actualizarTotales();
</script>
@endpush
@endsection
