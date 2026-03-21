@extends('layouts.bootstrap')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('agencies.index') }}" class="text-muted text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i>Agencias
            </a>
            <h2 class="mb-0 mt-1">
                <i class="bi bi-clock-history text-primary me-2"></i>
                Historial de Envíos — <span class="text-primary">{{ $agency->nombre }}</span>
            </h2>
            <p class="text-muted mb-0 mt-1 small">
                <code>{{ $agency->codigo }}</code>
                @if($agency->localidad)
                &nbsp;·&nbsp; {{ $agency->localidad->nombre }}
                @endif
            </p>
        </div>
    </div>

    {{-- Stats cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="display-6 fw-bold text-primary">{{ $totalOrigen + $totalDestino }}</div>
                    <div class="small text-muted">Total envíos</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="display-6 fw-bold text-success">{{ $totalOrigen }}</div>
                    <div class="small text-muted">Como origen</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="display-6 fw-bold text-warning">{{ $totalDestino }}</div>
                    <div class="small text-muted">Como destino</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="display-6 fw-bold text-info">
                        {{ $shipments->total() }}
                    </div>
                    <div class="small text-muted">En esta vista</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs filtro --}}
    <ul class="nav nav-tabs mb-0" style="border-bottom: none;">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'all' ? 'active fw-semibold' : '' }}"
                href="{{ route('agencies.shipments', [$agency, 'tab' => 'all']) }}">
                <i class="bi bi-arrow-left-right me-1"></i>Todos
                <span class="badge bg-secondary ms-1">{{ $totalOrigen + $totalDestino }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'origin' ? 'active fw-semibold' : '' }}"
                href="{{ route('agencies.shipments', [$agency, 'tab' => 'origin']) }}">
                <i class="bi bi-box-arrow-up me-1 text-success"></i>Enviados (Origen)
                <span class="badge bg-success ms-1">{{ $totalOrigen }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'destination' ? 'active fw-semibold' : '' }}"
                href="{{ route('agencies.shipments', [$agency, 'tab' => 'destination']) }}">
                <i class="bi bi-box-arrow-in-down me-1 text-warning"></i>Recibidos (Destino)
                <span class="badge bg-warning text-dark ms-1">{{ $totalDestino }}</span>
            </a>
        </li>
    </ul>

    {{-- Tabla --}}
    <div class="card border-0 shadow-sm" style="border-top-left-radius: 0;">
        <div class="card-body p-0">
            @if($shipments->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                No hay envíos para mostrar en esta categoría.
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Guía</th>
                            <th>Fecha</th>
                            <th>Remitente</th>
                            <th>Destinatario</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Forma Pago</th>
                            <th class="text-end">Total</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shipments as $s)
                        <tr>
                            <td>
                                <a href="{{ route('shipments.show', $s) }}" class="fw-semibold text-decoration-none">
                                    {{ $s->tracking_number }}
                                </a>
                            </td>
                            <td class="text-muted small">{{ \Carbon\Carbon::parse($s->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $s->sender?->nombre_fantasia ?? '—' }}</td>
                            <td>{{ $s->receiver?->nombre_fantasia ?? '—' }}</td>
                            <td>
                                @if($s->origin_agency_id === $agency->id)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-house-fill me-1"></i>Esta agencia
                                </span>
                                @else
                                <span class="text-muted small">{{ $s->originAgency?->nombre ?? '—' }}</span>
                                @endif
                            </td>
                            <td>
                                @if($s->destination_agency_id === $agency->id)
                                <span
                                    class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                    <i class="bi bi-house-fill me-1"></i>Esta agencia
                                </span>
                                @else
                                <span class="text-muted small">{{ $s->destinationAgency?->nombre ?? '—' }}</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $s->formaPago?->nombre ?? '—' }}</td>
                            <td class="text-end fw-semibold">${{ number_format($s->total_flete, 2) }}</td>
                            <td>
                                @php
                                $statusColors = [
                                'Admitted' => 'info',
                                'In Transit' => 'primary',
                                'In Destination' => 'warning',
                                'Delivered' => 'success',
                                'Cancelled' => 'danger',
                                ];
                                $color = $s->status ? ($statusColors[$s->status->name] ?? 'secondary') : 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ $s->status ? $s->status->name : 'Desconocido' }}</span>
                            </td>
                            <td>
                                <a href="{{ route('shipments.show', $s) }}" class="btn btn-sm btn-outline-secondary"
                                    title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- Paginación --}}
    @if($shipments->hasPages())
    <div class="mt-3">
        {{ $shipments->links() }}
    </div>
    @endif

</div>
@endsection