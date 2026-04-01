@extends('layouts.bootstrap')

@push('styles')
<style>
    .list-group.position-absolute {
        z-index: 2000 !important;
    }

    .table-responsive {
        overflow: visible !important;
    }

    .list-group-item-action {
        cursor: pointer;
    }

    .list-group-item-action:hover {
        background-color: #f8f9fa;
    }

    .item-total-display {
        font-weight: bold;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-truck me-2 text-primary"></i>Editar Guía: {{ $shipment->tracking_number }}</h5>
                    <span class="badge bg-{{ $shipment->status->color ?? 'secondary' }}">{{ $shipment->status->name }}</span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('shipments.update', $shipment) }}" method="POST" id="shipmentForm">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="sender_search" class="form-label fw-semibold">Remitente</label>
                                <div class="position-relative">
                                    <input type="text" id="sender_search" class="form-control"
                                        placeholder="Buscar cliente..." autocomplete="off" value="{{ $shipment->sender->nombre_fantasia }}">
                                    <input type="hidden" name="sender_id" id="sender_id" value="{{ $shipment->sender_id }}">
                                    <div id="sender_results" class="list-group position-absolute w-100 shadow-sm"
                                        style="z-index: 1000; display: none;"></div>
                                </div>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="payer" id="payer_sender"
                                            value="sender" {{ $shipment->payer === 'sender' ? 'checked' : '' }}>
                                        <label class="form-check-label text-primary fw-bold" for="payer_sender">Paga
                                            Remitente</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="receiver_search" class="form-label fw-semibold">Destinatario</label>
                                <div class="position-relative">
                                    <input type="text" id="receiver_search" class="form-control"
                                        placeholder="Buscar cliente..." autocomplete="off" value="{{ $shipment->receiver->nombre_fantasia }}">
                                    <input type="hidden" name="receiver_id" id="receiver_id" value="{{ $shipment->receiver_id }}">
                                    <div id="receiver_results" class="list-group position-absolute w-100 shadow-sm"
                                        style="z-index: 1000; display: none;"></div>
                                </div>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="payer" id="payer_receiver"
                                            value="receiver" {{ $shipment->payer === 'receiver' ? 'checked' : '' }}>
                                        <label class="form-check-label text-primary fw-bold" for="payer_receiver">Paga
                                            Destinatario</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="origin_agency_id" class="form-label fw-semibold">Agencia Origen</label>
                                <select name="origin_agency_id" id="origin_agency_id" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}" {{ $shipment->origin_agency_id == $agency->id ? 'selected' : '' }}>{{ $agency->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="destination_agency_id" class="form-label fw-semibold">Agencia
                                    Destino</label>
                                <select name="destination_agency_id" id="destination_agency_id" class="form-select"
                                    required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}" {{ $shipment->destination_agency_id == $agency->id ? 'selected' : '' }}>{{ $agency->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <label for="carrier_id" class="form-label fw-semibold">Transportista</label>
                                <select name="carrier_id" id="carrier_id" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($carriers as $carrier)
                                    <option value="{{ $carrier->id }}" {{ $shipment->carrier_id == $carrier->id ? 'selected' : '' }}>{{ $carrier->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="forma_pago_id" class="form-label fw-semibold">Forma de Pago</label>
                                <select name="forma_pago_id" id="forma_pago_id" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($formasPago as $fp)
                                    <option value="{{ $fp->id }}" {{ $shipment->forma_pago_id == $fp->id ? 'selected' : '' }}>{{ $fp->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="direccion_entrega" class="form-label fw-semibold">Lugar de Entrega</label>
                                <input type="text" name="direccion_entrega" id="direccion_entrega" class="form-control"
                                    placeholder="Dirección de entrega" value="{{ $shipment->direccion_entrega }}">
                            </div>
                        </div>

                        <div class="card mb-4 border-light bg-light bg-opacity-10">
                            <div
                                class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">Artículos / Bultos</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addItem">
                                    <i class="bi bi-plus-lg me-1"></i>Añadir Línea
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="itemsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="150">Código</th>
                                                <th>Producto</th>
                                                <th width="80">Cant.</th>
                                                <th width="120">P. Unitario</th>
                                                <th width="100">%</th>
                                                <th width="120">Total</th>
                                                <th width="50"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($shipment->items as $index => $item)
                                            <tr class="item-row">
                                                <td>
                                                    <div class="position-relative">
                                                        <input type="text"
                                                            class="form-control form-control-sm item-codigo"
                                                            placeholder="Código" autocomplete="off" value="{{ $item->articulo->codigo ?? '' }}">
                                                        <div class="list-group position-absolute w-100 shadow-sm item-codigo-results"
                                                            style="z-index: 1000; display: none;"></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="position-relative">
                                                        <input type="text" name="items[{{ $index }}][descripcion]"
                                                            class="form-control form-control-sm item-descripcion"
                                                            placeholder="Descripción del producto" required
                                                            autocomplete="off" value="{{ $item->descripcion }}">
                                                        <input type="hidden" name="items[{{ $index }}][articulo_id]"
                                                            class="item-articulo-id" value="{{ $item->articulo_id }}">
                                                        <div class="list-group position-absolute w-100 shadow-sm item-descripcion-results"
                                                            style="z-index: 1000; display: none;"></div>
                                                    </div>
                                                </td>
                                                <td><input type="number" name="items[{{ $index }}][cantidad]"
                                                        class="form-control form-control-sm item-cantidad" value="{{ $item->cantidad }}"
                                                        required min="1"></td>
                                                <td><input type="number" step="0.01" name="items[{{ $index }}][precio_unitario]"
                                                        class="form-control form-control-sm item-precio" value="{{ $item->precio_unitario }}"
                                                        required min="0"></td>
                                                <td><input type="number" step="0.01" name="items[{{ $index }}][bonificacion]"
                                                        class="form-control form-control-sm item-bonif" value="{{ $item->bonificacion }}"
                                                        min="0"></td>
                                                <td>
                                                    <span class="item-total-text fw-bold">$ {{ number_format($item->total, 2, ',', '.') }}</span>
                                                    <input type="hidden" name="items[{{ $index }}][total]" class="item-total" value="{{ $item->total }}">
                                                </td>
                                                <td class="text-center">
                                                    @if($index > 0)
                                                    <button type="button" class="btn btn-sm btn-danger remove-item"><i class="bi bi-x"></i></button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-light">
                                                <td colspan="5" class="text-end fw-bold">TOTAL ENVÍO:</td>
                                                <td class="fw-bold"><span id="total_envio_display">$ {{ number_format($shipment->total_flete, 2) }}</span></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="notas" class="form-label fw-semibold">Notas / Observaciones</label>
                                <textarea name="notas" id="notas" rows="3" class="form-control">{{ $shipment->notas }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="ref_remito" class="form-label fw-bold">Referencia Remito</label>
                                <input type="text" name="ref_remito" id="ref_remito" class="form-control" value="{{ old('ref_remito', $shipment->ref_remito) }}" placeholder="Ej: R-001234">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-4">
                            <a href="{{ route('shipments.index') }}" class="btn btn-light px-4">Cancelar</a>
                            <button type="submit" class="btn btn-warning px-4 text-white fw-bold">Actualizar Guía</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para creación rápida de cliente -->
<div class="modal fade" id="quickClientModal" tabindex="-1" aria-labelledby="quickClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="quickClientForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quickClientModalLabel">Nuevo Cliente Rápido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="qc_nombre_fantasia" class="form-label">Nombre / Razón Social</label>
                        <input type="text" class="form-control" id="qc_nombre_fantasia" required>
                    </div>
                    <div class="mb-3">
                        <label for="qc_direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="qc_direccion">
                    </div>
                    <input type="hidden" id="qc_target_input">
                    <input type="hidden" id="qc_target_hidden">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="qc_submit_btn">Guardar Cliente</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const quickClientModalElement = document.getElementById('quickClientModal');
        const quickClientModal = new bootstrap.Modal(quickClientModalElement);

        function calculateRowTotal(row) {
            const qty = parseFloat(row.querySelector('.item-cantidad').value) || 0;
            const price = parseFloat(row.querySelector('.item-precio').value) || 0;
            const bonif = parseFloat(row.querySelector('.item-bonif').value) || 0;

            // Nueva formula: cantidad * precio_unitario * bonificacion / 100
            let total = (qty * price * bonif) / 100;

            row.querySelector('.item-total').value = total.toFixed(2);
            row.querySelector('.item-total-text').innerText = '$ ' + total.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            let grandTotal = 0;
            document.querySelectorAll('.item-total').forEach(input => {
                grandTotal += parseFloat(input.value) || 0;
            });
            document.getElementById('total_envio_display').innerText = '$ ' + grandTotal.toFixed(2);
        }

        document.getElementById('addItem').addEventListener('click', function () {
            const tableBody = document.querySelector('#itemsTable tbody');
            const rowCount = tableBody.querySelectorAll('tr').length;
            const newRow = document.createElement('tr');
            newRow.className = 'item-row';
            newRow.innerHTML = `
                <td>
                    <div class="position-relative">
                        <input type="text" class="form-control form-control-sm item-codigo" placeholder="Código" autocomplete="off">
                        <div class="list-group position-absolute w-100 shadow-sm item-codigo-results" style="z-index: 1000; display: none;"></div>
                    </div>
                </td>
                <td>
                    <div class="position-relative">
                        <input type="text" name="items[${rowCount}][descripcion]" class="form-control form-control-sm item-descripcion" placeholder="Descripción del producto" required autocomplete="off">
                        <input type="hidden" name="items[${rowCount}][articulo_id]" class="item-articulo-id">
                        <div class="list-group position-absolute w-100 shadow-sm item-descripcion-results" style="z-index: 1000; display: none;"></div>
                    </div>
                </td>
                <td><input type="number" name="items[${rowCount}][cantidad]" class="form-control form-control-sm item-cantidad" value="1" required min="1"></td>
                <td><input type="number" step="0.01" name="items[${rowCount}][precio_unitario]" class="form-control form-control-sm item-precio" value="0.00" required min="0"></td>
                <td><input type="number" step="0.01" name="items[${rowCount}][bonificacion]" class="form-control form-control-sm item-bonif" value="100.00" min="0"></td>
                <td>
                    <span class="item-total-text fw-bold">$ 0.00</span>
                    <input type="hidden" name="items[${rowCount}][total]" class="item-total" value="0.00">
                </td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-item"><i class="bi bi-x"></i></button></td>
            `;
            tableBody.appendChild(newRow);

            setupItemAutocomplete(newRow);
            setupRowListeners(newRow);
        });

        document.querySelector('#itemsTable').addEventListener('click', function (e) {
            if (e.target.closest('.remove-item')) {
                e.target.closest('tr').remove();
                calculateGrandTotal();
            }
        });

        function setupAutocomplete(inputId, hiddenId, resultsId) {
            const input = document.getElementById(inputId);
            const hidden = document.getElementById(hiddenId);
            const results = document.getElementById(resultsId);
            let timeout = null;

            input.addEventListener('input', function () {
                clearTimeout(timeout);
                const q = this.value.trim();
                if (q.length < 2) {
                    results.style.display = 'none';
                    return;
                }

                timeout = setTimeout(() => {
                    const url = `{{ route('clientes.search') }}?q=${encodeURIComponent(q)}&activo=1`;
                    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(res => res.json())
                        .then(data => {
                            results.innerHTML = '';
                            if (data.length === 0) {
                                results.innerHTML = `
                                <div class="list-group-item text-muted">No se encontraron clientes</div>
                                <a href="#" class="list-group-item list-group-item-action text-primary text-center fw-bold btn-quick-client" 
                                   data-target-input="${inputId}" data-target-hidden="${hiddenId}">
                                    <i class="bi bi-plus-circle"></i> Agregar Cliente
                                </a>
                            `;
                                results.style.display = 'block';
                                return;
                            }
                            data.forEach(client => {
                                const a = document.createElement('a');
                                a.href = '#'; a.className = 'list-group-item list-group-item-action py-2';
                                a.innerHTML = `<strong>${client.nombre_fantasia}</strong> <br><small class="text-muted">Doc: ${client.documento_nro}</small>`;
                                a.onclick = function (e) {
                                    e.preventDefault();
                                    input.value = client.nombre_fantasia;
                                    hidden.value = client.id;
                                    results.style.display = 'none';

                                    if (inputId === 'sender_search') {
                                        if (client.agenciaorigen_id) {
                                            document.getElementById('origin_agency_id').value = client.agenciaorigen_id;
                                        }
                                        document.getElementById('receiver_search').focus();
                                    }

                                    if (inputId === 'receiver_search') {
                                        document.getElementById('direccion_entrega').value = client.direccion || '';
                                        if (client.agenciadestino_id) {
                                            document.getElementById('destination_agency_id').value = client.agenciadestino_id;
                                        }
                                    }
                                };
                                results.appendChild(a);
                            });
                            results.style.display = 'block';
                        });
                }, 300);
            });

            document.addEventListener('click', function (e) {
                if (e.target !== input && e.target !== results && !results.contains(e.target)) {
                    results.style.display = 'none';
                }
            });
        }

        function setupItemAutocomplete(row) {
            const codigoInput = row.querySelector('.item-codigo');
            const codigoResults = row.querySelector('.item-codigo-results');
            const descInput = row.querySelector('.item-descripcion');
            const descResults = row.querySelector('.item-descripcion-results');
            const articuloIdInput = row.querySelector('.item-articulo-id');
            const precioInput = row.querySelector('.item-precio');
            const cantidadInput = row.querySelector('.item-cantidad');

            function handleSearch(input, results, field) {
                let timeout = null;
                input.addEventListener('input', function () {
                    clearTimeout(timeout);
                    const q = this.value.trim();
                    if (q.length < 1) {
                        results.style.display = 'none';
                        return;
                    }
                    timeout = setTimeout(() => {
                        const url = `{{ route('articulos.search') }}?q=${encodeURIComponent(q)}`;
                        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(res => res.json())
                            .then(data => {
                                results.innerHTML = '';
                                if (data.length === 0) {
                                    results.style.display = 'none';
                                    return;
                                }
                                data.forEach(articulo => {
                                    const a = document.createElement('a');
                                    a.href = '#'; a.className = 'list-group-item list-group-item-action py-2';
                                    a.innerHTML = `<strong>${articulo.nombre}</strong> <small class="text-muted">(${articulo.codigo})</small>`;
                                    a.addEventListener('click', function (e) {
                                        e.preventDefault();
                                        codigoInput.value = articulo.codigo;
                                        // "para verlo por pantalla aparece el campo nombre"
                                        descInput.value = articulo.nombre;
                                        articuloIdInput.value = articulo.id;
                                        precioInput.value = articulo.precio;
                                        cantidadInput.value = 1;
                                        results.style.display = 'none';
                                        calculateRowTotal(row);
                                    });
                                    results.appendChild(a);
                                });
                                results.style.display = 'block';
                            });
                    }, 300);
                });
            }

            handleSearch(codigoInput, codigoResults, 'codigo');
            handleSearch(descInput, descResults, 'nombre');

            document.addEventListener('click', function (e) {
                if (e.target !== codigoInput && !codigoResults.contains(e.target)) codigoResults.style.display = 'none';
                if (e.target !== descInput && !descResults.contains(e.target)) descResults.style.display = 'none';
            });
        }

        function setupRowListeners(row) {
            row.querySelectorAll('input').forEach(input => {
                input.addEventListener('change', () => calculateRowTotal(row));
                input.addEventListener('keyup', () => calculateRowTotal(row));
            });
        }

        // Initialize for existing rows
        document.querySelectorAll('.item-row').forEach(row => {
            setupItemAutocomplete(row);
            setupRowListeners(row);
        });

        setupAutocomplete('sender_search', 'sender_id', 'sender_results');
        setupAutocomplete('receiver_search', 'receiver_id', 'receiver_results');

        // Form submission cleanup
        const shipmentForm = document.getElementById('shipmentForm');
        shipmentForm.addEventListener('submit', function (e) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 0) {
                const lastRow = rows[rows.length - 1];
                const artId = lastRow.querySelector('.item-articulo-id').value;
                const desc = lastRow.querySelector('.item-descripcion').value.trim();
                const codigo = lastRow.querySelector('.item-codigo').value.trim();
                
                // Si no hay articulo seleccionado (id o descripcion o codigo), eliminarla
                if (!artId && !desc && !codigo && rows.length > 1) {
                    lastRow.remove();
                }
            }
        });

        // Event delegation para el botón "Agregar Cliente" (generado dinámicamente con innerHTML)
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-quick-client');
            if (!btn) return;
            e.preventDefault();

            const targetInputId = btn.getAttribute('data-target-input');
            const targetHiddenId = btn.getAttribute('data-target-hidden');

            // Completamos el nombre sugerido desde el campo de búsqueda
            const searchInput = document.getElementById(targetInputId);
            document.getElementById('qc_nombre_fantasia').value = searchInput ? searchInput.value.trim() : '';
            document.getElementById('qc_direccion').value = '';
            document.getElementById('qc_target_input').value = targetInputId;
            document.getElementById('qc_target_hidden').value = targetHiddenId;

            // Ocultar el dropdown antes de abrir el modal
            const resultsEl = document.getElementById(targetInputId === 'sender_search' ? 'sender_results' : 'receiver_results');
            if (resultsEl) resultsEl.style.display = 'none';

            quickClientModal.show();
        });

        // Auto-select text on focus in form inputs
        shipmentForm.addEventListener('focus', function (e) {
            if (e.target.tagName === 'INPUT' && (e.target.type === 'text' || e.target.type === 'number')) {
                e.target.select();
            }
        }, true);

        document.getElementById('quickClientForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('qc_submit_btn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';
            fetch('{{ route("clientes.storeQuick") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ nombre_fantasia: document.getElementById('qc_nombre_fantasia').value, direccion: document.getElementById('qc_direccion').value })
            })
                .then(res => res.json())
                .then(client => {
                    const tInputId = document.getElementById('qc_target_input').value;
                    const tHiddenId = document.getElementById('qc_target_hidden').value;
                    document.getElementById(tInputId).value = client.nombre_fantasia;
                    document.getElementById(tHiddenId).value = client.id;
                    if (tInputId === 'receiver_search') document.getElementById('direccion_entrega').value = client.direccion || '';
                    quickClientModal.hide(); btn.disabled = false; btn.innerHTML = 'Guardar Cliente';
                })
                .catch(() => { alert('Error al crear cliente'); btn.disabled = false; btn.innerHTML = 'Guardar Cliente'; });
        });
    });
</script>
@endpush
@endsection