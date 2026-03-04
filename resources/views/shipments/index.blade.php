@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-file-earmark-text"></i> Guías de Carga</h2>
    @can('shipments.create')<a href="{{ route('shipments.create') }}" class="btn btn-primary"><i
            class="bi bi-plus-lg"></i> Nueva Guía</a>@endcan
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Guía #</th>
                    <th>Remitente</th>
                    <th>Destinatario</th>
                    <th>Agencia Origen</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shipments as $s)
                <tr>
                    <td><strong>{{ $s->tracking_number }}</strong></td>
                    <td>{{ $s->sender->nombre_fantasia }}</td>
                    <td>{{ $s->receiver->nombre_fantasia }}</td>
                    <td>{{ $s->originAgency->nombre }}</td>
                    <td>
                        @php
                        $badgeClass = match($s->status) {
                        'Admitted' => 'bg-info',
                        'In Office' => 'bg-primary',
                        'In Transit' => 'bg-warning',
                        'Delivered' => 'bg-success',
                        'Cancelled' => 'bg-danger',
                        default => 'bg-secondary'
                        };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $s->status }}</span>
                    </td>
                    <td>${{ number_format($s->total_flete, 2) }}</td>
                    <td>
                        <a href="{{ route('shipments.show', $s) }}" class="btn btn-sm btn-outline-primary"><i
                                class="bi bi-eye"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $shipments->links() }}</div>
@endsection