@extends('layouts.bootstrap')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Editar Forma de Pago</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('formas_pago.update', $formaPago) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-semibold">Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $formaPago->nombre) }}" required>
                        @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-end gap-2 pt-3">
                        <a href="{{ route('formas_pago.index') }}" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection