@extends('layouts.app')

@section('title', 'Tiendas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-shop"></i> Tiendas
    </h1>
    <a href="{{ route('tiendas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nueva Tienda
    </a>
</div>

<div class="row">
    @foreach($tiendas as $tienda)
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shop-window text-primary"></i> {{ $tienda->nombre }}
                    </h5>
                    @if($tienda->activo)
                        <span class="badge bg-success">Activa</span>
                    @else
                        <span class="badge bg-secondary">Inactiva</span>
                    @endif
                </div>

                <p class="text-muted mb-2">
                    <i class="bi bi-geo-alt"></i> {{ $tienda->direccion }}
                </p>

                @if($tienda->telefono)
                <p class="mb-2">
                    <i class="bi bi-telephone"></i> {{ $tienda->telefono }}
                </p>
                @endif

                @if($tienda->email)
                <p class="mb-2">
                    <i class="bi bi-envelope"></i> {{ $tienda->email }}
                </p>
                @endif

                <div class="row mt-3">
                    <div class="col-6">
                        <small class="text-muted">Usuarios</small>
                        <h6>{{ $tienda->users_count ?? 0 }}</h6>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Productos</small>
                        <h6>{{ $tienda->inventarios_count ?? 0 }}</h6>
                    </div>
                </div>

                <div class="mt-3 text-end">
                    <a href="{{ route('tiendas.show', $tienda->id) }}" class="btn btn-sm btn-info">
                        <i class="bi bi-eye"></i> Ver
                    </a>
                    <a href="{{ route('tiendas.edit', $tienda->id) }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-3">
    {{ $tiendas->links() }}
</div>
@endsection