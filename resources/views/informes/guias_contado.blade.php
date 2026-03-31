@extends('layouts.bootstrap')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-cash-stack"></i> Informe de Guías de Contado</h1>
</div>

{{-- Filtros --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtros</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('informes.guias_contado') }}" id="formFiltros">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="fecha_desde" class="form-label fw-semibold">Fecha Desde</label>
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"
                        value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-2">
                    <label for="fecha_hasta" class="form-label fw-semibold">Fecha Hasta</label>
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"
                        value="{{ request('fecha_hasta') }}">
                </div>
                <div class="col-md-3">
                    <label for="origin_agency_id" class="form-label fw-semibold">Agencia Origen</label>
                    <select class="form-select" id="origin_agency_id" name="origin_agency_id">
                        <option value="">Todas</option>
                        @foreach($agencies as $agency)
                            <option value="{{ $agency->id }}" {{ request('origin_agency_id') == $agency->id ? 'selected' : '' }}>
                                {{ $agency->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="destination_agency_id" class="form-label fw-semibold">Agencia Destino</label>
                    <select class="form-select" id="destination_agency_id" name="destination_agency_id">
                        <option value="">Todas</option>
                        @foreach($agencies as $agency)
                            <option value="{{ $agency->id }}" {{ request('destination_agency_id') == $agency->id ? 'selected' : '' }}>
                                {{ $agency->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-search"></i> Consultar
                        </button>
                        <a href="{{ route('informes.guias_contado') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                        @if($filtrado && $shipments->count() > 0)
                        <button type="button" class="btn btn-outline-danger" id="btnImprimir">
                            <i class="bi bi-printer"></i>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@if($filtrado)
{{-- Resumen --}}
<div class="row mb-4 g-3">
    <div class="col-md-6">
        <div class="card border-start border-primary border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Total Guías</div>
                <div class="h4 mb-0 fw-bold text-primary">{{ $totalGuias }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-start border-success border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Importe Total</div>
                <div class="h4 mb-0 fw-bold text-success">$ {{ number_format($totalImporte, 2, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Tabla --}}
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Guía #</th>
                        <th>Fecha</th>
                        <th>Remitente</th>
                        <th>Destinatario</th>
                        <th>Ag. Origen</th>
                        <th>Ag. Destino</th>
                        <th>Forma Pago</th>
                        <th class="text-end pe-4">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shipments as $s)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('shipments.show', $s) }}" class="text-decoration-none fw-semibold">
                                {{ $s->tracking_number }}
                            </a>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($s->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $s->sender->nombre_fantasia ?? $s->sender->razon_social ?? '-' }}</td>
                        <td>{{ $s->receiver->nombre_fantasia ?? $s->receiver->razon_social ?? '-' }}</td>
                        <td>{{ $s->originAgency->nombre ?? '-' }}</td>
                        <td>{{ $s->destinationAgency->nombre ?? '-' }}</td>
                        <td><span class="badge bg-success">{{ $s->formaPago->nombre ?? 'Contado' }}</span></td>
                        <td class="text-end fw-semibold pe-4">$ {{ number_format($s->total_flete, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-info-circle"></i> No se encontraron guías de contado con los filtros aplicados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($shipments->count() > 0)
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="7" class="text-end ps-4">TOTAL:</td>
                        <td class="text-end pe-4">$ {{ number_format($totalImporte, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@else
<div class="card shadow-sm">
    <div class="card-body text-center py-5 text-muted">
        <i class="bi bi-funnel" style="font-size: 2rem;"></i>
        <p class="mt-2 mb-0">Seleccione los filtros y presione <strong>Consultar</strong> para ver el informe.</p>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    .table td { border-bottom: 1px solid #f0f0f0; }
    .border-4 { border-width: 4px !important; }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#btnImprimir').on('click', function() {
            var params = new URLSearchParams();
            var fechaDesde = $('#fecha_desde').val();
            var fechaHasta = $('#fecha_hasta').val();
            var originAgency = $('#origin_agency_id').val();
            var destAgency = $('#destination_agency_id').val();

            if (fechaDesde) params.append('fecha_desde', fechaDesde);
            if (fechaHasta) params.append('fecha_hasta', fechaHasta);
            if (originAgency) params.append('origin_agency_id', originAgency);
            if (destAgency) params.append('destination_agency_id', destAgency);

            var url = "{{ route('informes.guias_contado.print') }}?" + params.toString();
            window.open(url, '_blank');
        });
    });
</script>
@endpush
