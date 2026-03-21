@extends('layouts.bootstrap')

@section('content')
<div class="card shadow-sm border-0 mb-3">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold">Facturación de Guías: {{ $cliente->nombre_fantasia }}</h5>
            <small class="text-muted">Seleccione las guías para generar una factura</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('clientes.history', $cliente) }}" class="btn btn-secondary btn-sm">
                Volver al historial
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('clientes.billing', $cliente) }}" method="GET" class="row g-2 mb-4">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Desde</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Hasta</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Estado Facturación</label>
                <select name="status_factura" class="form-select form-select-sm">
                    <option value="all" {{ $status_factura=='all' ? 'selected' : '' }}>Todas</option>
                    <option value="unbilled" {{ $status_factura=='unbilled' ? 'selected' : '' }}>No Facturadas</option>
                    <option value="billed" {{ $status_factura=='billed' ? 'selected' : '' }}>Facturadas</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-filter"></i>
                </button>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-1">
                <button type="submit" name="export" value="excel" class="btn btn-outline-success btn-sm flex-fill">
                    <i class="bi bi-file-earmark-excel"></i>
                </button>
                <button type="submit" name="export" value="pdf" class="btn btn-outline-danger btn-sm flex-fill">
                    <i class="bi bi-file-earmark-pdf"></i>
                </button>
            </div>
        </form>

        <form action="{{ route('clientes.generateInvoice', $cliente) }}" method="POST" id="billingForm">
            @csrf
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>Fecha</th>
                            <th>Guía</th>
                            <th>Ref Remito</th>
                            <th>F. Pago</th>
                            <th>Estado</th>
                            <th class="text-end">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalSeleccionado = 0; @endphp
                        @forelse($shipments as $s)
                        <tr>
                            <td>
                                @if($s->factura_id == 0)
                                <input type="checkbox" name="shipment_ids[]" value="{{ $s->id }}"
                                    class="form-check-input shipment-checkbox" data-amount="{{ $s->total_flete }}">
                                @else
                                <i class="bi bi-check-circle-fill text-success"
                                    title="Facturada (ID: {{ $s->factura_id }})"></i>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($s->fecha)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('shipments.show', $s) }}" target="_blank"
                                    class="text-decoration-none fw-bold">
                                    {{ $s->tracking_number }}
                                </a>
                            </td>
                            <td>{{ $s->ref_remito }}</td>
                            <td><small>{{ $s->formaPago?->nombre }}</small></td>
                            <td>
                                @if($s->factura_id > 0)
                                <span
                                    class="badge bg-success-subtle text-success border border-success">Facturada</span>
                                @else
                                <span
                                    class="badge bg-warning-subtle text-warning border border-warning">Pendiente</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold">$ {{ number_format($s->total_flete, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No se encontraron guías para este
                                periodo.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="5" class="text-end fw-bold">TOTAL SELECCIONADO:</td>
                            <td class="text-end fw-bold text-primary" id="totalDisplay">$ 0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-end gap-3 align-items-center bg-light p-3 rounded border">
                <div>
                    <span class="fw-bold me-2">Fecha Factura:</span>
                    <input type="date" name="fecha" class="form-control form-control-sm d-inline-block"
                        style="width: auto;" value="{{ date('Y-m-d') }}" required>
                </div>
                <div>
                    <span class="fw-bold me-2">Nro Factura (opcional):</span>
                    <input type="text" name="nro_factura" class="form-control form-control-sm d-inline-block"
                        style="width: 150px;" placeholder="Ej: 0001-00001234">
                </div>
                <button type="submit" class="btn btn-success" id="btnGenerate" disabled>
                    <i class="bi bi-plus-circle"></i> Generar Factura
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.shipment-checkbox');
        const totalDisplay = document.getElementById('totalDisplay');
        const btnGenerate = document.getElementById('btnGenerate');

        function updateTotal() {
            let total = 0;
            let checkedCount = 0;
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    total += parseFloat(cb.getAttribute('data-amount'));
                    checkedCount++;
                }
            });
            totalDisplay.innerText = '$ ' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            btnGenerate.disabled = checkedCount === 0;
        }

        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateTotal();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateTotal);
        });
    });
</script>
@endsection