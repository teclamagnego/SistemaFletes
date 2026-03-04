@extends('layouts.bootstrap')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Editar Localidad</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('localidades.update', $localidad) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de la Localidad *</label>
                        <input type="text" name="nombre" id="nombre"
                            class="form-control @error('nombre') is-invalid @enderror"
                            value="{{ old('nombre', $localidad->nombre) }}" required autofocus>
                        @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('localidades.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-warning text-dark">Actualizar Localidad</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection