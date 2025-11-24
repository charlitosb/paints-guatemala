@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-speedometer2"></i> Dashboard
    </h1>
    <span class="badge bg-primary">{{ auth()->user()->role }}</span>
</div>

<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card" style="border-left-color: #0d6efd;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Productos</h6>
                        <h2 class="mb-0">{{ $totalProductos }}</h2>
                    </div>
                    <div class="text-primary" style="font-size: 2.5rem;">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card" style="border-left-color: #198754;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Clientes</h6>
                        <h2 class="mb-0">{{ $totalClientes }}</h2>
                    </div>
                    <div class="text-success" style="font-size: 2.5rem;">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card" style="border-left-color: #dc3545;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Bajo Stock</h6>
                        <h2 class="mb-0">{{ $bajosStock }}</h2>
                    </div>
                    <div class="text-danger" style="font-size: 2.5rem;">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card" style="border-left-color: #ffc107;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Ventas del Mes</h6>
                        <h2 class="mb-0">Q{{ number_format($ventasMesActual, 2) }}</h2>
                    </div>
                    <div class="text-warning" style="font-size: 2.5rem;">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Productos Más Vendidos -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-graph-up"></i> Productos Más Vendidos
            </div>
            <div class="card-body">
                @if($productosMasVendidos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-end">Vendido</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productosMasVendidos as $producto)
                            <tr>
                                <td>{{ $producto->nombre }}</td>
                                <td class="text-end">
                                    <span class="badge bg-success">{{ $producto->total_vendido }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0">No hay datos disponibles</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Últimas Facturas -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-receipt"></i> Últimas Facturas
            </div>
            <div class="card-body">
                @if($ultimasFacturas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Factura</th>
                                <th>Cliente</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimasFacturas as $factura)
                            <tr>
                                <td>
                                    <a href="{{ route('facturas.show', $factura->id) }}">
                                        {{ $factura->numero_factura }}
                                    </a>
                                </td>
                                <td>{{ $factura->cliente->nombre ?? 'C/F' }}</td>
                                <td class="text-end">Q{{ number_format($factura->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0">No hay facturas registradas</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection