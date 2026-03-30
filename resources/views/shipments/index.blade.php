@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-file-earmark-text"></i> Guías de Carga</h2>
    @can('shipments.create')<a href="{{ route('shipments.create') }}" class="btn btn-primary"><i
            class="bi bi-plus-lg"></i> Nueva Guía</a>@endcan
</div>

<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('shipments.index') }}" method="GET" class="row g-2">
            <div class="col-md-2">
                <input type="text" name="tracking_number" class="form-control" placeholder="Guía #"
                    value="{{ request('tracking_number') }}">
            </div>
            <div class="col-md-2">
                <input type="text" name="cliente" class="form-control" placeholder="Cliente (Rem/Dest)"
                    value="{{ request('cliente') }}">
            </div>
            <div class="col-md-2">
                <select name="origin_agency_filter_id" class="form-select">
                    <option value="">Agencia Origen</option>
                    @foreach($agencies as $agency)
                    <option value="{{ $agency->id }}" {{ request('origin_agency_filter_id')==$agency->id ? 'selected' : '' }}>
                        {{ $agency->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="destination_agency_filter_id" class="form-select">
                    <option value="">Agencia Destino</option>
                    @foreach($agencies as $agency)
                    <option value="{{ $agency->id }}" {{ request('destination_agency_filter_id')==$agency->id ? 'selected' : '' }}>
                        {{ $agency->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status_id" class="form-select">
                    <option value="">Todos los Estados</option>
                    @foreach($statuses as $st)
                    <option value="{{ $st->id }}" {{ request('status_id')==$st->id ? 'selected' : '' }}>{{ $st->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-4 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
                <a href="{{ route('shipments.index') }}" class="btn btn-outline-secondary w-100"><i
                        class="bi bi-x-circle"></i></a>
                <button type="button" class="btn btn-info w-100" onclick="printFilteredShipments()"><i class="bi bi-printer"></i> Imprimir</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Guía #</th>
                    <th>Fecha</th>
                    <th>Fecha Entregado</th>
                    <th>Remitente</th>
                    <th>Destinatario</th>
                    <th>Agencia Origen</th>
                    <th>Agencia Destino</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shipments as $s)
                <tr>
                    <td><strong>{{ $s->tracking_number }}</strong></td>
                    <td>{{ $s->created_at->format('d/m/Y') }}</td>
                    <td>{{ $s->delivery_date ? \Carbon\Carbon::parse($s->delivery_date)->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $s->sender?->nombre_fantasia ?? 'N/A' }}</td>
                    <td>{{ $s->receiver?->nombre_fantasia ?? 'N/A' }}</td>
                    <td>{{ $s->originAgency?->nombre ?? 'N/A' }}</td>
                    <td>{{ $s->destinationAgency?->nombre ?? 'N/A' }}</td>
                    <td>
                        <select class="form-select form-select-sm status-select" 
                                data-shipment-id="{{ $s->id }}" 
                                style="width: auto; background-color: transparent; border-color: #dee2e6;">
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" {{ $s->status_id == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>${{ number_format($s->total_flete, 2) }}</td>
                    <td class="text-end">
                        <div class="btn-group">
                            <a href="{{ route('shipments.show', $s) }}" class="btn btn-sm btn-outline-primary"
                                title="Ver"><i class="bi bi-eye"></i></a>
                            @if(!$s->factura_id || $s->factura_id == 0)
                            <a href="{{ route('shipments.edit', $s) }}" class="btn btn-sm btn-outline-warning"
                                title="Editar"><i class="bi bi-pencil"></i></a>
                            @endif
                                <a href="{{ route('shipments.print', $s) }}" target="_blank"
                                    class="btn btn-sm btn-outline-secondary" title="Imprimir PDF"><i
                                        class="bi bi-printer"></i></a>
                                @if(!$s->factura_id)
                                <form action="{{ route('shipments.destroy', $s) }}" method="POST"
                                    onsubmit="return confirm('¿Está seguro de eliminar esta guía? Esta acción no se puede deshacer.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"><i
                                            class="bi bi-trash"></i></button>
                                </form>
                                @endif
                            </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $shipments->links() }}</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusSelects = document.querySelectorAll('.status-select');
        
        statusSelects.forEach(select => {
            select.addEventListener('change', function () {
                const shipmentId = this.dataset.shipmentId;
                const statusId = this.value;
                const originalValue = this.querySelector('option[selected]')?.value;

                // Feedback visual: deshabilitar mientras procesa
                this.disabled = true;
                this.style.borderColor = '#0d6efd'; // azul primario

                fetch(`/shipments/${shipmentId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status_id: statusId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Éxito: marcamos en verde brevemente y rehabilitamos
                        this.style.borderColor = '#198754'; // verde
                        setTimeout(() => {
                            this.style.borderColor = '#dee2e6';
                            this.disabled = false;
                        }, 1000);
                    } else {
                        throw new Error('Error al actualizar');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('No se pudo actualizar el estado.');
                    this.value = originalValue; // revertir
                    this.style.borderColor = '#dc3545'; // rojo
                    this.disabled = false;
                });
            });
        });

        window.printFilteredShipments = function() {
            const form = document.querySelector('.card-body > form');
            const params = new URLSearchParams(new FormData(form)).toString();
            window.open(`{{ route('shipments.print_filtered') }}?${params}`, '_blank');
        };
    });
</script>
@endpush
@endsection