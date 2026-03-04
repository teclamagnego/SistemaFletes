@extends('layouts.bootstrap')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Detalle de Guía: {{ $shipment->tracking_number }}</h2>
        <div>
            <span class="badge {{ $shipment->status == 'Admitted' ? 'bg-info' : 'bg-primary' }} fs-6">{{
                $shipment->status }}</span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><strong>Información General</strong></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Remitente:</strong> {{ $shipment->sender->nombre }} {{
                                $shipment->sender->apellido }}</p>
                            <p><strong>Destinatario:</strong> {{ $shipment->receiver->nombre }} {{
                                $shipment->receiver->apellido }}</p>
                            <p><strong>Origen:</strong> {{ $shipment->originAgency->name }}</p>
                            <p><strong>Destino:</strong> {{ $shipment->destinationAgency->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Fecha Emisión:</strong> {{ $shipment->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Modo Pago:</strong> {{ $shipment->payment_mode == 'PP' ? 'Pagado en Origen' :
                                'Pago en Destino' }}</p>
                            <p><strong>Flete Total:</strong> ${{ number_format($shipment->total_flete, 2) }}</p>
                            <p><strong>Comisión Agencia:</strong> ${{ number_format($shipment->comision_monto, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><strong>Detalle de Items</strong></div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Cant.</th>
                                <th>Descripción</th>
                                <th>Peso</th>
                                <th>Dimensiones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shipment->items as $item)
                            <tr>
                                <td>{{ $item->cantidad }}</td>
                                <td>{{ $item->descripcion }}</td>
                                <td>{{ $item->peso ? $item->peso . ' kg' : '-' }}</td>
                                <td>{{ $item->dimensiones ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header"><strong>Historial de Estados</strong></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($shipment->logs as $log)
                        <li class="list-group-item ps-0">
                            <div><small class="text-muted">{{ $log->created_at->format('d/m/Y H:i') }}</small></div>
                            <strong>{{ $log->status_to }}</strong>
                            <div class="small text-muted">Por: {{ $log->user->name }}</div>
                            @if($log->notas)<div class="small mt-1 fst-italic">"{{ $log->notas }}"</div>@endif
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-5">
        <a href="{{ route('shipments.index') }}" class="btn btn-secondary">Volver al Listado</a>
    </div>
</div>
@endsection