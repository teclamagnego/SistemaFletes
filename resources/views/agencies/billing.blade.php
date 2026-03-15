@extends('layouts.bootstrap')

@section('content')
<div class="card shadow-sm border-0 mb-3">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold">Liquidación de Comisiones: {{ $agency->nombre }}</h5>
            <small class="text-muted">Seleccione las guías entregadas para facturar su comisión</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('agencies.history', $agency) }}" class="btn btn-secondary btn-sm">
                Volver al historial
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('agencies.billing', $agency) }}" method="GET" class="row g-2 mb-4">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Desde</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Hasta</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Rol Agencia</label>
                <select name="role" class="form-select form-select-sm">
                    <option value="all" {{ $role=='all' ? 'selected' : '' }}>Todos</option>
                    <option value="origin" {{ $role=='origin' ? 'selected' : '' }}>Solo Origen</option>
                    <option value="destination" {{ $role=='destination' ? 'selected' : '' }}>Solo Destino</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Estado</label>
                <select name="status_factura" class="form-select form-select-sm">
                    <option value="all" {{ $status_factura=='all' ? 'selected' : '' }}>Todas</option>
                    <option value="unbilled" {{ $status_factura=='unbilled' ? 'selected' : '' }}>No Facturadas</option>
                    <option value="billed" {{ $status_factura=='billed' ? 'selected' : '' }}>Facturadas</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Estado Guía</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="all" {{ $status=='all' ? 'selected' : '' }}>Todos</option>
                    <option value="Admitted" {{ $status=='Admitted' ? 'selected' : '' }}>Admitida</option>
                    <option value="In Transit" {{ $status=='In Transit' ? 'selected' : '' }}>En Tránsito</option>
                    <option value="In Destination" {{ $status=='In Destination' ? 'selected' : '' }}>En Destino</option>
                    <option value="Delivered" {{ $status=='Delivered' ? 'selected' : '' }}>Entregada</option>
                    <option value="Cancelled" {{ $status=='Cancelled' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-filter"></i> Filtrar
                </button>
            </div>
        </form>

        <form action="{{ route('agencies.generateInvoice', $agency) }}" method="POST" id="billingForm">
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
                            <th>Estado</th>
                            <th>Participación</th>
                            <th>F. Pago</th>
                            <th class="text-end">Total Guía</th>
                            <th class="text-end">Comisión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $s)
                        @php
                        $isOrigin = $s->origin_agency_id == $agency->id;
                        $isDest = $s->destination_agency_id == $agency->id;
                        $billedOr = $s->agencia_f_origen_id > 0;
                        $billedDe = $s->agencia_f_destino_id > 0;

                        // Un shipment se puede facturar si en alguno de sus roles no está facturado
                        $canBill = ($isOrigin && !$billedOr) || ($isDest && !$billedDe);
                        // Pero si el usuario filtró por un rol específico, respetamos eso
                        if ($role === 'origin') $canBill = ($isOrigin && !$billedOr);
                        if ($role === 'destination') $canBill = ($isDest && !$billedDe);
                        @endphp
                        <tr>
                            <td>
                                @if($canBill)
                                <input type="checkbox" name="shipments[]" value="{{ $s->id }}"
                                    class="form-check-input shipment-checkbox"
                                    data-amount="{{ $s->comision_total_agencia }}">
                                @else
                                <i class="bi bi-check-circle-fill text-success" title="Facturada"></i>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($s->fecha)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('shipments.show', $s) }}" target="_blank"
                                    class="text-decoration-none fw-bold">
                                    {{ $s->tracking_number }}
                                </a>
                            </td>
                            <td>
                                @php
                                $statusColors = [
                                'Admitted' => 'info',
                                'In Transit' => 'primary',
                                'In Destination' => 'warning',
                                'Delivered' => 'success',
                                'Cancelled' => 'danger',
                                ];
                                $color = $statusColors[$s->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }} small">{{ $s->status }}</span>
                            </td>
                            <td>
                                @foreach($s->role_in_billing as $r)
                                <span class="badge bg-secondary-subtle text-secondary border small">{{ $r }}</span>
                                @endforeach
                            </td>
                            <td><small>{{ $s->formaPago?->nombre }}</small></td>
                            <td class="text-end text-muted small">$ {{ number_format($s->total_flete, 2) }}</td>
                            <td class="text-end fw-bold text-success">
                                $ {{ number_format($s->comision_total_agencia, 2) }}
                                @if(!$canBill)
                                <div class="small text-muted fw-normal" style="font-size: 0.7rem;">(Facturada)</div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No se encontraron guías para este
                                periodo.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="6" class="text-end fw-bold">TOTAL COMISIÓN SELECCIONADA:</td>
                            <td class="text-end fw-bold text-primary" id="totalDisplay">$ 0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-end gap-3 align-items-center bg-light p-3 rounded border">
                <button type="submit" class="btn btn-success px-5" id="btnGenerate" disabled>
                    <i class="bi bi-receipt"></i> Generar Factura de Comisión
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
                    total += parseFloat(cb.getAttribute('data-amount') || 0);
                    checkedCount++;
                }
            });

            totalDisplay.innerText = '$ ' + total.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            btnGenerate.disabled = (checkedCount === 0);
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                });
                updateTotal();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                if (!this.checked && selectAll) {
                    selectAll.checked = false;
                }
                const allChecked = Array.from(checkboxes).every(c => c.checked);
                if (allChecked && selectAll) {
                    selectAll.checked = true;
                }
                updateTotal();
            });
        });

        updateTotal();
    });
</script>
@endsection