@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-cash-coin"></i> Contrareembolsos</h2>
</div>

{{-- Filtros --}}
<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('contrareembolsos.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Cliente</label>
                <input type="text" name="cliente" class="form-control" placeholder="Buscar cliente..."
                    value="{{ request('cliente') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small">Estado Cobro</label>
                <select name="estado_cobrado" class="form-select">
                    <option value="">Todos</option>
                    <option value="cobrado" {{ request('estado_cobrado') == 'cobrado' ? 'selected' : '' }}>Cobrado</option>
                    <option value="pendiente" {{ request('estado_cobrado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small">Estado Rendición</label>
                <select name="estado_rendido" class="form-select">
                    <option value="">Todos</option>
                    <option value="rendido" {{ request('estado_rendido') == 'rendido' ? 'selected' : '' }}>Rendido</option>
                    <option value="pendiente" {{ request('estado_rendido') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small">Cobrado Desde</label>
                <input type="date" name="fecha_cobrado_desde" class="form-control" value="{{ request('fecha_cobrado_desde') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small">Cobrado Hasta</label>
                <input type="date" name="fecha_cobrado_hasta" class="form-control" value="{{ request('fecha_cobrado_hasta') }}">
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
                <a href="{{ route('contrareembolsos.index') }}" class="btn btn-outline-secondary w-100"><i class="bi bi-x-circle"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Resumen --}}
<div class="row mb-3 g-3">
    <div class="col-md-4">
        <div class="card border-start border-primary border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Total Monto</div>
                <div class="h4 mb-0 fw-bold text-primary">$ {{ number_format($totalMonto, 2, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-start border-warning border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Pendiente de Cobro</div>
                <div class="h4 mb-0 fw-bold text-warning">$ {{ number_format($totalPendienteCobro, 2, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-start border-danger border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Cobrado sin Rendir</div>
                <div class="h4 mb-0 fw-bold text-danger">$ {{ number_format($totalPendienteRendicion, 2, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Tabla --}}
<div class="card">
    <div class="card-body table-responsive p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Guía #</th>
                    <th>Nº CR</th>
                    <th>Cliente</th>
                    <th class="text-end">Monto</th>
                    <th class="text-center">Fecha Cobrado</th>
                    <th class="text-center">Fecha Rendido</th>
                    <th class="text-center pe-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contrareembolsos as $cr)
                <tr>
                    <td class="ps-3">
                        <a href="{{ route('shipments.show', $cr->guia_id) }}" class="text-decoration-none fw-semibold">
                            {{ $cr->shipment->tracking_number ?? $cr->guia_id }}
                        </a>
                    </td>
                    <td><span class="text-muted">#{{ str_pad($cr->id, 6, '0', STR_PAD_LEFT) }}</span></td>
                    <td>{{ $cr->cliente->nombre_fantasia ?? $cr->cliente->razon_social ?? '-' }}</td>
                    <td class="text-end fw-semibold">$ {{ number_format($cr->monto, 2, ',', '.') }}</td>
                    <td class="text-center">
                        @if($cr->fecha_cobrado)
                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> {{ $cr->fecha_cobrado->format('d/m/Y') }}</span>
                        @else
                            <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Pendiente</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($cr->fecha_rendido)
                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> {{ $cr->fecha_rendido->format('d/m/Y') }}</span>
                            <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1 btn-clear-rendido"
                                data-id="{{ $cr->id }}" title="Quitar fecha rendido">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-center pe-3">
                        <div class="d-flex align-items-center justify-content-center gap-1">
                            <a href="{{ route('contrareembolsos.print', $cr) }}" target="_blank"
                                class="btn btn-sm btn-outline-secondary" title="Imprimir comprobante">
                                <i class="bi bi-printer"></i>
                            </a>
                            @if(!$cr->fecha_rendido)
                            <div class="input-group input-group-sm" style="width: 170px;">
                                <input type="date" class="form-control fecha-rendido-input" data-id="{{ $cr->id }}"
                                    value="{{ now()->format('Y-m-d') }}">
                                <button class="btn btn-outline-success btn-set-rendido" data-id="{{ $cr->id }}" title="Guardar">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="bi bi-info-circle"></i> No hay contrareembolsos registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $contrareembolsos->links() }}</div>

@push('styles')
<style>
    .border-4 { border-width: 4px !important; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Setear fecha rendido
    document.querySelectorAll('.btn-set-rendido').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const input = document.querySelector(`.fecha-rendido-input[data-id="${id}"]`);
            const fecha = input.value;
            if (!fecha) { alert('Seleccione una fecha'); return; }

            fetch(`/contrareembolsos/${id}/fecha-rendido`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ fecha_rendido: fecha })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) location.reload();
                else alert('Error al actualizar');
            })
            .catch(() => alert('Error al actualizar'));
        });
    });

    // Limpiar fecha rendido
    document.querySelectorAll('.btn-clear-rendido').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('¿Quitar la fecha de rendición?')) return;
            const id = this.dataset.id;

            fetch(`/contrareembolsos/${id}/clear-rendido`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) location.reload();
                else alert('Error al actualizar');
            })
            .catch(() => alert('Error al actualizar'));
        });
    });
});
</script>
@endpush
@endsection
