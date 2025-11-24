@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="container">
    <div class="row justify-content-center" style="min-height: 80vh; align-items: center;">
        <div class="col-md-5">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-palette-fill text-primary" style="font-size: 3rem;"></i>
                        <h2 class="mt-3">Paints Guatemala</h2>
                        <p class="text-muted">Iniciar Sesión</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Recordarme
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            <a href="{{ route('register') }}" class="text-decoration-none">
                                ¿No tienes cuenta? Regístrate aquí
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3 text-muted">
                <small>
                    <strong>Usuarios de prueba:</strong><br>
                    admin@paints.com / digitador@paints.com / cajero@paints.com<br>
                    Contraseña: password123
                </small>
            </div>
        </div>
    </div>
</div>
@endsection