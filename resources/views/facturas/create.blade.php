@extends('layouts.app')

@section('title', 'Nueva Factura')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-receipt-cutoff"></i> Nueva Factura</h2>
        <a href="{{ route('facturas.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <strong>Errores de validación:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('facturas.store') }}" method="POST" id="facturaForm">
        @csrf

        <div class="row">
            <!-- Columna principal -->
            <div class="col-lg-8">
                <!-- Información básica -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-info-circle"></i> Información Básica
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Factura #</label>
                                <input type="text" class="form-control" value="A-{{ str_pad($siguienteCorrelativo, 6, '0', STR_PAD_LEFT) }}" disabled>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tienda *</label>
                                <select name="tienda_id" id="tienda_id" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($tiendas as $tienda)
                                    <option value="{{ $tienda->id }}" {{ old('tienda_id') == $tienda->id ? 'selected' : '' }}>
                                        {{ $tienda->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cliente</label>
                                <select name="cliente_id" id="cliente_id" class="form-select">
                                    <option value="">Cliente general</option>
                                    @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Agregar productos -->
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-cart-plus"></i> Agregar Productos
                    </div>
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-7 mb-2">
                                <label class="form-label">Producto</label>
                                <select id="producto_select" class="form-select">
                                    <option value="">Seleccione un producto...</option>
                                    @foreach($productos as $producto)
                                    <option value="{{ $producto->id }}" 
                                            data-nombre="{{ $producto->nombre }}"
                                            data-precio="{{ $producto->precio }}"
                                            data-descuento="{{ $producto->descuento }}">
                                        {{ $producto->nombre }} - Q{{ number_format($producto->precio, 2) }}
                                        @if($producto->descuento > 0)
                                        ({{ $producto->descuento }}% descuento)
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-2">
                                <label class="form-label">Cantidad</label>
                                <input type="number" id="cantidad_input" class="form-control" value="1" min="1">
                            </div>

                            <div class="col-md-2 mb-2">
                                <button type="button" id="btnAgregar" class="btn btn-success w-100">
                                    <i class="bi bi-plus-circle"></i> Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de productos -->
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-list-ul"></i> Productos en la factura
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0" id="tablaProductos">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50%">Producto</th>
                                        <th style="width: 15%">Cantidad</th>
                                        <th style="width: 15%">Precio Unit.</th>
                                        <th style="width: 15%">Subtotal</th>
                                        <th style="width: 5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="productosBody">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox"></i> No hay productos agregados
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Medios de pago -->
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <i class="bi bi-cash-stack"></i> Medios de Pago
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Efectivo</label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="number" 
                                           name="pago_efectivo" 
                                           id="pago_efectivo" 
                                           class="form-control pago-input" 
                                           step="0.01" 
                                           min="0" 
                                           value="0">
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cheque</label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="number" 
                                           name="pago_cheque" 
                                           id="pago_cheque" 
                                           class="form-control pago-input" 
                                           step="0.01" 
                                           min="0" 
                                           value="0">
                                </div>
                                <input type="text" 
                                       name="numero_cheque" 
                                       class="form-control form-control-sm mt-1" 
                                       placeholder="Número de cheque (opcional)">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tarjeta</label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="number" 
                                           name="pago_tarjeta" 
                                           id="pago_tarjeta" 
                                           class="form-control pago-input" 
                                           step="0.01" 
                                           min="0" 
                                           value="0">
                                </div>
                                <input type="text" 
                                       name="referencia_tarjeta" 
                                       class="form-control form-control-sm mt-1" 
                                       placeholder="Referencia (opcional)">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <strong>Total a pagar:</strong> Q<span id="totalPagar">0.00</span>
                            </div>
                            <div class="col-md-6 text-end">
                                <strong>Diferencia:</strong> Q<span id="diferencia" class="text-danger">0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notas adicionales -->
                <div class="card mb-3">
                    <div class="card-body">
                        <label class="form-label">Notas (opcional)</label>
                        <textarea name="notas" class="form-control" rows="2" placeholder="Observaciones adicionales...">{{ old('notas') }}</textarea>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="d-flex justify-content-end gap-2 mb-4">
                    <a href="{{ route('facturas.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary" id="btnGuardar">
                        <i class="bi bi-save"></i> Guardar Factura
                    </button>
                </div>
            </div>

            <!-- Resumen lateral -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-calculator"></i> Resumen</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong>Q<span id="subtotal">0.00</span></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Descuentos:</span>
                            <strong class="text-danger">-Q<span id="descuentos">0.00</span></strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="mb-0">TOTAL:</h5>
                            <h5 class="mb-0 text-success">Q<span id="total">0.00</span></h5>
                        </div>

                        <div class="alert alert-info small mb-0">
                            <i class="bi bi-info-circle"></i>
                            <strong>Recordatorio:</strong> El total de los pagos debe ser igual al total de la factura.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- JavaScript modular -->
<script>
// ============================================
// VARIABLES GLOBALES
// ============================================
let productos = [];
let productoIndex = 0;

// ============================================
// INICIALIZACIÓN
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    inicializarEventos();
});

