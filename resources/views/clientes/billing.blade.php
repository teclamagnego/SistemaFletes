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
        @if($guiasNuevasCount > 0)
        <div class="alert alert-warning d-flex align-items-center mb-4 border-warning-subtle shadow-sm alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
            <div>
                Existen <strong>{{ $guiasNuevasCount }}</strong> guías para este cliente en estado Nuevo (sin entregar).
                <a href="{{ route('shipments.index', ['cliente_id' => $cliente->id, 'status_id' => 1]) }}" class="alert-link ms-1 text-decoration-underline" target="_blank">
                    Haz clic aquí para verlas
                </a>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form action="{{ route('clientes.billing', $cliente) }}" method="GET" class="row g-2 mb-4">
            <div class="col-md-2">
                <label class="form-label small fw-bold">Desde</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Hasta</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Estado Facturación</label>
                <select name="status_factura" class="form-select form-select-sm">
                    <option value="all" {{ $status_factura=='all' ? 'selected' : '' }}>Todas</option>
                    <option value="unbilled" {{ $status_factura=='unbilled' ? 'selected' : '' }}>No Facturadas</option>
                    <option value="billed" {{ $status_factura=='billed' ? 'selected' : '' }}>Facturadas</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Origen</label>
                <select name="origin_agency_id" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($agencies as $agency)
                    <option value="{{ $agency->id }}" {{ $origin_agency_id == $agency->id ? 'selected' : '' }}>
                        {{ $agency->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Destino</label>
                <select name="destination_agency_id" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($agencies as $agency)
                    <option value="{{ $agency->id }}" {{ $destination_agency_id == $agency->id ? 'selected' : '' }}>
                        {{ $agency->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-fill" title="Filtrar">
                    <i class="bi bi-filter"></i>
                </button>
                <button type="submit" name="export" value="excel" class="btn btn-outline-success btn-sm flex-fill" title="Exportar Excel">
                    <i class="bi bi-file-earmark-excel"></i>
                </button>
                <button type="submit" name="export" value="pdf" class="btn btn-outline-danger btn-sm flex-fill" title="Exportar PDF">
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
                            <th>F. Entrega</th>
                            <th>Guía</th>
                            <th>Ref Remito</th>
                            <th>Origen</th>
                            <th>Destino</th>
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
                                @php
                                    $deliveryLog = $s->logs->where('status_to_id', \App\Models\ShipmentStatus::DELIVERED)->first();
                                @endphp
                                @if($deliveryLog)
                                    {{ \Carbon\Carbon::parse($deliveryLog->created_at)->format('d/m/Y') }}
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('shipments.show', $s) }}" target="_blank"
                                    class="text-decoration-none fw-bold">
                                    {{ $s->tracking_number }}
                                </a>
                            </td>
                            <td>{{ $s->ref_remito }}</td>
                            <td><small>{{ $s->originAgency?->nombre ?? '-' }}</small></td>
                            <td><small>{{ $s->destinationAgency?->nombre ?? '-' }}</small></td>
                            <td><small>{{ $s->formaPago?->nombre }}</small></td>
                            <td>
                                @if($s->factura_id != 0)
                                <span class="badge bg-success-subtle text-success border border-success">Facturada</span>
                                @else
                                <span class="badge bg-warning-subtle text-warning border border-warning">Pendiente</span>
                                @endif
                                
                                <br>
                                @if($s->faltarendir <= 0)
                                <span class="badge bg-success-subtle text-success border border-success mt-1 toggle-payment" data-id="{{ $s->id }}" data-amount="{{ $s->total_flete }}" style="cursor: pointer;" title="Clic para cambiar">Pagada</span>
                                @else
                                <span class="badge bg-danger-subtle text-danger border border-danger mt-1 toggle-payment" data-id="{{ $s->id }}" data-amount="{{ $s->total_flete }}" style="cursor: pointer;" title="Clic para cambiar">Pendiente Pago</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold">$ {{ number_format($s->total_flete, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">No se encontraron guías para este
                                periodo.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="9" class="text-end fw-bold">TOTAL SELECCIONADO:</td>
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
                     <span class="fw-bold me-2">Código Factura:</span>
                     <select name="codigo" class="form-select form-select-sm d-inline-block" style="width: auto;">
                         <option value="">Seleccione código</option>
                         @foreach($facturaCodigos as $codigo)
                             <option value="{{ $codigo->id }}">{{ $codigo->nombre }}</option>
                         @endforeach
                     </select>
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

{{-- Flotante para Total Recaudado --}}
<div id="floatingTotalContainer" class="position-fixed bottom-0 end-0 m-4 p-3 bg-white rounded shadow border border-success" style="z-index: 1050; display: none; min-width: 200px;">
    <div class="small fw-bold text-muted mb-1"><i class="bi bi-calculator"></i> Total Modificado (Sesión)</div>
    <div class="d-flex align-items-center justify-content-between gap-3">
        <span class="fs-4 fw-bold text-success" id="floatingTotalAmount">$ 0.00</span>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="resetFloatingTotal" title="Reiniciar a cero">
            <i class="bi bi-arrow-counterclockwise"></i>
        </button>
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

        // Floating Total Logic
        let sessionTotal = 0;
        const floatingContainer = document.getElementById('floatingTotalContainer');
        const floatingAmount = document.getElementById('floatingTotalAmount');
        const resetBtn = document.getElementById('resetFloatingTotal');

        function updateFloatingTotal() {
            if(sessionTotal === 0) {
                floatingContainer.style.display = 'none';
            } else {
                floatingContainer.style.display = 'block';
                floatingAmount.innerText = '$ ' + sessionTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }

        resetBtn.addEventListener('click', function() {
            sessionTotal = 0;
            updateFloatingTotal();
        });

        // AJAX Toggle Payment Status
        document.querySelectorAll('.toggle-payment').forEach(el => {
            el.addEventListener('click', function() {
                let id = this.getAttribute('data-id');
                let amount = parseFloat(this.getAttribute('data-amount')) || 0;
                let isCurrentlyPaid = this.classList.contains('bg-success-subtle');
                
                // Optic UI update immediately for snappiness, or wait for fetch. We'll wait.
                fetch(`/shipments/${id}/toggle-payment`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        let isNowPaid = data.faltarendir <= 0;
                        if(isNowPaid) {
                             this.className = "badge bg-success-subtle text-success border border-success mt-1 toggle-payment";
                             this.innerText = "Pagada";
                             if(!isCurrentlyPaid) sessionTotal += amount;
                        } else {
                             this.className = "badge bg-danger-subtle text-danger border border-danger mt-1 toggle-payment";
                             this.innerText = "Pendiente Pago";
                             if(isCurrentlyPaid) sessionTotal -= amount;
                        }
                        updateFloatingTotal();
                    } else {
                        alert('Error al cambiar el estado de pago');
                    }
                });
            });
        });
    });
</script>
@endsection