@extends('layouts.bootstrap')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold text-primary">Editar Datos del Cheque</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('cheques.update', $cheque) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="numero" class="form-label fw-bold small">Nro. Cheque</label>
                        <input type="text" name="numero" id="numero" class="form-control" value="{{ old('numero', $cheque->numero) }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha" class="form-label fw-bold small">Fecha</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" value="{{ old('fecha', $cheque->fecha) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="monto" class="form-label fw-bold small">Monto</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="monto" id="monto" class="form-control" value="{{ old('monto', $cheque->monto) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="observacion_origen" class="form-label fw-bold small">Observación Origen / Caja</label>
                        <textarea name="observacion_origen" id="observacion_origen" rows="2" class="form-control">{{ old('observacion_origen', $cheque->observacion_origen) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="observacion_destino" class="form-label fw-bold small">Observación Destino</label>
                        <textarea name="observacion_destino" id="observacion_destino" rows="2" class="form-control">{{ old('observacion_destino', $cheque->observacion_destino) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3">
                        <a href="{{ route('cheques.index') }}" class="btn btn-light px-4">Volver</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">Actualizar Cheque</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
