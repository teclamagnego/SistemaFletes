@extends('layouts.bootstrap')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Nuevo Recibo de Cliente</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('cliente_recibos.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cliente_id" class="form-label fw-semibold">Cliente</label>
                            <select name="cliente_id" id="cliente_id"
                                class="form-select @error('cliente_id') is-invalid @enderror" required
                                onchange="window.location.href = '{{ route('cliente_recibos.create') }}?cliente_id=' + this.value">
                                <option value="">Seleccionar Cliente...</option>
                                @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ (old('cliente_id')==$cliente->id ||
                                    $selected_cliente_id == $cliente->id) ? 'selected' : '' }}>
                                    {{ $cliente->nombre_fantasia }}
                                </option>
                                @endforeach
                            </select>
                            @if($selected_cliente_id)
                            <div class="mt-2 text-primary fw-bold">
                                Saldo Pendiente: $ {{ number_format($saldo, 2) }}
                            </div>
                            @endif
                            @error('cliente_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha" class="form-label fw-semibold">Fecha</label>
                            <input type="date" name="fecha" id="fecha"
                                class="form-control @error('fecha') is-invalid @enderror"
                                value="{{ old('fecha', date('Y-m-d')) }}" required>
                            @error('fecha')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="monto" class="form-label fw-semibold">Monto</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="monto" id="monto"
                                    class="form-control @error('monto') is-invalid @enderror" value="{{ old('monto') }}"
                                    required>
                            </div>
                            @error('monto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="forma_pago_id" class="form-label fw-semibold">Forma de Pago</label>
                            <select name="forma_pago_id" id="forma_pago_id"
                                class="form-select @error('forma_pago_id') is-invalid @enderror" required>
                                <option value="">Seleccionar...</option>
                                @php $chequesId = 0; @endphp
                                @foreach($formasPago as $fp)
                                    @if($fp->nombre == 'Cheques') @php $chequesId = $fp->id; @endphp @endif
                                    <option value="{{ $fp->id }}" {{ old('forma_pago_id')==$fp->id ? 'selected' : '' }}>
                                        {{ $fp->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('forma_pago_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Sección de Cheques --}}
                    <div id="chequesSection" style="display: none;" class="border p-3 rounded mb-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">Detalle de Cheques</h6>
                            <button type="button" class="btn btn-sm btn-success" onclick="addChequeRow()">+ Agregar Cheque</button>
                        </div>
                        <table class="table table-sm table-bordered bg-white">
                            <thead>
                                <tr class="table-secondary">
                                    <th>Nro Cheque</th>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Obs/Caja</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="chequesBody">
                                {{-- Filas dinámicas --}}
                            </tbody>
                        </table>
                    </div>

                    <div class="mb-3">
                        <label for="nro_recibo" class="form-label fw-semibold">Nro. de Recibo (Opcional)</label>
                        <input type="text" name="nro_recibo" id="nro_recibo"
                            class="form-control @error('nro_recibo') is-invalid @enderror"
                            value="{{ old('nro_recibo') }}">
                        @error('nro_recibo')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="observaciones" class="form-label fw-semibold">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" rows="3"
                            class="form-control @error('observaciones') is-invalid @enderror">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3">
                        <a href="{{ $selected_cliente_id ? route('clientes.history', $selected_cliente_id) : route('cliente_recibos.index') }}"
                            class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">Guardar Recibo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const chequesId = "{{ $chequesId }}";
    const formaPagoSelect = document.getElementById('forma_pago_id');
    const chequesSection = document.getElementById('chequesSection');
    const montoInput = document.getElementById('monto');
    const chequesBody = document.getElementById('chequesBody');

    formaPagoSelect.addEventListener('change', function() {
        if (this.value == chequesId) {
            chequesSection.style.display = 'block';
            montoInput.readOnly = true;
            if (chequesBody.rows.length === 0) addChequeRow();
            calculateTotalCheques();
        } else {
            chequesSection.style.display = 'none';
            montoInput.readOnly = false;
        }
    });

    function addChequeRow() {
        const index = chequesBody.rows.length;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" name="cheques[${index}][numero]" class="form-control form-control-sm" required></td>
            <td><input type="date" name="cheques[${index}][fecha]" class="form-control form-control-sm" required></td>
            <td><input type="number" step="0.01" name="cheques[${index}][monto]" class="form-control form-control-sm cheque-monto" onchange="calculateTotalCheques()" required></td>
            <td><input type="text" name="cheques[${index}][observacion_origen]" class="form-control form-control-sm"></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeChequeRow(this)"><i class="bi bi-trash"></i></button></td>
        `;
        chequesBody.appendChild(row);
    }

    function removeChequeRow(btn) {
        btn.closest('tr').remove();
        calculateTotalCheques();
    }

    function calculateTotalCheques() {
        if (formaPagoSelect.value != chequesId) return;
        
        let total = 0;
        document.querySelectorAll('.cheque-monto').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        montoInput.value = total.toFixed(2);
    }

    // Inicializar si ya hay algo (old value)
    window.onload = function() {
        if (formaPagoSelect.value == chequesId) {
            chequesSection.style.display = 'block';
            montoInput.readOnly = true;
        }
    }
</script>
@endsection