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

    .list-group-item-action:hover, .list-group-item-action.active {
        background-color: #e9ecef !important;
        color: inherit !important;
    }

    .item-total-display {
        font-weight: bold;
    }

    /* QZ-Tray Print Status Modal */
    #printStatusModal .modal-header.bg-success { border-radius: 0.3rem 0.3rem 0 0; }
    #printStatusModal .modal-header.bg-danger  { border-radius: 0.3rem 0.3rem 0 0; }
    .qz-status-icon { font-size: 3rem; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-truck me-2 text-primary"></i>Nueva Guía de Flete</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('shipments.store') }}" method="POST" id="shipmentForm">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="sender_search" class="form-label fw-semibold">Remitente</label>
                                <div class="position-relative">
                                    <input type="text" id="sender_search" class="form-control"
                                        placeholder="Buscar cliente..." autocomplete="off">
                                    <input type="hidden" name="sender_id" id="sender_id">
                                    <div id="sender_results" class="list-group position-absolute w-100 shadow-sm"
                                        style="z-index: 1000; display: none;"></div>
                                </div>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="payer" id="payer_sender"
                                            value="sender" checked>
                                        <label class="form-check-label text-primary fw-bold" for="payer_sender">Paga
                                            Remitente</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="receiver_search" class="form-label fw-semibold">Destinatario</label>
                                <div class="position-relative">
                                    <input type="text" id="receiver_search" class="form-control"
                                        placeholder="Buscar cliente..." autocomplete="off">
                                    <input type="hidden" name="receiver_id" id="receiver_id">
                                    <div id="receiver_results" class="list-group position-absolute w-100 shadow-sm"
                                        style="z-index: 1000; display: none;"></div>
                                </div>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="payer" id="payer_receiver"
                                            value="receiver">
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
                                    <option value="{{ $agency->id }}">{{ $agency->nombre }}</option>
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
                                    <option value="{{ $agency->id }}">{{ $agency->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            @if($carriers->count() === 1)
                            <input type="hidden" name="carrier_id" value="{{ $carriers->first()->id }}">
                            @else
                            <div class="col-md-4 mb-3">
                                <label for="carrier_id" class="form-label fw-semibold">Transportista</label>
                                <select name="carrier_id" id="carrier_id" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($carriers as $carrier)
                                    <option value="{{ $carrier->id }}">{{ $carrier->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="{{ $carriers->count() === 1 ? 'col-md-6' : 'col-md-4' }} mb-3">
                                <label for="forma_pago_id" class="form-label fw-semibold">Forma de Pago</label>
                                <select name="forma_pago_id" id="forma_pago_id" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($formasPago as $fp)
                                    <option value="{{ $fp->id }}">{{ $fp->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="{{ $carriers->count() === 1 ? 'col-md-6' : 'col-md-4' }} mb-3">
                                <label for="direccion_entrega" class="form-label fw-semibold">Lugar de Entrega</label>
                                <input type="text" name="direccion_entrega" id="direccion_entrega" class="form-control"
                                    placeholder="Dirección de entrega">
                            </div>
                        </div>

                        <div class="card mb-4 border-light bg-light bg-opacity-10">
                            <div
                                class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">Artículos / Bultos</h6>
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
                                            <tr class="item-row">
                                                <td>
                                                    <div class="position-relative">
                                                        <input type="text"
                                                            class="form-control form-control-sm item-codigo"
                                                            placeholder="Código" autocomplete="off">
                                                        <div class="list-group position-absolute w-100 shadow-sm item-codigo-results"
                                                            style="z-index: 1000; display: none;"></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="position-relative">
                                                        <input type="text" name="items[0][descripcion]"
                                                            class="form-control form-control-sm item-descripcion"
                                                            placeholder="Descripción del producto"
                                                            autocomplete="off">
                                                        <input type="hidden" name="items[0][articulo_id]"
                                                            class="item-articulo-id">
                                                        <div class="list-group position-absolute w-100 shadow-sm item-descripcion-results"
                                                            style="z-index: 1000; display: none;"></div>
                                                    </div>
                                                </td>
                                                <td><input type="number" name="items[0][cantidad]"
                                                        class="form-control form-control-sm item-cantidad" value="1"
                                                        required min="1"></td>
                                                <td><input type="number" step="0.01" name="items[0][precio_unitario]"
                                                        class="form-control form-control-sm item-precio" value="0.00"
                                                        required min="0"></td>
                                                <td><input type="number" step="0.01" name="items[0][bonificacion]"
                                                        class="form-control form-control-sm item-bonif" value="100.00"
                                                        min="0"></td>
                                                <td>
                                                    <input type="number" step="0.01" name="items[0][total]"
                                                        class="form-control form-control-sm item-total" value="0.00"
                                                        readonly>
                                                </td>
                                                <td class="text-center"></td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-light">
                                                <td colspan="5" class="text-end fw-bold">TOTAL ENVÍO:</td>
                                                <td class="fw-bold"><span id="total_envio_display">$ 0.00</span></td>
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
                                <textarea name="notas" id="notas" rows="3" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="ref_remito" class="form-label fw-bold">Referencia Remito</label>
                                <input type="text" name="ref_remito" id="ref_remito" class="form-control" placeholder="Ej: R-001234">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-4">
                            <a href="{{ route('shipments.index') }}" class="btn btn-light px-4">Cancelar</a>
                            <button type="button" id="btnCrearSinImprimir" class="btn btn-outline-primary px-4">
                                <i class="bi bi-save me-1"></i>Crear sin Imprimir
                            </button>
                            <button type="button" id="btnCrearGuia" class="btn btn-primary px-4">
                                <i class="bi bi-printer me-1"></i>Crear e Imprimir Guía
                            </button>
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

<!-- Modal de estado de impresión QZ-Tray -->
<div class="modal fade" id="printStatusModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white" id="printModalHeader">
                <h5 class="modal-title" id="printModalTitle">
                    <span class="spinner-border spinner-border-sm me-2" id="printSpinner"></span>
                    Guardando guía...
                </h5>
            </div>
            <div class="modal-body text-center py-4">
                <div id="printStatusContent">
                    <div class="spinner-border text-primary mb-3" style="width:3rem;height:3rem;"></div>
                    <p class="fs-5 mb-1" id="printStatusText">Creando guía en el sistema...</p>
                    <p class="text-muted small" id="printStatusSub">Por favor espere</p>
                </div>
            </div>
            <div class="modal-footer justify-content-center" id="printModalFooter" style="display:none !important;">
                <a id="btnGoToList" href="{{ route('shipments.index') }}" class="btn btn-primary">
                    <i class="bi bi-list-ul me-1"></i>Ver listado de guías
                </a>
                <button id="btnRetryPrint" class="btn btn-outline-secondary" style="display:none;">
                    <i class="bi bi-arrow-clockwise me-1"></i>Reintentar impresión
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- QZ-Tray library --}}
<script src="https://cdn.jsdelivr.net/npm/qz-tray@2.2.4/qz-tray.js"></script>
<script>
    // ============================================================
    // QZ-Tray: Impresión directa al guardar la guía
    // ============================================================
    let createdShipmentPrintBase64Url = null;
    let createdTrackingNumber = null;
    let printStatusModal = null;

    function updatePrintModal(phase, errorDetail) {
        const header   = document.getElementById('printModalHeader');
        const title    = document.getElementById('printModalTitle');
        const text     = document.getElementById('printStatusText');
        const sub      = document.getElementById('printStatusSub');
        const footer   = document.getElementById('printModalFooter');
        const btnRetry = document.getElementById('btnRetryPrint');

        // Reset classes
        header.className = 'modal-header';

        if (phase === 'saving') {
            header.classList.add('bg-primary', 'text-white');
            title.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando guía...';
            text.textContent = 'Creando guía en el sistema...';
            sub.textContent = 'Por favor espere';
            footer.style.setProperty('display', 'none', 'important');
            btnRetry.style.display = 'none';
        } else if (phase === 'connecting') {
            header.classList.add('bg-primary', 'text-white');
            title.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Conectando con QZ-Tray...';
            text.textContent = 'Conectando con el servicio de impresión local (puerto 8181)...';
            sub.textContent = 'Asegúrese de que QZ-Tray esté instalado y ejecutándose';
            footer.style.setProperty('display', 'none', 'important');
        } else if (phase === 'printing') {
            header.classList.add('bg-primary', 'text-white');
            title.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando a impresora...';
            text.textContent = 'Enviando guía ' + (createdTrackingNumber || '') + ' a la impresora...';
            sub.textContent = 'Los documentos están siendo enviados a la impresora por defecto';
            footer.style.setProperty('display', 'none', 'important');
        } else if (phase === 'success') {
            header.classList.add('bg-success', 'text-white');
            title.innerHTML = '<i class="bi bi-check-circle me-2"></i>¡Guía impresa correctamente!';
            text.textContent = 'Guía ' + (createdTrackingNumber || '') + ' creada e impresa exitosamente.';
            sub.textContent = 'La impresión fue enviada a la impresora por defecto.';
            footer.style.removeProperty('display');
            btnRetry.style.display = 'none';
        } else if (phase === 'qz_error') {
            header.classList.add('bg-warning', 'text-dark');
            title.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>No se pudo imprimir automáticamente';
            text.textContent = 'La guía ' + (createdTrackingNumber || '') + ' fue creada correctamente.';
            // Mostrar el detalle real del error si existe
            if (errorDetail) {
                sub.innerHTML = '<span class="text-danger"><strong>Error:</strong> ' + errorDetail + '</span><br><small class="text-muted">Habilite "Allow unsigned requests" en QZ-Tray &rsaquo; Preferencias</small>';
            } else {
                sub.textContent = 'QZ-Tray no está disponible o no permite impresión no firmada.';
            }
            footer.style.removeProperty('display');
            btnRetry.style.display = 'inline-flex';
        } else if (phase === 'error') {
            header.classList.add('bg-danger', 'text-white');
            title.innerHTML = '<i class="bi bi-x-circle me-2"></i>Error';
            text.textContent = 'Ocurrió un error al procesar la solicitud.';
            sub.textContent = errorDetail || '';
            footer.style.removeProperty('display');
            btnRetry.style.display = 'none';
        }
    }

    // Configuración de impresora desde la Empresa
    const configuredPrinter = "{{ $empresa->qz_printer ?? '' }}";

    async function printWithQzTray(pdfBase64) {
        updatePrintModal('connecting');

        // Certificado de firma dinámico
        qz.security.setCertificatePromise(function(resolve, reject) {
            fetch('{{ route("qz.certificate") }}')
                .then(r => r.ok ? r.text() : reject('No se encontró certificado'))
                .then(resolve)
                .catch(reject);
        });

        qz.security.setSignatureAlgorithm('SHA512');
        qz.security.setSignaturePromise(function(toSign) {
            return function(resolve, reject) {
                // Firmar en el servidor (más robusto y no requiere HTTPS para SubtleCrypto)
                fetch('{{ route("qz.sign") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ toSign: toSign })
                })
                .then(r => {
                    if (!r.ok) throw new Error('Error en el servidor al firmar');
                    return r.text();
                })
                .then(resolve)
                .catch(reject);
            };
        });

        try {
            if (!qz.websocket.isActive()) {
                await qz.websocket.connect({ retries: 2, delay: 1 });
            }

            updatePrintModal('printing');

            let printer;
            if (configuredPrinter) {
                // Intentamos buscar la impresora configurada
                try {
                    printer = await qz.printers.find(configuredPrinter);
                } catch(e) {
                    console.warn('No se encontró la impresora configurada, intentando por defecto');
                }
            }

            if (!printer) {
                printer = await qz.printers.getDefault();
            }

            if (!printer) {
                throw new Error('No se encontró ninguna impresora utilizable.');
            }

            console.log('[QZ-Tray] Imprimiendo en:', printer);

            const config = qz.configs.create(printer);
            await qz.print(config, [{ type: 'pixel', format: 'pdf', flavor: 'base64', data: pdfBase64 }]);
            updatePrintModal('success');

            // Redirigir automáticamente al listado después de 2 segundos de éxito
            setTimeout(() => {
                window.location.href = "{{ route('shipments.index') }}";
            }, 2000);

        } catch (err) {

            console.error('[QZ-Tray] Error:', err);
            updatePrintModal('qz_error', err.message || err.toString());
        }
    }



    document.getElementById('btnRetryPrint').addEventListener('click', async function() {
        if (!createdShipmentPrintBase64Url) return;

        this.disabled = true;
        updatePrintModal('connecting');

        try {
            const resp = await fetch(createdShipmentPrintBase64Url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await resp.json();
            if (data.success) {
                await printWithQzTray(data.pdf_base64);
            }
        } catch(e) {
            updatePrintModal('qz_error');
        }

        this.disabled = false;
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        printStatusModal = new bootstrap.Modal(document.getElementById('printStatusModal'));
        const quickClientModalElement = document.getElementById('quickClientModal');
        const quickClientModal = new bootstrap.Modal(quickClientModalElement);

        quickClientModalElement.addEventListener('shown.bs.modal', function () {
            document.getElementById('qc_direccion').focus();
        });

        const shipmentForm = document.getElementById('shipmentForm');

        function calculateRowTotal(row) {
            const qty = parseFloat(row.querySelector('.item-cantidad').value) || 0;
            const price = parseFloat(row.querySelector('.item-precio').value) || 0;
            const bonif = parseFloat(row.querySelector('.item-bonif').value) || 0;

            // Formula: cantidad * precio_unitario * bonificacion / 100
            let total = (qty * price * bonif) / 100;

            row.querySelector('.item-total').value = total.toFixed(2);
            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            let grandTotal = 0;
            document.querySelectorAll('.item-total').forEach(input => {
                grandTotal += parseFloat(input.value) || 0;
            });
            const formattedGrandTotal = grandTotal.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('total_envio_display').innerText = '$ ' + formattedGrandTotal;
        }

        function addNewRow() {
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
                        <input type="text" name="items[${rowCount}][descripcion]" class="form-control form-control-sm item-descripcion" placeholder="Descripción del producto" autocomplete="off">
                        <input type="hidden" name="items[${rowCount}][articulo_id]" class="item-articulo-id">
                        <div class="list-group position-absolute w-100 shadow-sm item-descripcion-results" style="z-index: 1000; display: none;"></div>
                    </div>
                </td>
                <td><input type="number" name="items[${rowCount}][cantidad]" class="form-control form-control-sm item-cantidad" value="1" required min="1"></td>
                <td><input type="number" step="0.01" name="items[${rowCount}][precio_unitario]" class="form-control form-control-sm item-precio" value="0.00" required min="0"></td>
                <td><input type="number" step="0.01" name="items[${rowCount}][bonificacion]" class="form-control form-control-sm item-bonif" value="100.00" min="0"></td>
                <td><input type="number" step="0.01" name="items[${rowCount}][total]" class="form-control form-control-sm item-total" value="0.00" readonly></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-item"><i class="bi bi-x"></i></button></td>
            `;
            tableBody.appendChild(newRow);

            setupItemAutocomplete(newRow);
            setupRowListeners(newRow);
            
            return newRow;
        }

        document.querySelector('#itemsTable').addEventListener('click', function (e) {
            if (e.target.closest('.remove-item')) {
                const rows = document.querySelectorAll('.item-row');
                if (rows.length > 1) {
                    e.target.closest('tr').remove();
                    calculateGrandTotal();
                } else {
                    // Si es la única fila, solo limpiarla
                    const row = rows[0];
                    row.querySelector('.item-codigo').value = '';
                    row.querySelector('.item-descripcion').value = '';
                    row.querySelector('.item-articulo-id').value = '';
                    row.querySelector('.item-cantidad').value = 1;
                    row.querySelector('.item-precio').value = '0.00';
                    row.querySelector('.item-bonif').value = '0.00';
                    row.querySelector('.item-total').value = '0.00';
                    calculateGrandTotal();
                }
            }
        });

        function setupAutocomplete(inputId, hiddenId, resultsId) {
            const input = document.getElementById(inputId);
            const hidden = document.getElementById(hiddenId);
            const results = document.getElementById(resultsId);
            let timeout = null;
            let currentFocus = -1;

            function addActive(x) {
                if (!x) return false;
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = (x.length - 1);
                x[currentFocus].classList.add("active");
            }

            function removeActive(x) {
                for (let i = 0; i < x.length; i++) {
                    x[i].classList.remove("active");
                }
            }

            input.addEventListener('keydown', function (e) {
                let x = results.getElementsByClassName("list-group-item-action");
                if (e.key === "ArrowDown") {
                    currentFocus++;
                    addActive(x);
                } else if (e.key === "ArrowUp") {
                    currentFocus--;
                    addActive(x);
                } else if (e.key === "Enter") {
                    if (currentFocus > -1) {
                        if (x[currentFocus]) x[currentFocus].click();
                        e.preventDefault();
                    }
                }
            });

            input.addEventListener('input', function () {
                clearTimeout(timeout);
                const q = this.value.trim();
                currentFocus = -1;
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
                            data.forEach((client, index) => {
                                const a = document.createElement('a');
                                a.href = '#'; 
                                a.className = 'list-group-item list-group-item-action py-2 d-flex justify-content-between align-items-center client-search-item';
                                a.innerHTML = `
                                    <div class="flex-grow-1">
                                        <strong>${client.nombre_fantasia}</strong> <br>
                                        <small class="text-muted">Dir: ${client.direccion || ''}</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger border-0 delete-client-ajax" data-id="${client.id}" title="Eliminar cliente">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                `;
                                
                                a.onclick = function (e) {
                                    if (e.target.closest('.delete-client-ajax')) return;
                                    e.preventDefault();
                                    input.value = client.nombre_fantasia;
                                    hidden.value = client.id;
                                    results.style.display = 'none';

                                    if (inputId === 'sender_search') {
                                        if (client.agenciaorigen_id) {
                                            document.getElementById('origin_agency_id').value = client.agenciaorigen_id;
                                        }
                                    }

                                    if (inputId === 'receiver_search') {
                                        document.getElementById('direccion_entrega').value = client.direccion || '';
                                        if (client.agenciadestino_id) {
                                            document.getElementById('destination_agency_id').value = client.agenciadestino_id;
                                        }
                                    }
                                };

                                const btnDel = a.querySelector('.delete-client-ajax');
                                btnDel.onclick = function(e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    
                                    function performDelete(force = false) {
                                        const originalContent = btnDel.innerHTML;
                                        btnDel.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                                        btnDel.disabled = true;

                                        let url = `/clientes/${client.id}/ajax`;
                                        if (force) url += '?force=1';

                                        fetch(url, {
                                            method: 'DELETE',
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                                                'Accept': 'application/json'
                                            }
                                        })
                                        .then(res => res.json())
                                        .then(resData => {
                                            if (resData.success) {
                                                a.remove();
                                                if (results.querySelectorAll('.client-search-item').length === 0) {
                                                    results.style.display = 'none';
                                                }
                                            } else if (resData.has_shipments) {
                                                btnDel.innerHTML = originalContent;
                                                btnDel.disabled = false;
                                                if (confirm(`${resData.message}\n\n¿Desea eliminarlo de todas formas?`)) {
                                                    performDelete(true);
                                                }
                                            } else {
                                                alert(resData.message);
                                                btnDel.innerHTML = originalContent;
                                                btnDel.disabled = false;
                                            }
                                        })
                                        .catch(err => {
                                            console.error(err);
                                            alert('Error al intentar eliminar el cliente.');
                                            btnDel.innerHTML = originalContent;
                                            btnDel.disabled = false;
                                        });
                                    }

                                    if (confirm('¿Está seguro de eliminar este cliente?')) {
                                        performDelete();
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
                let currentFocus = -1;

                function addActive(x) {
                    if (!x) return false;
                    removeActive(x);
                    if (currentFocus >= x.length) currentFocus = 0;
                    if (currentFocus < 0) currentFocus = (x.length - 1);
                    x[currentFocus].classList.add("active");
                }

                function removeActive(x) {
                    for (let i = 0; i < x.length; i++) {
                        x[i].classList.remove("active");
                    }
                }

                input.addEventListener('keydown', function (e) {
                    let x = results.getElementsByClassName("list-group-item-action");
                    if (e.key === "ArrowDown") {
                        currentFocus++;
                        addActive(x);
                    } else if (e.key === "ArrowUp") {
                        currentFocus--;
                        addActive(x);
                    } else if (e.key === "Enter") {
                        if (currentFocus > -1) {
                            if (x[currentFocus]) x[currentFocus].click();
                            e.preventDefault();
                        }
                    }
                });

                input.addEventListener('input', function () {
                    clearTimeout(timeout);
                    const q = this.value.trim();
                    currentFocus = -1;
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
                                        // Enfocar en cantidad después de seleccionar
                                        cantidadInput.focus();
                                        cantidadInput.select();
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
            const qtyInput = row.querySelector('.item-cantidad');
            const priceInput = row.querySelector('.item-precio');
            const bonifInput = row.querySelector('.item-bonif');
            const codigoInput = row.querySelector('.item-codigo');

            row.querySelectorAll('input').forEach(input => {
                input.addEventListener('change', () => calculateRowTotal(row));
                input.addEventListener('keyup', () => calculateRowTotal(row));
            });

            // Enter navigation
            qtyInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    priceInput.focus();
                    priceInput.select();
                }
            });

            priceInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    // Aceptar la línea y añadir una nueva
                    const nextRow = addNewRow();
                    nextRow.querySelector('.item-codigo').focus();
                }
            });

            bonifInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    // Aceptar la línea y añadir una nueva
                    const nextRow = addNewRow();
                    nextRow.querySelector('.item-codigo').focus();
                }
            });
        }

        // Initialize for existing rows
        document.querySelectorAll('.item-row').forEach(row => {
            setupItemAutocomplete(row);
            setupRowListeners(row);
        });

        setupAutocomplete('sender_search', 'sender_id', 'sender_results');
        setupAutocomplete('receiver_search', 'receiver_id', 'receiver_results');

        // Prevent Enter from submitting the form globally
        shipmentForm.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
            }
        });

        // -------------------------------------------------------
        // Submit via click only (buttons are type="button")
        // -------------------------------------------------------
        document.getElementById('btnCrearGuia').addEventListener('click', function() {
            saveShipment(true);
        });
        async function saveShipment(shouldPrint = true) {
            const originAgencyId = document.getElementById('origin_agency_id').value;
            const destinationAgencyId = document.getElementById('destination_agency_id').value;
            
            if (originAgencyId && destinationAgencyId && originAgencyId === destinationAgencyId) {
                if (!confirm('ATENCIÓN: La Agencia Origen y la Agencia Destino seleccionadas son la misma.\n\n¿Desea crear la guía de todas formas?')) {
                    return;
                }
            }

            // Cleanup empty last row
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                const lastRow = rows[rows.length - 1];
                const artId  = lastRow.querySelector('.item-articulo-id').value;
                const desc   = lastRow.querySelector('.item-descripcion').value.trim();
                const codigo = lastRow.querySelector('.item-codigo').value.trim();
                if (!artId && !desc && !codigo) lastRow.remove();
            }

            // Validación mínima: Al menos una fila con descripción
            const remainingRows = document.querySelectorAll('.item-row');
            let hasValidItem = false;
            remainingRows.forEach(row => {
                if (row.querySelector('.item-descripcion').value.trim() !== '') hasValidItem = true;
            });
            if (!hasValidItem) {
                alert('Debe agregar al menos un artículo con descripción.');
                return;
            }

            // Deshabilitar botones y mostrar modal
            const btnSubmit = document.getElementById('btnCrearGuia');
            const btnNoPrint = document.getElementById('btnCrearSinImprimir');
            btnSubmit.disabled = true;
            btnNoPrint.disabled = true;

            updatePrintModal('saving');
            printStatusModal.show();

            // Recopilar datos del form
            const formData = new FormData(shipmentForm);

            try {
                // 1. Guardar la guía via AJAX
                const saveResp = await fetch(shipmentForm.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                if (saveResp.status === 422) {
                    const errData = await saveResp.json();
                    printStatusModal.hide();
                    btnSubmit.disabled = false;
                    btnNoPrint.disabled = false;
                    const errMessages = Object.values(errData.errors || {}).flat().join('\n');
                    alert('Error de validación:\n' + (errMessages || 'Revise los campos del formulario.'));
                    return;
                }

                if (!saveResp.ok) {
                    printStatusModal.hide();
                    btnSubmit.disabled = false;
                    btnNoPrint.disabled = false;
                    alert('Error del servidor al guardar la guía. Intente nuevamente.');
                    return;
                }

                const saveData = await saveResp.json();

                if (!saveData.success) {
                    updatePrintModal('error');
                    btnSubmit.disabled = false;
                    btnNoPrint.disabled = false;
                    console.error('Error al guardar:', saveData);
                    return;
                }

                createdTrackingNumber         = saveData.tracking_number;
                createdShipmentPrintBase64Url = saveData.print_base64_url;

                if (shouldPrint) {
                    // 2. Obtener PDF en Base64
                    updatePrintModal('connecting');
                    const pdfResp = await fetch(saveData.print_base64_url, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const pdfData = await pdfResp.json();

                    if (!pdfData.success) {
                        updatePrintModal('qz_error');
                        return;
                    }

                    // 3. Imprimir con QZ-Tray
                    await printWithQzTray(pdfData.pdf_base64);
                } else {
                    // Éxito sin imprimir: Redirigir inmediatamente
                    updatePrintModal('success');
                    setTimeout(() => {
                        window.location.href = "{{ route('shipments.index') }}";
                    }, 500);
                }

            } catch (err) {
                console.error('Error en el proceso de creación/impresión:', err);
                updatePrintModal('error');
                btnSubmit.disabled = false;
                btnNoPrint.disabled = false;
            }
        }

        document.getElementById('btnCrearSinImprimir').addEventListener('click', function(e) {
            saveShipment(false);
        });

        // Event delegation para el botón "Agregar Cliente" (generado dinámicamente con innerHTML)
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-quick-client');
            if (!btn) return;
            e.preventDefault();

            const targetInputId = btn.getAttribute('data-target-input');
            const targetHiddenId = btn.getAttribute('data-target-hidden');

            const searchInput = document.getElementById(targetInputId);
            document.getElementById('qc_nombre_fantasia').value = searchInput ? searchInput.value.trim() : '';
            document.getElementById('qc_direccion').value = '';
            document.getElementById('qc_target_input').value = targetInputId;
            document.getElementById('qc_target_hidden').value = targetHiddenId;

            const resultsEl = document.getElementById(targetInputId === 'sender_search' ? 'sender_results' : 'receiver_results');
            if (resultsEl) resultsEl.style.display = 'none';

            quickClientModal.show();
        });

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