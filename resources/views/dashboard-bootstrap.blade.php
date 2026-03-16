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

<div class="row">
    <div class="col-md-12 mb-3">
        <div class="card text-white bg-dark shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Total General de Envíos</h5>
                        <h2 class="fw-bold mb-0 display-4">{{ \App\Models\Shipment::count() }}</h2>
                    </div>
                    <i class="bi bi-truck" style="font-size: 4rem; opacity: 0.2;"></i>
                </div>
                <hr class="mt-3 mb-3 opacity-25">
                <div class="d-flex gap-3">
                    <a href="{{ route('shipments.index') }}" class="btn btn-outline-light btn-sm px-4">Ver todos los
                        envíos</a>
                    <a href="{{ route('shipments.create') }}" class="btn btn-light btn-sm px-4 text-dark fw-bold"><i
                            class="bi bi-plus-lg me-1"></i>Nuevo Envío</a>
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
                <p class="text-muted mb-0 small">Has iniciado sesión como: <span
                        class="badge bg-light text-dark border">{{ Auth::user()->getRoleNames()->implode(', ') ?: 'Sin
                        Rol' }}</span></p>
            </div>
        </div>
    </div>
</div>
@endsection