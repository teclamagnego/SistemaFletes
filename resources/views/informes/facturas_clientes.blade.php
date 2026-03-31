@extends('layouts.bootstrap')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-receipt"></i> Informe de Facturas de Clientes</h1>
</div>

{{-- Filtros --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtros</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('informes.facturas_clientes') }}" id="formFiltros">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="fecha_desde" class="form-label fw-semibold">Fecha Desde</label>
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"
                        value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-3">
                    <label for="fecha_hasta" class="form-label fw-semibold">Fecha Hasta</label>
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"
                        value="{{ request('fecha_hasta') }}">
                </div>
                <div class="col-md-3">
                    <label for="codigos" class="form-label fw-semibold">Tipo de Factura</label>
                    <select class="form-select" id="codigos" name="codigos[]" multiple>
                        @foreach($codigos as $codigo)
                            <option value="{{ $codigo->id }}"
                                {{ in_array($codigo->id, (array) request('codigos', [])) ? 'selected' : '' }}>
                                {{ $codigo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-search"></i> Consultar
                        </button>
                        <a href="{{ route('informes.facturas_clientes') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                        @if($filtrado && $facturas->count() > 0)
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

{{-- Resumen --}}
@if($filtrado)
<div class="row mb-4 g-3">
    <div class="col-md-4">
        <div class="card border-start border-primary border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Total Facturas</div>
                <div class="h4 mb-0 fw-bold text-primary">{{ $totalFacturas }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-start border-success border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Importe Total</div>
                <div class="h4 mb-0 fw-bold text-success">$ {{ number_format($totalImporte, 2, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-start border-danger border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Pendiente de Cobro</div>
                <div class="h4 mb-0 fw-bold text-danger">$ {{ number_format($totalPendiente, 2, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Tabla de resultados --}}
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Fecha</th>
                        <th>Tipo</th>
                        <th>Nro. Factura</th>
                        <th>Cliente</th>
                        <th>Forma de Pago</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Pendiente</th>
                        <th class="text-end pe-4">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($facturas as $factura)
                        @php
                            $codigoNombre = $codigos->firstWhere('id', $factura->codigo);
                            $tipoBadge = match($codigoNombre?->nombre ?? '') {
                                'FA', 'FB' => 'bg-primary',
                                'DA', 'DB' => 'bg-info',
                                'CA', 'CB' => 'bg-warning text-dark',
                                'REM' => 'bg-secondary',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <tr>
                            <td class="ps-4">{{ \Carbon\Carbon::parse($factura->fecha)->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge {{ $tipoBadge }}">{{ $codigoNombre?->nombre ?? $factura->codigo }}</span>
                            </td>
                            <td class="fw-semibold">{{ $factura->nro_factura }}</td>
                            <td>
                                <a href="{{ route('clientes.history', $factura->cliente_id) }}" class="text-decoration-none text-primary">
                                    {{ $factura->cliente->nombre_fantasia ?? $factura->cliente->razon_social ?? '-' }}
                                </a>
                            </td>
                            <td class="text-muted">{{ $factura->formaPago->nombre ?? '-' }}</td>
                            <td class="text-end fw-semibold">$ {{ number_format($factura->total, 2, ',', '.') }}</td>
                            <td class="text-end {{ $factura->falta_imputar > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                $ {{ number_format($factura->falta_imputar, 2, ',', '.') }}
                            </td>
                            <td class="text-end pe-4">
                                @if($factura->falta_imputar <= 0)
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Pagada</span>
                                @elseif($factura->falta_imputar < $factura->total)
                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Parcial</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-exclamation-circle"></i> Pendiente</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-info-circle"></i> No se encontraron facturas con los filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
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
        $('#codigos').select2({
            theme: 'bootstrap-5',
            placeholder: 'Todos los tipos',
            allowClear: true,
            width: '100%'
        });

        $('#btnImprimir').on('click', function() {
            var params = new URLSearchParams();
            var fechaDesde = $('#fecha_desde').val();
            var fechaHasta = $('#fecha_hasta').val();
            var codigos = $('#codigos').val();

            if (fechaDesde) params.append('fecha_desde', fechaDesde);
            if (fechaHasta) params.append('fecha_hasta', fechaHasta);
            if (codigos && codigos.length > 0) params.append('codigos', codigos.join(','));

            var url = "{{ route('informes.facturas_clientes.print') }}?" + params.toString();
            window.open(url, '_blank');
        });
    });
</script>
@endpush
