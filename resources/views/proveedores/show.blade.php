@extends('layouts.app')

@section('title', 'Detalle del Proveedor')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-truck"></i> Detalle del Proveedor
            </h1>
            <p class="text-muted">Información completa del proveedor</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <a href="{{ route('proveedores.edit', $proveedor->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-circle"></i> {{ $proveedor->nombre }}
                    @if($proveedor->activo)
                        <span class="badge bg-success float-end">Activo</span>
                    @else
                        <span class="badge bg-secondary float-end">Inactivo</span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Información General</h6>
                        
                        <div class="mb-3">
                            <label class="text-muted small">Nombre:</label>
                            <p class="mb-0"><strong>{{ $proveedor->nombre }}</strong></p>
                        </div>

                        @if($proveedor->empresa)
                        <div class="mb-3">
                            <label class="text-muted small">Empresa:</label>
                            <p class="mb-0">{{ $proveedor->empresa }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Información de Contacto</h6>
                        
                        @if($proveedor->telefono)
                        <div class="mb-3">
                            <label class="text-muted small">Teléfono:</label>
                            <p class="mb-0">
                                <i class="bi bi-telephone"></i> {{ $proveedor->telefono }}
                            </p>
                        </div>
                        @endif

                        @if($proveedor->email)
                        <div class="mb-3">
                            <label class="text-muted small">Email:</label>
                            <p class="mb-0">
                                <i class="bi bi-envelope"></i> 
                                <a href="mailto:{{ $proveedor->email }}">{{ $proveedor->email }}</a>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                @if($proveedor->direccion)
                <div class="mb-3">
                    <h6 class="text-muted">Dirección:</h6>
                    <p class="mb-0">
                        <i class="bi bi-geo-alt"></i> {{ $proveedor->direccion }}
                    </p>
                </div>
                @endif

                <hr>

                <div class="row">
    @if($proveedor->created_at)
    <div class="col-md-6">
        <p class="small text-muted mb-0">
            <i class="bi bi-calendar-plus"></i> 
            <strong>Registrado:</strong> {{ $proveedor->created_at->format('d/m/Y H:i') }}
        </p>
    </div>
    @endif
    
    @if($proveedor->updated_at)
    <div class="col-md-6">
        <p class="small text-muted mb-0">
            <i class="bi bi-calendar-check"></i> 
            <strong>Última actualización:</strong> {{ $proveedor->updated_at->format('d/m/Y H:i') }}
        </p>
    </div>
    @endif
</div>
            </div>
        </div>

        <!-- Historial de Ingresos -->
        @if($proveedor->ingresos && $proveedor->ingresos->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-box-arrow-in-down"></i> Últimos Ingresos de Productos
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No. Ingreso</th>
                                <th>Fecha</th>
                                <th>Tienda</th>
                                <th class="text-end">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proveedor->ingresos->take(10) as $ingreso)
                            <tr>
                                <td><strong>{{ $ingreso->numero_ingreso }}</strong></td>
                                <td>{{ $ingreso->fecha_ingreso->format('d/m/Y') }}</td>
                                <td>{{ $ingreso->tienda->nombre }}</td>
                                <td class="text-end">Q{{ number_format($ingreso->total, 2) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('ingresos.show', $ingreso->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($proveedor->ingresos->count() > 10)
                <p class="text-muted small mb-0 mt-2">
                    Mostrando los últimos 10 ingresos de {{ $proveedor->ingresos->count() }} totales.
                </p>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Estadísticas -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-graph-up"></i> Estadísticas
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Total de Ingresos:</label>
                    <h4 class="mb-0">{{ $proveedor->ingresos->count() ?? 0 }}</h4>
                </div>

                @if($proveedor->ingresos && $proveedor->ingresos->count() > 0)
                <div class="mb-3">
                    <label class="text-muted small">Monto Total Comprado:</label>
                    <h4 class="mb-0 text-success">
                        Q{{ number_format($proveedor->ingresos->sum('total'), 2) }}
                    </h4>
                </div>

                <div class="mb-3">
                    <label class="text-muted small">Último Ingreso:</label>
                    <p class="mb-0">{{ $proveedor->ingresos->first()->fecha_ingreso->format('d/m/Y') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Acciones -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-gear"></i> Acciones
                </h6>
            </div>
            <div class="card-body">
                <a href="{{ route('proveedores.edit', $proveedor->id) }}" class="btn btn-warning w-100 mb-2">
                    <i class="bi bi-pencil"></i> Editar Proveedor
                </a>
                
                <form action="{{ route('proveedores.destroy', $proveedor->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este proveedor?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-trash"></i> Eliminar Proveedor
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection