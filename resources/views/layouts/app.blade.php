<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Paints Guatemala')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
        }
        
        body {
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        
        .navbar {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .navbar-dark .navbar-nav .nav-link {
            color: rgba(255,255,255,.85);
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.2s;
        }
        
        .navbar-dark .navbar-nav .nav-link:hover,
        .navbar-dark .navbar-nav .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,.15);
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
        }
        
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            padding: 1rem 1.25rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #0a58ca 0%, #0846a6 100%);
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .badge {
            padding: 0.35em 0.65em;
        }
        
        main {
            min-height: calc(100vh - 56px);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="bi bi-paint-bucket"></i> PAINTS
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                @auth
                <ul class="navbar-nav me-auto">
                    
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>

                    <!-- PRODUCTOS -->
                    @if(auth()->user()->rol !== 'cliente')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('productos*') ? 'active' : '' }}" href="{{ route('productos.index') }}">
                            <i class="bi bi-box"></i> Productos
                        </a>
                    </li>

                    <!-- CATEGORÍAS -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('categorias*') ? 'active' : '' }}" href="{{ route('categorias.index') }}">
                            <i class="bi bi-tags"></i> Categorías
                        </a>
                    </li>

                    <!-- INVENTARIOS -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('inventarios*') ? 'active' : '' }}" href="{{ route('inventarios.index') }}">
                            <i class="bi bi-stack"></i> Inventarios
                        </a>
                    </li>
                    @endif

                    <!-- INGRESOS -->
                    @if(in_array(auth()->user()->rol, ['digitador', 'admin', 'gerente']))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('ingresos.index') }}">
                            <i class="bi bi-box-arrow-in-down"></i> Ingresos
                        </a>
                    </li>
                    @endif

                    <!-- FACTURAS -->
                    @if(in_array(auth()->user()->rol, ['cajero', 'admin', 'gerente']))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('facturas*') ? 'active' : '' }}" href="{{ route('facturas.index') }}">
                            <i class="bi bi-receipt"></i> Facturas
                        </a>
                    </li>
                    @endif

                    <!-- PROVEEDORES -->
                    @if(in_array(auth()->user()->rol, ['admin', 'gerente', 'digitador']))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('proveedores*') ? 'active' : '' }}" href="{{ route('proveedores.index') }}">
                            <i class="bi bi-truck"></i> Proveedores
                        </a>
                    </li>
                    @endif

                    <!-- TIENDAS -->
                    @if(in_array(auth()->user()->rol, ['admin', 'gerente']))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('tiendas*') ? 'active' : '' }}" href="{{ route('tiendas.index') }}">
                            <i class="bi bi-shop"></i> Tiendas
                        </a>
                    </li>
                    @endif

                    <!-- USUARIOS -->
                    @if(auth()->user()->rol === 'admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('usuarios*') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                            <i class="bi bi-people"></i> Usuarios
                        </a>
                    </li>
                    @endif

                </ul>

                <!-- Usuario -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                            <span class="badge bg-light text-dark ms-1">{{ ucfirst(auth()->user()->rol) }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="dropdown-item-text">
                                <div class="text-muted small">{{ auth()->user()->email }}</div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid">
        <main class="py-4">
            <!-- Alertas -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>