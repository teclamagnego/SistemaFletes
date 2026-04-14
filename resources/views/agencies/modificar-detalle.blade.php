@extends('layouts.bootstrap')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="mb-0 fw-bold">Modificar Liquidación Nº {{ $factura->nro_factura }}</h2>
            <p class="text-muted mb-0">
                Agencia: <strong>{{ $factura->agency->nombre }}</strong> &mdash;
                Fecha: {{ \Carbon\Carbon::parse($factura->fecha)->format('d/m/Y') }}
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('agencies.history', $factura->agency_id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
            <a href="{{ route('agencies.facturas.printDetail', $factura) }}" class="btn btn-outline-primary ms-2" target="_blank">
                <i class="bi bi-printer me-1"></i> Imprimir
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('agencies.facturas.guardarModificacionDetalle', $factura) }}" method="POST">
        @csrf

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold">Guías incluidas en la liquidación</span>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-success me-1" id="btnSelAll">
                        <i class="bi bi-check2-all"></i> Marcar todas
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="btnDeselAll">
                        <i class="bi bi-x-lg"></i> Desmarcar todas
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width:60px;">Mantener</th>
                                <th>F. Entrega</th>
                                <th>Guía Nº</th>
                                <th>Remitente</th>
                                <th>Destinatario</th>
                                <th>Rol</th>
                                <th class="text-end">Comisión</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalCalculado = 0; @endphp
                            @forelse($shipments as $s)
                                @php
                                    $comision = 0;
                                    $roles = [];

                                    if ($s->agencia_f_origen_id == $factura->id) {
                                        $comision += $s->comision_origen;
                                        $roles[] = ['label' => 'Origen', 'key' => 'origen_' . $s->id];
                                    }

                                    if ($s->agencia_f_destino_id == $factura->id) {
                                        $comision += $s->comision_destino;
                                        $roles[] = ['label' => 'Destino', 'key' => 'destino_' . $s->id];
                                    }

                                    $totalCalculado += $comision;

                                    $deliveryLog = $s->logs->where('status_to_id', \App\Models\ShipmentStatus::DELIVERED)->first();
                                    $fechaEntrega = $deliveryLog
                                        ? \Carbon\Carbon::parse($deliveryLog->created_at)->format('d/m/Y')
                                        : \Carbon\Carbon::parse($s->fecha)->format('d/m/Y');
                                @endphp
                                @foreach($roles as $rol)
                                <tr>
                                    <td class="text-center">
                                        <input
                                            type="checkbox"
                                            name="mantener[]"
                                            value="{{ $rol['key'] }}"
                                            class="form-check-input chk-guia"
                                            checked
                                            style="width:1.2em;height:1.2em;"
                                        >
                                    </td>
                                    <td>{{ $fechaEntrega }}</td>
                                    <td>
                                        <a href="{{ route('shipments.show', $s->id) }}" target="_blank" class="text-decoration-none fw-semibold">
                                            {{ $s->tracking_number }}
                                        </a>
                                    </td>
                                    <td>{{ $s->sender?->nombre_fantasia }}</td>
                                    <td>{{ $s->receiver?->nombre_fantasia }}</td>
                                    <td>
                                        <span class="badge {{ $rol['label'] === 'Origen' ? 'bg-info-subtle text-info border border-info' : 'bg-warning-subtle text-warning border border-warning' }}">
                                            {{ $rol['label'] }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-semibold">
                                        $ {{ number_format($rol['label'] === 'Origen' ? $s->comision_origen : $s->comision_destino, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">No hay guías vinculadas a esta liquidación.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-secondary fw-bold">
                            <tr>
                                <td colspan="6" class="text-end">TOTAL LIQUIDACIÓN:</td>
                                <td class="text-end">$ {{ number_format($totalCalculado, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <span class="fw-bold">Observación</span>
            </div>
            <div class="card-body">
                <textarea name="observacion" class="form-control" rows="3" placeholder="Observaciones sobre esta liquidación...">{{ old('observacion', $factura->observacion) }}</textarea>
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-end mb-5">
            <a href="{{ route('agencies.history', $factura->agency_id) }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Guardar cambios
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('btnSelAll').addEventListener('click', function () {
        document.querySelectorAll('.chk-guia').forEach(c => c.checked = true);
    });
    document.getElementById('btnDeselAll').addEventListener('click', function () {
        document.querySelectorAll('.chk-guia').forEach(c => c.checked = false);
    });
</script>
@endpush
@endsection
