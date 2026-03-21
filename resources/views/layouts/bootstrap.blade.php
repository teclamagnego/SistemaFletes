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
                        <a class="nav-link {{ request()->routeIs('shipments.*') && !request()->routeIs('shipments.consolidation') ? 'active' : '' }}"
                            href="{{ route('shipments.index') }}">
                            <i class="bi bi-file-earmark-text"></i> Guías
                        </a>
                    </li>
                    @endcan
                    @can('shipments.index')
                    @if(\App\Models\Carrier::where('activo', true)->count() > 1)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shipments.consolidation') ? 'active' : '' }}"
                            href="{{ route('shipments.consolidation') }}">
                            <i class="bi bi-boxes"></i> Consolidación
                        </a>
                    </li>
                    @endif
                    @endcan
                    @can('clientes.index')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}"
                            href="{{ route('clientes.index') }}">
                            <i class="bi bi-people"></i> Clientes
                        </a>
                    </li>
                    @endcan
                    @can('clientes.index')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('informes.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-file-earmark-bar-graph"></i> Informes
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('informes.saldos_clientes') }}"><i class="bi bi-person-lines-fill"></i> Saldos de Clientes</a></li>
                            <li><a class="dropdown-item" href="{{ route('informes.saldos_agencias') }}"><i class="bi bi-shop"></i> Saldos de Agencias</a></li>
                        </ul>
                    </li>
                    @endcan
                    @canany(['users.index', 'roles.index', 'formas_pago.index', 'empresas.index', 'sucursales.index', 'localidades.index', 'articulos.index', 'carriers.index'])
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('formas_pago.*') || request()->routeIs('empresas.*') || request()->routeIs('sucursales.*') || request()->routeIs('localidades.*') || request()->routeIs('articulos.*') || request()->routeIs('carriers.*') ? 'active' : '' }}"
                            href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear"></i> Configuraciones
                        </a>
                        <ul class="dropdown-menu">
                            @can('articulos.index')
                            <li><a class="dropdown-item {{ request()->routeIs('articulos.*') ? 'active' : '' }}"
                                    href="{{ route('articulos.index') }}"><i class="bi bi-box-seam"></i> Artículos</a>
                            </li>
                            @endcan
                            @can('carriers.index')
                            <li><a class="dropdown-item {{ request()->routeIs('carriers.*') ? 'active' : '' }}"
                                    href="{{ route('carriers.index') }}"><i class="bi bi-truck"></i> Transportistas</a>
                            </li>
                            @endcan
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            @can('users.index')
                            <li><a class="dropdown-item {{ request()->routeIs('users.*') ? 'active' : '' }}"
                                    href="{{ route('users.index') }}"><i class="bi bi-person-gear"></i> Usuarios</a></li>
                            @endcan
                            @can('roles.index')
                            <li><a class="dropdown-item {{ request()->routeIs('roles.*') ? 'active' : '' }}"
                                    href="{{ route('roles.index') }}"><i class="bi bi-shield-lock"></i> Roles</a></li>
                            @endcan
                            @can('localidades.index')
                            <li><a class="dropdown-item {{ request()->routeIs('localidades.*') ? 'active' : '' }}"
                                    href="{{ route('localidades.index') }}"><i class="bi bi-geo-alt"></i> Localidades</a>
                            </li>
                            @endcan
                            @can('formas_pago.index')
                            <li><a class="dropdown-item {{ request()->routeIs('formas_pago.*') ? 'active' : '' }}"
                                    href="{{ route('formas_pago.index') }}"><i class="bi bi-credit-card"></i> Formas de
                                    Pago</a></li>
                            @endcan
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item {{ request()->routeIs('empresas.*') ? 'active' : '' }}"
                                    href="{{ route('empresas.index') }}"><i class="bi bi-buildings"></i> Empresas</a>
                            </li>
                            <li><a class="dropdown-item {{ request()->routeIs('sucursales.*') ? 'active' : '' }}"
                                    href="{{ route('sucursales.index') }}"><i class="bi bi-shop"></i> Sucursales</a></li>

                        </ul>
                    </li>
                    @endcanany
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