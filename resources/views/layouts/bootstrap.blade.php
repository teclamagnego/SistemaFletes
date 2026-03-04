<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'DobleG') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        body {
            background-color: #f4f6f9;
        }

        .navbar-brand {
            font-weight: 700;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);
        }

        .table th {
            background-color: #f8f9fa;
        }

        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/dashboard') }}">
                <i class="bi bi-truck"></i> DobleG
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    @can('agencies.index')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('agencies.*') ? 'active' : '' }}"
                            href="{{ route('agencies.index') }}">
                            <i class="bi bi-shop"></i> Agencias
                        </a>
                    </li>
                    @endcan
                    @can('shipments.index')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shipments.*') ? 'active' : '' }}"
                            href="{{ route('shipments.index') }}">
                            <i class="bi bi-file-earmark-text"></i> Envíos
                        </a>
                    </li>
                    @endcan
                    @can('shipments.index')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shipments.consolidation') ? 'active' : '' }}"
                            href="{{ route('shipments.consolidation') }}">
                            <i class="bi bi-boxes"></i> Consolidación
                        </a>
                    </li>
                    @endcan
                    @can('clientes.index')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}"
                            href="{{ route('clientes.index') }}">
                            <i class="bi bi-people"></i> Clientes
                        </a>
                    </li>
                    @endcan
                    @can('rubros.index')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('rubros.*') ? 'active' : '' }}"
                            href="{{ route('rubros.index') }}">
                            <i class="bi bi-tags"></i> Rubros
                        </a>
                    </li>
                    @endcan
                    @can('proveedores.index')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}"
                            href="{{ route('proveedores.index') }}">
                            <i class="bi bi-building"></i> Proveedores
                        </a>
                    </li>
                    @endcan
                    @can('articulos.index')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('articulos.*') ? 'active' : '' }}"
                            href="{{ route('articulos.index') }}">
                            <i class="bi bi-box-seam"></i> Artículos
                        </a>
                    </li>
                    @endcan
                    @can('carriers.index')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('carriers.*') ? 'active' : '' }}"
                            href="{{ route('carriers.index') }}">
                            <i class="bi bi-truck"></i> Transportistas
                        </a>
                    </li>
                    @endcan
                    @can('users.index')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'active' : '' }}"
                            href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear"></i> Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('users.index') }}"><i
                                        class="bi bi-person-gear"></i> Usuarios</a></li>
                            @can('roles.index')
                            <li><a class="dropdown-item" href="{{ route('roles.index') }}"><i
                                        class="bi bi-shield-lock"></i> Roles</a></li>
                            @endcan
                        </ul>
                    </li>
                    @endcan
                    @endauth
                </ul>
                <ul class="navbar-nav">
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i
                                        class="bi bi-pencil-square"></i> Perfil</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right"></i>
                                        Cerrar Sesión</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @stack('scripts')
</body>

</html>