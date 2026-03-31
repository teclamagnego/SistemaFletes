@extends('layouts.bootstrap')
@section('content')
<h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard - Resumen de Envíos</h2>

@php
$statuses = \App\Models\ShipmentStatus::all();
$statusColors = [
'Admitido' => 'info',
'En Deposito Origen' => 'primary',
'En Transito' => 'warning text-dark',
'En Deposito Destino' => 'dark',
'Entregado' => 'success',
'Anulado' => 'danger',
];
@endphp

<div class="row g-3 mb-4">
    <!-- Mes Anterior (Izquierda) -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100 bg-light">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted small fw-bold mb-1">Mes Anterior (Tránsito/Entregado)</h6>
                        <h2 class="fw-bold mb-0 text-dark">{{ $lastMonthEnvoys }}</h2>
                    </div>
                    <div class="bg-secondary bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-calendar-event text-secondary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Mes Actual (Derecha) -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100 bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-white-50 small fw-bold mb-1">Mes Actual (Tránsito/Entregado)</h6>
                        <h2 class="fw-bold mb-0">{{ $currentMonthEnvoys }}</h2>
                    </div>
                    <div class="bg-white bg-opacity-20 p-3 rounded-circle">
                        <i class="bi bi-calendar-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Año Pasado (Izquierda) -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100 bg-dark text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-white-50 small fw-bold mb-1">Total Envíos Año Pasado</h6>
                        <h3 class="fw-bold mb-0">{{ $lastYearEnvoys }}</h3>
                    </div>
                    <i class="bi bi-graph-up-arrow opacity-25 fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Este Año (Derecha) -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100 bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-white-50 small fw-bold mb-1">Total Envíos Este Año</h6>
                        <h3 class="fw-bold mb-0">{{ $currentYearEnvoys }}</h3>
                    </div>
                    <i class="bi bi-activity opacity-50 fs-1"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<h5 class="mb-3 fw-bold mt-5"><i class="bi bi-funnel"></i> Estado Actual de Todas las Guías</h5>
<div class="row g-3 mb-4">
    @foreach($statuses as $status)
    <div class="col-md-4 col-lg-2">
        <div class="card shadow-sm border-0 h-100 bg-{{ $statusColors[$status->name] ?? 'secondary' }} text-white">
            <div class="card-body text-center d-flex flex-column justify-content-center py-4">
                <i class="bi bi-box-seam mb-2" style="font-size: 1.5rem; opacity: 0.7;"></i>
                <h6 class="card-subtitle mb-1 small opacity-75 text-uppercase fw-bold">{{ $status->name }}</h6>
                <h2 class="card-title mb-0 fw-bold">{{ \App\Models\Shipment::where('status_id', $status->id)->count() }}
                </h2>
            </div>
            <div class="card-footer bg-black bg-opacity-10 border-0 py-2">
                <a href="{{ route('shipments.index', ['status_id' => $status->id]) }}"
                    class="text-white text-decoration-none small d-flex justify-content-between align-items-center">
                    <span>Ver lista</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card border-0 shadow-sm mt-3">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="bg-primary text-white rounded-circle p-3 me-3">
                <i class="bi bi-person-check fs-4"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold">Bienvenido, {{ Auth::user()->name }}</h5>
                <p class="text-muted mb-0 small">Has iniciado sesión como: <span
                        class="badge bg-light text-dark border">{{ Auth::user()->getRoleNames()->implode(', ') ?: 'Sin
                        Rol' }}</span></p>
            </div>
        </div>
    </div>
</div>
@endsection