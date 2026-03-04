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
                        <div class="mb-3">
                            <label class="form-label">Remitente *</label>
                            <select name="sender_id" class="form-select @error('sender_id') is-invalid @enderror"
                                required>
                                <option value="">Seleccione Remitente...</option>
                                @foreach($clientes as $c)
                                <option value="{{ $c->id }}" {{ old('sender_id')==$c->id ? 'selected' : '' }}>{{
                                    $c->nombre_fantasia }} ({{ $c->documento_nro }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Destinatario *</label>
                            <select name="receiver_id" class="form-select @error('receiver_id') is-invalid @enderror"
                                required>
                                <option value="">Seleccione Destinatario...</option>
                                @foreach($clientes as $c)
                                <option value="{{ $c->id }}" {{ old('receiver_id')==$c->id ? 'selected' : '' }}>{{
                                    $c->nombre_fantasia }} ({{ $c->documento_nro }})</option>
                                @endforeach
                            </select>
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
                                        }}>{{ $a->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Agencia Destino *</label>
                                <select name="destination_agency_id" class="form-select" required>
                                    @foreach($agencies as $a)
                                    <option value="{{ $a->id }}" {{ old('destination_agency_id')==$a->id ? 'selected' :
                                        '' }}>{{ $a->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Modo Pago</label>
                                <select name="payment_mode" class="form-select">
                                    <option value="PP" {{ old('payment_mode')=='PP' ? 'selected' : '' }}>Pagado en
                                        Origen (PP)</option>
                                    <option value="CC" {{ old('payment_mode')=='CC' ? 'selected' : '' }}>Pago en Destino
                                        (CC)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
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

<script>
    document.getElementById('addItem').addEventListener('click', function  () {
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

    document.querySelector('#itemsTable').addEventListener('click', functio n (e) {
        if (e.target.closest('.remove-item')) {
            e.target.closest('tr').remove();
        }
    });
</script>
@endsection