@php
    $isEdit = isset($shipment) && $shipment->exists;
@endphp

<form action="{{ $action }}" method="POST" id="shipmentForm">
    @csrf
    @if(isset($method) && $method !== 'POST')
        @method($method)
    @endif

    <div class="row mb-4">
        <div class="col-md-6 mb-3 mb-md-0">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label for="sender_search" class="form-label fw-semibold mb-0">Remitente</label>
                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-1 btn-quick-client" data-target-input="sender_search" data-target-hidden="sender_id" title="Agregar nuevo cliente">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </div>
            <div class="position-relative">
                <input type="text" id="sender_search" class="form-control"
                    placeholder="Buscar cliente..." autocomplete="off"
                    value="{{ $isEdit ? ($shipment->sender->nombre_fantasia ?? '') : '' }}">
                <input type="hidden" name="sender_id" id="sender_id" value="{{ $isEdit ? $shipment->sender_id : '' }}">
                <div id="sender_results" class="list-group position-absolute w-100 shadow-sm"
                    style="z-index: 1000; display: none;"></div>
            </div>
            <div class="mt-2">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="payer" id="payer_sender"
                        value="sender" {{ ($isEdit && $shipment->payer === 'sender') ? 'checked' : '' }}>
                    <label class="form-check-label text-primary fw-bold" for="payer_sender">Paga
                        Remitente</label>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label for="receiver_search" class="form-label fw-semibold mb-0">Destinatario</label>
                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-1 btn-quick-client" data-target-input="receiver_search" data-target-hidden="receiver_id" title="Agregar nuevo cliente">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </div>
            <div class="position-relative">
                <input type="text" id="receiver_search" class="form-control"
                    placeholder="Buscar cliente..." autocomplete="off"
                    value="{{ $isEdit ? ($shipment->receiver->nombre_fantasia ?? '') : '' }}">
                <input type="hidden" name="receiver_id" id="receiver_id" value="{{ $isEdit ? $shipment->receiver_id : '' }}">
                <div id="receiver_results" class="list-group position-absolute w-100 shadow-sm"
                    style="z-index: 1000; display: none;"></div>
            </div>
            <div class="mt-2">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="payer" id="payer_receiver"
                        value="receiver" {{ ($isEdit && $shipment->payer === 'receiver') ? 'checked' : '' }}>
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
                <option value="{{ $agency->id }}" {{ ($isEdit && $shipment->origin_agency_id == $agency->id) ? 'selected' : '' }}>{{ $agency->nombre }}</option>
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
                <option value="{{ $agency->id }}" {{ ($isEdit && $shipment->destination_agency_id == $agency->id) ? 'selected' : '' }}>{{ $agency->nombre }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row mb-4">
        @if($carriers->count() === 1)
        <input type="hidden" name="carrier_id" value="{{ $isEdit ? $shipment->carrier_id : $carriers->first()->id }}">
        @else
        <div class="col-md-4 mb-3">
            <label for="carrier_id" class="form-label fw-semibold">Transportista</label>
            <select name="carrier_id" id="carrier_id" class="form-select" required>
                <option value="">Seleccionar...</option>
                @foreach($carriers as $carrier)
                <option value="{{ $carrier->id }}" {{ ($isEdit && $shipment->carrier_id == $carrier->id) ? 'selected' : '' }}>{{ $carrier->nombre }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="{{ $carriers->count() === 1 ? 'col-md-6' : 'col-md-4' }} mb-3">
            <label for="forma_pago_id" class="form-label fw-semibold">Forma de Pago</label>
            <select name="forma_pago_id" id="forma_pago_id" class="form-select" required>
                <option value="">Seleccionar...</option>
                @foreach($formasPago as $fp)
                <option value="{{ $fp->id }}" {{ ($isEdit && $shipment->forma_pago_id == $fp->id) ? 'selected' : '' }}>{{ $fp->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="{{ $carriers->count() === 1 ? 'col-md-6' : 'col-md-4' }} mb-3">
            <label for="direccion_entrega" class="form-label fw-semibold">Lugar de Entrega</label>
            <input type="text" name="direccion_entrega" id="direccion_entrega" class="form-control"
                placeholder="Dirección de entrega" required value="{{ $isEdit ? $shipment->direccion_entrega : '' }}">
        </div>
    </div>

    <div class="card mb-4 border-light bg-light bg-opacity-10">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
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
                        @php
                            $items = $isEdit ? $shipment->items : [null];
                        @endphp
                        @foreach($items as $index => $item)
                        <tr class="item-row">
                            <td>
                                <div class="position-relative">
                                    <input type="text"
                                        class="form-control form-control-sm item-codigo"
                                        placeholder="Código" autocomplete="off" value="{{ $isEdit ? ($item->articulo->codigo ?? '') : '' }}">
                                    <div class="list-group position-absolute w-100 shadow-sm item-codigo-results"
                                        style="z-index: 1000; display: none;"></div>
                                </div>
                            </td>
                            <td>
                                <div class="position-relative">
                                    <input type="text" name="items[{{ $index }}][descripcion]"
                                        class="form-control form-control-sm item-descripcion"
                                        placeholder="Descripción del producto"
                                        autocomplete="off" value="{{ $isEdit ? $item->descripcion : '' }}">
                                    <input type="hidden" name="items[{{ $index }}][articulo_id]"
                                        class="item-articulo-id" value="{{ $isEdit ? $item->articulo_id : '' }}">
                                    <div class="list-group position-absolute w-100 shadow-sm item-descripcion-results"
                                        style="z-index: 1000; display: none;"></div>
                                </div>
                            </td>
                            <td><input type="number" name="items[{{ $index }}][cantidad]"
                                    class="form-control form-control-sm item-cantidad" value="{{ $isEdit ? $item->cantidad : 1 }}"
                                    required min="1"></td>
                            <td><input type="number" step="0.01" name="items[{{ $index }}][precio_unitario]"
                                    class="form-control form-control-sm item-precio" value="{{ $isEdit ? $item->precio_unitario : '0.00' }}"
                                    required min="0"></td>
                            <td><input type="number" step="0.01" name="items[{{ $index }}][bonificacion]"
                                    class="form-control form-control-sm item-bonif" value="{{ $isEdit ? $item->bonificacion : '100.00' }}"
                                    min="0"></td>
                            <td>
                                <span class="item-total-text fw-bold">$ {{ $isEdit ? number_format($item->total, 2, ',', '.') : '0,00' }}</span>
                                <input type="hidden" name="items[{{ $index }}][total]" class="item-total" value="{{ $isEdit ? $item->total : '0.00' }}">
                            </td>
                            <td class="text-center">
                                @if($index > 0 || $isEdit)
                                <button type="button" class="btn btn-sm btn-danger remove-item"><i class="bi bi-x"></i></button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="5" class="text-end fw-bold">TOTAL ENVÍO:</td>
                            <td class="fw-bold"><span id="total_envio_display">$ {{ $isEdit ? number_format($shipment->total_flete, 2, ',', '.') : '0,00' }}</span></td>
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
            <textarea name="notas" id="notas" rows="3" class="form-control">{{ $isEdit ? $shipment->notas : '' }}</textarea>
        </div>
        <div class="mb-3">
            <label for="ref_remito" class="form-label fw-bold">Referencia Remito</label>
            <input type="text" name="ref_remito" id="ref_remito" class="form-control" placeholder="Ej: R-001234" value="{{ $isEdit ? $shipment->ref_remito : '' }}">
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 border-top pt-4">
        <a href="{{ route('shipments.index') }}" id="btnCancelarGuia" class="btn btn-light px-4">Cancelar</a>
        @if($isEdit)
        <button type="button" id="btnActualizarGuia" class="btn btn-warning px-4 text-white fw-bold">Actualizar Guía</button>
        @else
        <button type="button" id="btnCrearSinImprimir" class="btn btn-outline-primary px-4">
            <i class="bi bi-save me-1"></i>Crear sin Imprimir
        </button>
        <button type="button" id="btnCrearGuia" class="btn btn-primary px-4">
            <i class="bi bi-printer me-1"></i>Crear e Imprimir Guía
        </button>
        @endif
    </div>
</form>

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
                    <div class="mb-3">
                        <label for="qc_localidad_search" class="form-label">Localidad</label>
                        <div class="position-relative">
                            <input type="text" id="qc_localidad_search" class="form-control" placeholder="Buscar localidad..." autocomplete="off">
                            <input type="hidden" id="qc_localidad_id">
                            <div id="qc_localidad_results" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1050; display: none;"></div>
                        </div>
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
    let createdShipmentPrintBase64Url = null;
    let createdTrackingNumber = null;
    let printStatusModalObj = null;

    function updatePrintModal(phase, errorDetail) {
        const header   = document.getElementById('printModalHeader');
        const title    = document.getElementById('printModalTitle');
        const text     = document.getElementById('printStatusText');
        const sub      = document.getElementById('printStatusSub');
        const footer   = document.getElementById('printModalFooter');
        const btnRetry = document.getElementById('btnRetryPrint');

        if (!header) return;

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
            sub.innerHTML = errorDetail ? '<span class="text-danger"><strong>Error:</strong> ' + errorDetail + '</span>' : 'QZ-Tray no disponible.';
            footer.style.removeProperty('display');
            btnRetry.style.display = 'inline-flex';
        } else if (phase === 'error') {
            header.classList.add('bg-danger', 'text-white');
            title.innerHTML = '<i class="bi bi-x-circle me-2"></i>Error';
            text.textContent = 'Ocurrió un error al procesar la solicitud.';
            sub.textContent = errorDetail || '';
            footer.style.removeProperty('display');
        }
    }

    async function printWithQzTray(pdfBase64) {
        updatePrintModal('connecting');
        qz.security.setCertificatePromise(resolve => fetch('{{ route("qz.certificate") }}').then(r => r.text()).then(resolve));
        qz.security.setSignatureAlgorithm('SHA512');
        qz.security.setSignaturePromise(toSign => resolve => fetch('{{ route("qz.sign") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ toSign: toSign }) }).then(r => r.text()).then(resolve));

        try {
            if (!qz.websocket.isActive()) await qz.websocket.connect({ retries: 2, delay: 1 });
            updatePrintModal('printing');
            const printer = await qz.printers.getDefault();
            const config = qz.configs.create(printer);
            await qz.print(config, [{ type: 'pixel', format: 'pdf', flavor: 'base64', data: pdfBase64 }]);
            updatePrintModal('success');
            setTimeout(() => window.location.href = "{{ route('shipments.index') }}", 2000);
        } catch (err) {
            updatePrintModal('qz_error', err.message);
        }
    }

    function calculateRowTotal(row) {
        const qty = parseFloat(row.querySelector('.item-cantidad').value) || 0;
        const price = parseFloat(row.querySelector('.item-precio').value) || 0;
        const bonif = parseFloat(row.querySelector('.item-bonif').value) || 0;
        let total = (qty * price * bonif) / 100;
        row.querySelector('.item-total').value = total.toFixed(2);
        row.querySelector('.item-total-text').innerText = '$ ' + total.toLocaleString('es-AR', { minimumFractionDigits: 2 });
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.item-total').forEach(input => grandTotal += parseFloat(input.value) || 0);
        document.getElementById('total_envio_display').innerText = '$ ' + grandTotal.toLocaleString('es-AR', { minimumFractionDigits: 2 });
    }

    function addNewRow() {
        const tableBody = document.querySelector('#itemsTable tbody');
        const rowCount = tableBody.querySelectorAll('tr').length;
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <td><div class="position-relative"><input type="text" class="form-control form-control-sm item-codigo" placeholder="Código" autocomplete="off"><div class="list-group position-absolute w-100 shadow-sm item-codigo-results" style="z-index: 1000; display: none;"></div></div></td>
            <td><div class="position-relative"><input type="text" name="items[${rowCount}][descripcion]" class="form-control form-control-sm item-descripcion" placeholder="Descripción" autocomplete="off"><input type="hidden" name="items[${rowCount}][articulo_id]" class="item-articulo-id"><div class="list-group position-absolute w-100 shadow-sm item-descripcion-results" style="z-index: 1000; display: none;"></div></div></td>
            <td><input type="number" name="items[${rowCount}][cantidad]" class="form-control form-control-sm item-cantidad" value="1" required min="1"></td>
            <td><input type="number" step="0.01" name="items[${rowCount}][precio_unitario]" class="form-control form-control-sm item-precio" value="0.00" required min="0"></td>
            <td><input type="number" step="0.01" name="items[${rowCount}][bonificacion]" class="form-control form-control-sm item-bonif" value="100.00" min="0"></td>
            <td><span class="item-total-text fw-bold">$ 0,00</span><input type="hidden" name="items[${rowCount}][total]" class="item-total" value="0.00"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-item"><i class="bi bi-x"></i></button></td>
        `;
        tableBody.appendChild(newRow);
        setupItemAutocomplete(newRow);
        setupRowListeners(newRow);
        return newRow;
    }

    function setupAutocomplete(inputId, hiddenId, resultsId) {
        const input = document.getElementById(inputId);
        const results = document.getElementById(resultsId);
        if (!input || !results) return;

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
                    if (x[currentFocus]) {
                        x[currentFocus].click();
                        e.preventDefault();
                    }
                }
            }
        });

        input.addEventListener('input', function() {
            const q = this.value.trim();
            currentFocus = -1;
            if (q.length < 2) { results.style.display = 'none'; return; }
            fetch(`{{ route('clientes.search') }}?q=${encodeURIComponent(q)}&activo=1`, { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => {
                    results.innerHTML = '';
                    if (data.length === 0) {
                        results.innerHTML = `<div class="list-group-item text-muted">No se encontraron clientes</div><a href="#" class="list-group-item list-group-item-action text-primary btn-quick-client" data-target-input="${inputId}" data-target-hidden="${hiddenId}">+ Agregar Cliente</a>`;
                    } else {
                        data.forEach(client => {
                            const a = document.createElement('a');
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
                            a.onclick = (e) => {
                                if (e.target.closest('.delete-client-ajax')) return;
                                e.preventDefault();
                                input.value = client.nombre_fantasia;
                                document.getElementById(hiddenId).value = client.id;
                                results.style.display = 'none';
                                if (inputId === 'sender_search') {
                                    if (client.agenciaorigen_id) document.getElementById('origin_agency_id').value = client.agenciaorigen_id;
                                    document.getElementById('receiver_search').focus();
                                } else {
                                    let deliveryAddr = client.direccion || '';
                                    if (client.localidad && client.localidad.nombre) {
                                        deliveryAddr = deliveryAddr ? `${deliveryAddr} (${client.localidad.nombre})` : `(${client.localidad.nombre})`;
                                    }
                                    document.getElementById('direccion_entrega').value = deliveryAddr;
                                    if (client.agenciadestino_id) document.getElementById('destination_agency_id').value = client.agenciadestino_id;
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
                    }
                    results.style.display = 'block';
                });
        });

        document.addEventListener('click', (e) => { if (e.target !== input && !results.contains(e.target)) results.style.display = 'none'; });
    }

    function setupItemAutocomplete(row) {
        const input = row.querySelector('.item-descripcion');
        const codeInput = row.querySelector('.item-codigo');
        const results = row.querySelector('.item-descripcion-results');
        const codeResults = row.querySelector('.item-codigo-results');

        const handleSearch = (inputField, resultsBox, field) => {
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

            inputField.addEventListener('keydown', function (e) {
                let x = resultsBox.getElementsByClassName("list-group-item-action");
                if (e.key === "ArrowDown") {
                    currentFocus++;
                    addActive(x);
                } else if (e.key === "ArrowUp") {
                    currentFocus--;
                    addActive(x);
                } else if (e.key === "Enter") {
                    if (currentFocus > -1) {
                        if (x[currentFocus]) {
                            x[currentFocus].click();
                            e.preventDefault();
                        }
                    }
                }
            });

            inputField.addEventListener('input', () => {
                clearTimeout(timeout);
                const q = inputField.value.trim();
                currentFocus = -1;
                
                if (q.length < 1) { resultsBox.style.display = 'none'; return; }
                
                timeout = setTimeout(() => {
                    fetch(`{{ route('articulos.search') }}?q=${encodeURIComponent(q)}`, { headers: { 'Accept': 'application/json' } })
                        .then(r => r.json())
                        .then(data => {
                            resultsBox.innerHTML = '';
                            data.forEach(art => {
                                const a = document.createElement('a');
                                a.className = 'list-group-item list-group-item-action py-1';
                                a.innerHTML = `<strong>${art.nombre}</strong> <small>(${art.codigo})</small>`;
                                a.onclick = (e) => {
                                    e.preventDefault();
                                    row.querySelector('.item-codigo').value = art.codigo;
                                    row.querySelector('.item-descripcion').value = art.nombre;
                                    row.querySelector('.item-articulo-id').value = art.id;
                                    row.querySelector('.item-precio').value = art.precio;
                                    resultsBox.style.display = 'none';
                                    calculateRowTotal(row);
                                    row.querySelector('.item-cantidad').focus();
                                };
                                resultsBox.appendChild(a);
                            });
                            resultsBox.style.display = data.length ? 'block' : 'none';
                        });
                }, 300);
            });
        };

        handleSearch(input, results, 'nombre');
        handleSearch(codeInput, codeResults, 'codigo');

        document.addEventListener('click', (e) => {
            if (e.target !== input && !results.contains(e.target)) results.style.display = 'none';
            if (e.target !== codeInput && !codeResults.contains(e.target)) codeResults.style.display = 'none';
        });
    }

    function setupRowListeners(row) {
        row.querySelectorAll('input').forEach(i => {
            i.addEventListener('input', () => calculateRowTotal(row));
            i.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    // Si el autocompletado está abierto y tiene algo seleccionado, dejamos que el otro handler maneje el Enter
                    const resultsBox = i.classList.contains('item-codigo') ? 
                        row.querySelector('.item-codigo-results') : 
                        (i.classList.contains('item-descripcion') ? row.querySelector('.item-descripcion-results') : null);
                    
                    if (resultsBox && resultsBox.style.display !== 'none' && resultsBox.querySelector('.active')) {
                        return;
                    }

                    e.preventDefault();
                    if (i.classList.contains('item-codigo')) {
                        row.querySelector('.item-descripcion').focus();
                    } else if (i.classList.contains('item-descripcion')) {
                        row.querySelector('.item-cantidad').focus();
                    } else if (i.classList.contains('item-cantidad')) {
                        row.querySelector('.item-precio').focus();
                    } else if (i.classList.contains('item-precio') || i.classList.contains('item-bonif')) {
                        const nr = addNewRow();
                        nr.querySelector('.item-codigo').focus();
                    }
                }
            });
        });
    }

    function setupLocalidadAutocomplete() {
        const input = document.getElementById('qc_localidad_search');
        const results = document.getElementById('qc_localidad_results');
        if (!input) return;

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
                    if (x[currentFocus]) {
                        x[currentFocus].click();
                        e.preventDefault();
                    }
                }
            }
        });

        input.addEventListener('input', () => {
            const q = input.value;
            currentFocus = -1;
            if (q.length < 2) { results.style.display = 'none'; return; }
            fetch(`{{ route('localidades.search') }}?q=${encodeURIComponent(q)}`).then(r => r.json()).then(data => {
                results.innerHTML = '';
                data.forEach(l => {
                    const a = document.createElement('a');
                    a.href = '#';
                    a.className = 'list-group-item list-group-item-action py-2 d-flex justify-content-between align-items-center';
                    a.innerHTML = `
                        <div class="flex-grow-1">
                            <strong>${l.nombre}</strong>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger border-0 delete-localidad-ajax" data-id="${l.id}" title="Desactivar localidad">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    `;
                    a.onclick = (e) => {
                        if (e.target.closest('.delete-localidad-ajax')) return;
                        e.preventDefault();
                        input.value = l.nombre;
                        document.getElementById('qc_localidad_id').value = l.id;
                        results.style.display = 'none';
                    };

                    const btnDel = a.querySelector('.delete-localidad-ajax');
                    btnDel.onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        function performDelete() {
                            const originalContent = btnDel.innerHTML;
                            btnDel.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                            btnDel.disabled = true;

                            fetch(`/localidades/${l.id}/ajax`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(resData => {
                                if (resData.success) {
                                    a.remove();
                                    if (results.querySelectorAll('.list-group-item-action').length === 0) {
                                        results.style.display = 'none';
                                    }
                                } else {
                                    alert(resData.message || 'No se puede eliminar la localidad.');
                                    btnDel.innerHTML = originalContent;
                                    btnDel.disabled = false;
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                alert('Error al intentar eliminar la localidad.');
                                btnDel.innerHTML = originalContent;
                                btnDel.disabled = false;
                            });
                        }

                        if (confirm('¿Está seguro de eliminar esta localidad?')) {
                            performDelete();
                        }
                    };
                    results.appendChild(a);
                });
                results.style.display = 'block';
            });
        });
    }

    async function saveShipment(shouldPrint = true) {
        const form = document.getElementById('shipmentForm');

        // 1. Limpiar filas vacías pero dejar al menos una
        let rowsChanged = false;
        document.querySelectorAll('.item-row').forEach(row => {
            const artId = row.querySelector('.item-articulo-id').value;
            const desc = row.querySelector('.item-descripcion').value.trim();
            if (!artId && !desc && document.querySelectorAll('.item-row').length > 1) {
                row.remove();
                rowsChanged = true;
            }
        });
        if (rowsChanged) calculateGrandTotal();

        // 2. Validaciones Personalizadas (para campos que reportValidity no cubre bien o son mandatorios)
        
        // Remitente
        if (!document.getElementById('sender_id').value) {
            alert('Debe seleccionar un Remitente válido de la lista.');
            document.getElementById('sender_search').focus();
            return;
        }

        // Destinatario
        if (!document.getElementById('receiver_id').value) {
            alert('Debe seleccionar un Destinatario válido de la lista.');
            document.getElementById('receiver_search').focus();
            return;
        }

        // Quién paga (Payer)
        const payerSelected = document.querySelector('input[name="payer"]:checked');
        if (!payerSelected) {
            alert('Debe seleccionar quién paga el envío (Remitente o Destinatario).');
            return;
        }

        // 3. Validar el resto de campos (agencias, transportista, forma pago, dirección entrega, campos de items)
        if (!form.reportValidity()) return;
        
        // 4. Validar que al menos haya un ítem con descripción
        let hasValidItem = false;
        document.querySelectorAll('.item-row').forEach(row => {
            if (row.querySelector('.item-descripcion').value.trim()) hasValidItem = true;
        });
        
        if (!hasValidItem) {
            alert('Debe completar al menos un artículo.');
            document.querySelectorAll('.item-row')[0].querySelector('.item-codigo').focus();
            return;
        }

        const isEdit = {{ $isEdit ? 'true' : 'false' }};
        if (isEdit) { form.submit(); return; }

        // Logic for Create (AJAX + Printer)
        const btn = document.getElementById('btnCrearGuia');
        if (btn) btn.disabled = true;
        
        updatePrintModal('saving');
        printStatusModalObj = printStatusModalObj || new bootstrap.Modal(document.getElementById('printStatusModal'));
        printStatusModalObj.show();

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: new FormData(form)
            });
            const data = await res.json();
            if (!data.success) { updatePrintModal('error', data.message); return; }

            createdTrackingNumber = data.tracking_number;
            if (shouldPrint) {
                const pdfRes = await fetch(data.print_base64_url).then(r => r.json());
                await printWithQzTray(pdfRes.pdf_base64);
            } else {
                updatePrintModal('success');
                setTimeout(() => window.location.href = "{{ route('shipments.index') }}", 500);
            }
        } catch (e) { updatePrintModal('error', e.toString()); }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('shipmentForm');
        if (!form) return;

        setupAutocomplete('sender_search', 'sender_id', 'sender_results');
        setupAutocomplete('receiver_search', 'receiver_id', 'receiver_results');
        setupLocalidadAutocomplete();
        document.querySelectorAll('.item-row').forEach(r => { setupItemAutocomplete(r); setupRowListeners(r); });

        document.getElementById('addItem').onclick = () => addNewRow();
        
        form.addEventListener('keydown', (e) => { if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') e.preventDefault(); });

        form.addEventListener('focus', function (e) {
            if (e.target.tagName === 'INPUT' && (e.target.type === 'text' || e.target.type === 'number')) {
                e.target.select();
            }
        }, true);

        const btnCrear = document.getElementById('btnCrearGuia');
        if (btnCrear) btnCrear.onclick = () => saveShipment(true);
        const btnNoPrint = document.getElementById('btnCrearSinImprimir');
        if (btnNoPrint) btnNoPrint.onclick = () => saveShipment(false);
        const btnUpdate = document.getElementById('btnActualizarGuia');
        if (btnUpdate) btnUpdate.onclick = () => saveShipment();

        // Modal event delegation
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-quick-client');
            if (btn) {
                const tid = btn.getAttribute('data-target-input');
                document.getElementById('qc_target_input').value = tid;
                document.getElementById('qc_target_hidden').value = btn.getAttribute('data-target-hidden');
                document.getElementById('qc_nombre_fantasia').value = document.getElementById(tid).value;
                new bootstrap.Modal(document.getElementById('quickClientModal')).show();
            }
            if (e.target.closest('.remove-item')) {
                if (document.querySelectorAll('.item-row').length > 1) {
                    e.target.closest('tr').remove();
                    calculateGrandTotal();
                }
            }
        });

        const qcf = document.getElementById('quickClientForm');
        if (qcf) {
            qcf.onsubmit = (e) => {
                e.preventDefault();
                const btn = document.getElementById('qc_submit_btn');
                btn.disabled = true;
                fetch('{{ route("clientes.storeQuick") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ nombre_fantasia: document.getElementById('qc_nombre_fantasia').value, direccion: document.getElementById('qc_direccion').value, localidad_id: document.getElementById('qc_localidad_id').value })
                }).then(r => r.json()).then(c => {
                    const tid = document.getElementById('qc_target_input').value;
                    document.getElementById(tid).value = c.nombre_fantasia;
                    document.getElementById(document.getElementById('qc_target_hidden').value).value = c.id;

                    if (tid === 'receiver_search') {
                        let deliveryAddr = c.direccion || '';
                        if (c.localidad && c.localidad.nombre) {
                            deliveryAddr = deliveryAddr ? `${deliveryAddr} (${c.localidad.nombre})` : `(${c.localidad.nombre})`;
                        }
                        document.getElementById('direccion_entrega').value = deliveryAddr;
                    }

                    bootstrap.Modal.getInstance(document.getElementById('quickClientModal')).hide();
                    btn.disabled = false;
                });
            };
        }
    });
</script>
@endpush