// ============================================
// CONFIGURAR EVENTOS
// ============================================
function inicializarEventos() {
    // Botón agregar producto
    document.getElementById('btnAgregar').addEventListener('click', agregarProducto);

    // Inputs de pago
    document.querySelectorAll('.pago-input').forEach(input => {
        input.addEventListener('input', calcularDiferencia);
    });

    // Submit del formulario
    document.getElementById('facturaForm').addEventListener('submit', validarFormulario);
}

// ============================================
// AGREGAR PRODUCTO A LA FACTURA
// ============================================
function agregarProducto() {
    const productoSelect = document.getElementById('producto_select');
    const cantidadInput = document.getElementById('cantidad_input');

    // Validar que se haya seleccionado un producto
    if (!productoSelect.value) {
        alert('Por favor seleccione un producto');
        return;
    }

    // Validar cantidad
    const cantidad = parseInt(cantidadInput.value);
    if (cantidad < 1) {
        alert('La cantidad debe ser mayor a 0');
        return;
    }

    // Obtener datos del producto
    const option = productoSelect.options[productoSelect.selectedIndex];
    const productoId = productoSelect.value;
    const nombre = option.dataset.nombre;
    const precio = parseFloat(option.dataset.precio);
    const descuento = parseFloat(option.dataset.descuento) || 0;

    // Calcular precio con descuento
    const precioConDescuento = precio * (1 - descuento / 100);
    const subtotal = cantidad * precioConDescuento;

    // Crear objeto producto
    const producto = {
        index: productoIndex++,
        id: productoId,
        nombre: nombre,
        cantidad: cantidad,
        precio: precioConDescuento,
        descuento: descuento,
        subtotal: subtotal
    };

    // Agregar a la lista
    productos.push(producto);

    // Agregar fila a la tabla
    agregarFilaProducto(producto);

    // Recalcular totales
    calcularTotales();

    // Limpiar inputs
    productoSelect.value = '';
    cantidadInput.value = 1;
}

