@extends('layouts.app')

@section('title', 'Tiendas')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-shop"></i> Tiendas / Sucursales</h2>
        @if(auth()->user()->rol === 'admin')
        <a href="{{ route('tiendas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva Tienda
        </a>
        @endif
    </div>

    <!-- Mensajes -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Grid de Tiendas -->
    <div class="row">
        @forelse($tiendas as $tienda)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 {{ $tienda->activo ? '' : 'border-danger' }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-shop"></i> {{ $tienda->nombre }}
                    </h5>
                    @if($tienda->activo)
                        <span class="badge bg-success">Activa</span>
                    @else
                        <span class="badge bg-danger">Inactiva</span>
                    @endif
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <i class="bi bi-geo-alt text-muted"></i> 
                        {{ $tienda->direccion }}
                    </p>
                    
                    @if($tienda->telefono)
                    <p class="mb-2">
                        <i class="bi bi-telephone text-muted"></i> 
                        {{ $tienda->telefono }}
                    </p>
                    @endif
                    
                    @if($tienda->email)
                    <p class="mb-2">
                        <i class="bi bi-envelope text-muted"></i> 
                        {{ $tienda->email }}
                    </p>
                    @endif

                    <hr>

                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <h4 class="mb-0 text-primary">{{ $tienda->inventarios_count ?? 0 }}</h4>
                                <small class="text-muted">Productos</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                @if($tienda->tieneCoordenadas())
                                    <h4 class="mb-0 text-success"><i class="bi bi-geo-alt-fill"></i></h4>
                                    <small class="text-muted">GPS</small>
                                @else
                                    <h4 class="mb-0 text-secondary"><i class="bi bi-geo-alt"></i></h4>
                                    <small class="text-muted">Sin GPS</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tiendas.show', $tienda) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> Ver
                        </a>
                        @if(auth()->user()->rol === 'admin')
                        <a href="{{ route('tiendas.edit', $tienda) }}" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i> No hay tiendas registradas
            </div>
        </div>
        @endforelse
    </div>

    <!-- Paginación -->
    @if($tiendas->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $tiendas->links() }}
    </div>
    @endif
</div>
@endsection