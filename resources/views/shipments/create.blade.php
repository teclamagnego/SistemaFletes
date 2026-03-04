@extends('layouts.bootstrap')
@section('content')
<div class="container-fluid">
    <h2><i class="bi bi-file-earmark-plus"></i> Nueva Guía de Carga</h2>
    <form action="{{ route('shipments.store') }}" method="POST" id="shipmentForm">
        @csrf
        <div class="row mt-3">
            <div class="col-md-6">
                <!-- Secciones de Remitente y Destinatario -->
                <div class="card mb-3">
                    <div class="card-header bg-light"><strong>Intervinientes</strong></div>
                    <div class="card-body">
                        <div class="mb-3 position-relative">
                            <label class="form-label">Remitente *</label>
                            <input type="hidden" name="sender_id" id="sender_id" value="{{ old('sender_id') }}"
                                required>
                            <input type="text" id="sender_search" class="form-control" autocomplete="off"
                                placeholder="Buscar por nombre fantasía..."
                                value="{{ old('sender_id') ? \App\Models\Cliente::find(old('sender_id'))?->nombre_fantasia : '' }}">
                            <div id="sender_results" class="list-group position-absolute w-100 shadow"
                                style="z-index: 1000; display: none; max-height: 250px; overflow-y: auto;"></div>
                            @error('sender_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3 position-relative">
                            <label class="form-label">Destinatario *</label>
                            <input type="hidden" name="receiver_id" id="receiver_id" value="{{ old('receiver_id') }}"
                                required>
                            <input type="text" id="receiver_search" class="form-control" autocomplete="off"
                                placeholder="Buscar por nombre fantasía..."
                                value="{{ old('receiver_id') ? \App\Models\Cliente::find(old('receiver_id'))?->nombre_fantasia : '' }}">
                            <div id="receiver_results" class="list-group position-absolute w-100 shadow"
                                style="z-index: 1000; display: none; max-height: 250px; overflow-y: auto;"></div>
                            @error('receiver_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Secciones de Agencias y Datos de Carga -->
                <div class="card mb-3">
                    <div class="card-header bg-light"><strong>Ruta y Costos</strong></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Agencia Origen *</label>
                                <select name="origin_agency_id" class="form-select" required>
                                    @foreach($agencies as $a)
                                    <option value="{{ $a->id }}" {{ old('origin_agency_id')==$a->id ? 'selected' : ''
                                        }}>{{ $a->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Agencia Destino *</label>
                                <select name="destination_agency_id" class="form-select" required>
                                    @foreach($agencies as $a)
                                    <option value="{{ $a->id }}" {{ old('destination_agency_id')==$a->id ? 'selected' :
                                        '' }}>{{ $a->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha</label>
                                <input type="date" name="fecha" class="form-control"
                                    value="{{ old('fecha', date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Forma de Pago *</label>
                                <select name="forma_pago_id" class="form-select" required>
                                    <option value="">Seleccione Pago...</option>
                                    @foreach($formas_pago as $fp)
                                    <option value="{{ $fp->id }}" {{ old('forma_pago_id')==$fp->id ? 'selected' : ''
                                        }}>{{ $fp->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label class="form-label">Dirección de Entrega</label>
                                <input type="text" name="direccion_entrega" class="form-control"
                                    value="{{ old('direccion_entrega') }}">
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label">Total Flete ($) *</label>
                                <input type="number" step="0.01" name="total_flete" class="form-control" required
                                    value="{{ old('total_flete', 0) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Items -->
        <div class="card mb-3">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <strong>Detalle de Carga (Bultos)</strong>
                <button type="button" class="btn btn-sm btn-success" id="addItem"><i class="bi bi-plus"></i> Agregar
                    Item</button>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0" id="itemsTable">
                    <thead class="bg-light">
                        <tr>
                            <th>Descripción *</th>
                            <th style="width: 100px;">Cant. *</th>
                            <th style="width: 150px;">Peso (kg)</th>
                            <th>Dimensiones</th>
                            <th style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="item-row">
                            <td><input type="text" name="items[0][descripcion]" class="form-control" required></td>
                            <td><input type="number" name="items[0][cantidad]" class="form-control" value="1" required>
                            </td>
                            <td><input type="number" step="0.01" name="items[0][peso]" class="form-control"></td>
                            <td><input type="text" name="items[0][dimensiones]" class="form-control"
                                    placeholder="Ej: 30x30x20"></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 mb-5">
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save"></i> Emitir Guía</button>
            <a href="{{ route('shipments.index') }}" class="btn btn-secondary btn-lg">Cancelar</a>
        </div>
    </form>
</div>

<!-- Modal Nuevo Cliente Rápido -->
<div class="modal fade" id="quickClientModal" tabindex="-1" aria-labelledby="quickClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="quickClientForm">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="quickClientModalLabel">Nuevo Cliente Rápido</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2">
                        <small>Los demás datos fiscales se autocompletarán con valores por defecto y podrán ser editados
                            luego.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre Fantasía *</label>
                        <input type="text" class="form-control" id="qc_nombre_fantasia" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
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

<script>
    document.getElementById('addItem').addEventListener('click', function () {
        const tableBody = document.querySelector('#itemsTable tbody');
        const rowCount = tableBody.querySelectorAll('tr').length;
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <td><input type="text" name="items[${rowCount}][descripcion]" class="form-control" required></td>
            <td><input type="number" name="items[${rowCount}][cantidad]" class="form-control" value="1" required></td>
            <td><input type="number" step="0.01" name="items[${rowCount}][peso]" class="form-control"></td>
            <td><input type="text" name="items[${rowCount}][dimensiones]" class="form-control"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-item"><i class="bi bi-x"></i></button></td>
        `;
        tableBody.appendChild(newRow);
    });

    document.querySelector('#itemsTable').addEventListener('click', function (e) {
        if (e.target.closest('.remove-item')) {
            e.target.closest('tr').remove();
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
                const url = `{{ route('clientes.search') }}?q=${encodeURIComponent(q)}`;
                fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(res => {
                        if (!res.ok) throw new Error('Network response was not ok');
                        return res.json();
                    })
                    .then(data => {
                        results.innerHTML = '';
                        if (data.length === 0) {
                            results.innerHTML = `
                                <div class="list-group-item text-muted">No se encontraron clientes</div>
                                <a href="#" class="list-group-item list-group-item-action text-primary text-center fw-bold btn-quick-client" data-target-input="${inputId}" data-target-hidden="${hiddenId}">
                                    <i class="bi bi-plus-circle"></i> Agregar Cliente
                                </a>
                            `;
                            results.style.display = 'block';
                            return;
                        }

                        data.forEach(client => {
                            const a = document.createElement('a');
                            a.href = '#';
                            a.className = 'list-group-item list-group-item-action py-2';
                            a.innerHTML = `<strong>${client.nombre_fantasia}</strong> <br><small class="text-muted">Doc: ${client.documento_nro}</small>`;
                            a.addEventListener('click', function (e) {
                                e.preventDefault();
                                input.value = client.nombre_fantasia;
                                hidden.value = client.id;
                                results.style.display = 'none';
                            });
                            results.appendChild(a);
                        });
                        results.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error fetching clients:', error);
                        results.innerHTML = '<div class="list-group-item text-danger">Error buscando clientes</div>';
                        results.style.display = 'block';
                    });
            }, 300);
        });

        document.addEventListener('click', function (e) {
            if (e.target.closest('.btn-quick-client')) {
                e.preventDefault();
                const btn = e.target.closest('.btn-quick-client');
                const tInput = btn.getAttribute('data-target-input');
                const tHidden = btn.getAttribute('data-target-hidden');
                
    etElementById('qc_target_input').value = tInput;
                document.getElementById('qc_target_hidden').value = tHidden;
                document.getElementById('qc_nombre_fantasia').value = document.getElementById(tInput).value;
                document.getElementById('qc_direccion').value = '';
                
                results.style.display = 'none';
                new bootstrap.Modal(document.getElementById('quickClientModal')).show();
                return;
            }

            if (e.target !== input && e.target !== results && !results.contains(e.target)) {
                results.style.display = 'none';
            }
        });
    }

    setupAutocomplete('sender_search', 'sender_id', 'sender_results');
    setupAutocomplete('receiver_search', 'receiver_id', 'receiver_results');

    document.getElementById('quickClientForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('qc_submit_btn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

        const payload = {
            nombre_fantasia: document.getElementById('qc_nombre_fantasia').value,
            direccion: document.getElementById('qc_direccion').value,
            _token: '{{ csrf_token() }}'
        };

        fetch('{{ route("clientes.storeQuick") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(res => {
            if (!res.ok) throw new Error('Error guardando cliente');
            return res.json();
        })
        .then(client => {
            const tInputId = document.getElementById('qc_target_input').value;
            const tHiddenId = document.getElementById('qc_target_hidden').value;
            
            document.getElementById(tInputId).value = client.nombre_fantasia;
            document.getElementById(tHiddenId).value = client.id;
            
            bootstrap.Modal.getInstance(document.getElementById('quickClientModal')).hide();
            btn.disabled = false;
            btn.innerHTML = 'Guardar Cliente';
            
            document.getElementById(tInputId).classList.remove('is-invalid');
        })
        .catch(error => {
            console.error(error);
            alert('Hubo un error al crear el cliente. Intente nuevamente.');
            btn.disabled = false;
            btn.innerHTML = 'Guardar Cliente';
        });
    });
</script>
@endsection