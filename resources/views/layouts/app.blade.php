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
        
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #fff;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
        }
        
        .sidebar .nav-link {
            color: #495057;
            padding: 0.75rem 1rem;
            border-radius: 0.25rem;
            margin: 0.25rem 0;
            transition: all 0.2s;
        }
        
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
            color: #0d6efd;
        }
        
        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
            width: 20px;
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
        
        .stat-card {
            border-left: 4px solid var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-palette-fill"></i> Paints Guatemala
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
{{--                                 <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person"></i> Mi Perfil
                                </a></li>
                                <li><hr class="dropdown-divider"></li> --}}
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            @auth
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar p-3">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        
                        @if(auth()->user()->hasAnyRole(['admin', 'digitador']))
                        <li class="nav-item mt-3">
                            <h6 class="text-muted px-3 mb-2">GESTIÓN</h6>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}" href="{{ route('proveedores.index') }}">
                                <i class="bi bi-truck"></i> Proveedores
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}" href="{{ route('clientes.index') }}">
                                <i class="bi bi-people"></i> Clientes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}" href="{{ route('categorias.index') }}">
                                <i class="bi bi-tags"></i> Categorías
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}" href="{{ route('productos.index') }}">
                                <i class="bi bi-box-seam"></i> Productos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('ingresos.*') ? 'active' : '' }}" href="{{ route('ingresos.index') }}">
                                <i class="bi bi-box-arrow-in-down"></i> Ingresos
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->user()->hasAnyRole(['admin', 'cajero']))
                        <li class="nav-item mt-3">
                            <h6 class="text-muted px-3 mb-2">VENTAS</h6>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('facturas.*') ? 'active' : '' }}" href="{{ route('facturas.index') }}">
                                <i class="bi bi-receipt"></i> Facturas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('cotizaciones.*') ? 'active' : '' }}" href="{{ route('cotizaciones.index') }}">
                                <i class="bi bi-file-earmark-text"></i> Cotizaciones
                            </a>
                        </li>
                        @endif
                        
                        <li class="nav-item mt-3">
                            <h6 class="text-muted px-3 mb-2">INVENTARIO</h6>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('inventarios.*') ? 'active' : '' }}" href="{{ route('inventarios.index') }}">
                                <i class="bi bi-clipboard-data"></i> Inventarios
                            </a>
                        </li>
                        
                        @if(auth()->user()->hasAnyRole(['admin', 'gerente']))
                        <li class="nav-item mt-3">
                            <h6 class="text-muted px-3 mb-2">REPORTES</h6>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}" href="{{ route('reportes.index') }}">
                                <i class="bi bi-graph-up"></i> Reportes
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->user()->isAdmin())
                        <li class="nav-item mt-3">
                            <h6 class="text-muted px-3 mb-2">ADMINISTRACIÓN</h6>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                                <i class="bi bi-person-badge"></i> Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tiendas.*') ? 'active' : '' }}" href="{{ route('tiendas.index') }}">
                                <i class="bi bi-shop"></i> Tiendas
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </nav>
            @endauth

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
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
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>