// ============================================
// AGREGAR FILA A LA TABLA
// ============================================
function agregarFilaProducto(producto) {
    const tbody = document.getElementById('productosBody');

    // Si es el primer producto, limpiar mensaje
    if (productos.length === 1) {
        tbody.innerHTML = '';
    }

    // Crear fila
    const tr = document.createElement('tr');
    tr.id = `producto-${producto.index}`;
    tr.innerHTML = `
        <td>
            ${producto.nombre}
            <input type="hidden" name="productos[${producto.index}][producto_id]" value="${producto.id}">
        </td>
        <td>
            ${producto.cantidad}
            <input type="hidden" name="productos[${producto.index}][cantidad]" value="${producto.cantidad}">
        </td>
        <td>
            Q${producto.precio.toFixed(2)}
            <input type="hidden" name="productos[${producto.index}][precio_unitario]" value="${producto.precio}">
        </td>
        <td><strong>Q${producto.subtotal.toFixed(2)}</strong></td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="eliminarProducto(${producto.index})">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;

    tbody.appendChild(tr);
}

// ============================================
// ELIMINAR PRODUCTO
// ============================================
function eliminarProducto(index) {
    // Eliminar del array
    productos = productos.filter(p => p.index !== index);

    // Eliminar fila de la tabla
    const fila = document.getElementById(`producto-${index}`);
    fila.remove();

    // Si no quedan productos, mostrar mensaje
    if (productos.length === 0) {
        const tbody = document.getElementById('productosBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    <i class="bi bi-inbox"></i> No hay productos agregados
                </td>
            </tr>
        `;
    }

    // Recalcular totales
    calcularTotales();
}

// ============================================
// CALCULAR TOTALES
// ============================================
function calcularTotales() {
    let subtotal = 0;
    let descuentos = 0;

    productos.forEach(p => {
        subtotal += p.cantidad * p.precio / (1 - p.descuento / 100);
        descuentos += (p.cantidad * p.precio / (1 - p.descuento / 100)) * (p.descuento / 100);
    });

    const total = subtotal - descuentos;

    // Actualizar UI
    document.getElementById('subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('descuentos').textContent = descuentos.toFixed(2);
    document.getElementById('total').textContent = total.toFixed(2);
    document.getElementById('totalPagar').textContent = total.toFixed(2);

    // Recalcular diferencia
    calcularDiferencia();
}

// ============================================
// CALCULAR DIFERENCIA DE PAGOS
// ============================================
function calcularDiferencia() {
    const efectivo = parseFloat(document.getElementById('pago_efectivo').value) || 0;
    const cheque = parseFloat(document.getElementById('pago_cheque').value) || 0;
    const tarjeta = parseFloat(document.getElementById('pago_tarjeta').value) || 0;

    const totalPagos = efectivo + cheque + tarjeta;
    const total = parseFloat(document.getElementById('total').textContent) || 0;
    const diferencia = total - totalPagos;

    // Actualizar UI
    const diferenciaSpan = document.getElementById('diferencia');
    diferenciaSpan.textContent = Math.abs(diferencia).toFixed(2);

    // Cambiar color según diferencia
    if (Math.abs(diferencia) < 0.01) {
        diferenciaSpan.classList.remove('text-danger');
        diferenciaSpan.classList.add('text-success');
    } else {
        diferenciaSpan.classList.remove('text-success');
        diferenciaSpan.classList.add('text-danger');
    }
}

// ============================================
// VALIDAR FORMULARIO ANTES DE ENVIAR
// ============================================
function validarFormulario(e) {
    // Validar que haya productos
    if (productos.length === 0) {
        e.preventDefault();
        alert('Debe agregar al menos un producto a la factura');
        return false;
    }

    // Validar que se haya seleccionado tienda
    if (!document.getElementById('tienda_id').value) {
        e.preventDefault();
        alert('Debe seleccionar una tienda');
        return false;
    }

    // Validar que los pagos coincidan con el total
    const efectivo = parseFloat(document.getElementById('pago_efectivo').value) || 0;
    const cheque = parseFloat(document.getElementById('pago_cheque').value) || 0;
    const tarjeta = parseFloat(document.getElementById('pago_tarjeta').value) || 0;
    const totalPagos = efectivo + cheque + tarjeta;
    const total = parseFloat(document.getElementById('total').textContent) || 0;

    if (Math.abs(totalPagos - total) > 0.01) {
        e.preventDefault();
        alert('El total de los pagos debe ser igual al total de la factura');
        return false;
    }

    // Deshabilitar botón para evitar doble submit
    document.getElementById('btnGuardar').disabled = true;
    return true;
}
</script>
@endsection
