@extends('layouts.bootstrap')
@section('content')
<h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard</h2>
<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-primary shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Clientes</h5>
                        <h2 class="fw-bold">{{ \App\Models\Cliente::count() }}</h2>
                    </div>
                    <i class="bi bi-people" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
                <hr class="mt-2 mb-2 opacity-25">
                <a href="{{ route('clientes.index') }}" class="text-white text-decoration-none"><small>Ver todos los clientes →</small></a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-success shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Agencias</h5>
                        <h2 class="fw-bold">{{ \App\Models\Agency::count() }}</h2>
                    </div>
                    <i class="bi bi-shop" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
                <hr class="mt-2 mb-2 opacity-25">
                <a href="{{ route('agencies.index') }}" class="text-white text-decoration-none"><small>Gestionar agencias →</small></a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-info shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Envíos (Guías)</h5>
                        <h2 class="fw-bold">{{ \App\Models\Shipment::count() }}</h2>
                    </div>
                    <i class="bi bi-file-earmark-text" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
                <hr class="mt-2 mb-2 opacity-25">
                <a href="{{ route('shipments.index') }}" class="text-white text-decoration-none"><small>Ver todas las guías →</small></a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-6 mb-3">
        <div class="card bg-white shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-light p-3 rounded">
                        <i class="bi bi-box-seam text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Artículos</h6>
                        <h4 class="mb-0 fw-bold">{{ \App\Models\Articulo::count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card bg-white shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-light p-3 rounded">
                        <i class="bi bi-truck text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Transportistas</h6>
                        <h4 class="mb-0 fw-bold">{{ \App\Models\Carrier::count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mt-3">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="bg-primary text-white rounded-circle p-3 me-3">
                <i class="bi bi-person-check fs-4"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold">Bienvenido, {{ Auth::user()->name }}</h5>
                <p class="text-muted mb-0 small">Has iniciado sesión como: <span class="badge bg-light text-dark border">{{ Auth::user()->getRoleNames()->implode(', ') ?: 'Sin Rol' }}</span></p>
            </div>
        </div>
    </div>
</div>
@endsection