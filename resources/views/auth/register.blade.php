@extends('layouts.app')

@section('title', 'Registrarse')

@section('content')
<div class="container">
    <div class="row justify-content-center py-5">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus-fill text-primary" style="font-size: 3rem;"></i>
                        <h2 class="mt-3">Crear Cuenta</h2>
                        <p class="text-muted">Regístrate en Paints Guatemala</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Rol *</label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="">Seleccione un rol</option>
                                <option value="digitador" {{ old('role') == 'digitador' ? 'selected' : '' }}>Digitador</option>
                                <option value="cajero" {{ old('role') == 'cajero' ? 'selected' : '' }}>Cajero</option>
                                <option value="gerente" {{ old('role') == 'gerente' ? 'selected' : '' }}>Gerente</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tienda_id" class="form-label">Tienda</label>
                            <select class="form-select @error('tienda_id') is-invalid @enderror" id="tienda_id" name="tienda_id">
                                <option value="">Sin tienda asignada</option>
                                @foreach($tiendas as $tienda)
                                    <option value="{{ $tienda->id }}" {{ old('tienda_id') == $tienda->id ? 'selected' : '' }}>
                                        {{ $tienda->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tienda_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña *</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Contraseña *</label>
                            <input type="password" class="form-control" id="password_confirmation" 
                                   name="password_confirmation" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-person-check"></i> Registrarse
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                ¿Ya tienes cuenta? Inicia sesión aquí
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection