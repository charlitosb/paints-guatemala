@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-graph-up"></i> Reportes
    </h1>
    <p class="text-muted">Selecciona el tipo de reporte que deseas generar</p>
</div>

<div class="row">
    <!-- Reporte de Ventas -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-currency-dollar text-success"></i> Reporte de Ventas
                </h5>
                <p class="card-text">Consulta las ventas realizadas por rango de fechas, incluyendo totales por tipo de pago.</p>
                <a href="{{ route('reportes.ventas') }}" class="btn btn-primary">
                    <i class="bi bi-file-earmark-bar-graph"></i> Generar Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- Productos Más Vendidos -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-trophy text-warning"></i> Productos Más Vendidos
                </h5>
                <p class="card-text">Listado de productos más vendidos por dinero generado o por cantidad vendida.</p>
                <a href="{{ route('reportes.productos-mas-vendidos') }}" class="btn btn-primary">
                    <i class="bi bi-graph-up-arrow"></i> Generar Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- Inventario General -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-boxes text-primary"></i> Inventario General
                </h5>
                <p class="card-text">Estado actual del inventario en todas las tiendas con valorización.</p>
                <a href="{{ route('reportes.inventario') }}" class="btn btn-primary">
                    <i class="bi bi-clipboard-data"></i> Generar Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- Productos Bajo Stock -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-exclamation-triangle text-danger"></i> Productos Bajo Stock
                </h5>
                <p class="card-text">Productos con existencias por debajo del stock mínimo permitido.</p>
                <a href="{{ route('reportes.bajo-stock') }}" class="btn btn-primary">
                    <i class="bi bi-bell"></i> Generar Reporte
                </a>
            </div>
        </div>
    </div>
</div>
@endsection