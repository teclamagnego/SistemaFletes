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
                                @foreach($formasPago as $fp)
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
@endsection