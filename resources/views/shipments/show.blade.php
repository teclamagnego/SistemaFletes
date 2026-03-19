@extends('layouts.bootstrap')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Detalle de Guía: {{ $shipment->tracking_number }}</h2>
        <div>
            @if($shipment->status_id == \App\Models\ShipmentStatus::ADMITTED)
            <form action="{{ route('shipments.receive', $shipment) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success me-2"><i class="bi bi-box-arrow-in-down"></i> Recibir en
                    Oficina</button>
            </form>
            @endif

            @if($shipment->status_id == \App\Models\ShipmentStatus::IN_TRANSIT)
            <form action="{{ route('shipments.arrive', $shipment) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning me-2"><i class="bi bi-geo-fill"></i> Marcar Arribo en
                    Destino</button>
            </form>
            @endif

            @if($shipment->status_id == \App\Models\ShipmentStatus::IN_DESTINATION)
            <form action="{{ route('shipments.deliver', $shipment) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-check-circle-fill"></i> Entregar al
                    Destinatario
                </button>
            </form>
            @endif

            <a href="{{ route('shipments.print', $shipment) }}" target="_blank" class="btn btn-outline-dark me-2">
                <i class="bi bi-printer"></i> Imprimir Guía
            </a>

            <span class="badge bg-{{ $shipment->status->color ?? 'secondary' }} fs-6">
                {{ $shipment->status->name ?? 'N/A' }}
            </span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white"><strong>Información General</strong></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <p><strong>Remitente:</strong> {{ $shipment->sender?->nombre_fantasia ?? 'N/A' }}</p>
                            <p><strong>Destinatario:</strong> {{ $shipment->receiver?->nombre_fantasia ?? 'N/A' }}</p>
                            <p><strong>Origen:</strong> {{ $shipment->originAgency?->nombre ?? 'N/A' }}</p>
                            <p><strong>Destino:</strong> {{ $shipment->destinationAgency?->nombre ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 ps-md-4">
                            <p><strong>Pagador:</strong>
                                @php
                                    $pagadorId = $shipment->cliente_id ?? $shipment->sender_id;
                                    $pagadorNombre = $shipment->cliente?->nombre_fantasia ?? $shipment->sender?->nombre_fantasia ?? 'N/A';
                                @endphp
                                @if($pagadorId)
                                    <a href="{{ route('clientes.history', $pagadorId) }}" class="text-decoration-none">
                                        <span class="badge bg-primary">
                                            <i class="bi bi-clock-history me-1"></i> {{ $pagadorNombre }}
                                        </span>
                                    </a>
                                @else
                                    <span class="badge bg-primary">{{ $pagadorNombre }}</span>
                                @endif
                            </p>
                            <p><strong>Forma Pago:</strong> {{ $shipment->formaPago?->nombre ?? '-' }}</p>
                            <p><strong>Fecha:</strong> {{ $shipment->fecha }}</p>
                            <p><strong>Dirección Entrega:</strong> {{ $shipment->direccion_entrega ?? '-' }}</p>
                            <p class="fs-5 text-primary"><strong>Flete Total:</strong> ${{
                                number_format($shipment->total_flete, 2) }}</p>
                        </div>
                    </div>
                    @if($shipment->notas)
                    <div class="mt-3 p-2 bg-light rounded border">
                        <strong>Notas:</strong> {{ $shipment->notas }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white"><strong>Detalle de Items</strong></div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Cant.</th>
                                <th>Artículo / Descripción</th>
                                <th class="text-end">P. Unitario</th>
                                <th class="text-end">Bonif. (%)</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shipment->items as $item)
                            <tr>
                                <td>{{ $item->cantidad }}</td>
                                <td>
                                    @if($item->articulo)
                                    <small class="text-muted d-block">{{ $item->articulo->codigo }}</small>
                                    @endif
                                    {{ $item->descripcion }}
                                </td>
                                <td class="text-end">${{ number_format($item->precio_unitario, 2) }}</td>
                                <td class="text-end">{{ number_format($item->bonificacion, 2) }}%</td>
                                <td class="text-end fw-bold">${{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="4" class="text-end fw-bold">TOTAL:</td>
                                <td class="text-end fw-bold text-primary">${{ number_format($shipment->total_flete, 2)
                                    }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white"><strong>Historial de Estados</strong></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($shipment->logs as $log)
                        <li class="list-group-item ps-0 border-0 mb-3 pb-0">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle me-2" style="width: 10px; height: 10px;"></div>
                                <small class="text-muted">{{ $log->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                            <div class="ms-3 border-start ps-3 pb-2">
                                <strong>{{ $log->status_to }}</strong>
                                <div class="small text-muted">Por: {{ $log->user->name }}</div>
                                @if($log->notas)<div class="small mt-1 text-secondary">"{{ $log->notas }}"</div>@endif
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-5">
        <a href="{{ route('shipments.index') }}" class="btn btn-secondary px-4">Volver al Listado</a>
    </div>
</div>
@endsection