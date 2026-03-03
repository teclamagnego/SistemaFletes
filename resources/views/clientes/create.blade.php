@extends('layouts.bootstrap')
@section('content')
<h2><i class="bi bi-person-plus"></i> Nuevo Cliente</h2>
<div class="card mt-3">
    <div class="card-body">
        <form action="{{ route('clientes.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3"><label class="form-label">Nombre *</label><input type="text" name="nombre"
                        class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}"
                        required>@error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-4 mb-3"><label class="form-label">Apellido</label><input type="text" name="apellido"
                        class="form-control" value="{{ old('apellido') }}"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Razón Social</label><input type="text"
                        name="razon_social" class="form-control" value="{{ old('razon_social') }}"></div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3"><label class="form-label">CUIT</label><input type="text" name="cuit"
                        class="form-control" value="{{ old('cuit') }}"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Teléfono</label><input type="text" name="telefono"
                        class="form-control" value="{{ old('telefono') }}"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Email</label><input type="email" name="email"
                        class="form-control" value="{{ old('email') }}"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Dirección</label><input type="text"
                        name="direccion" class="form-control" value="{{ old('direccion') }}"></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Localidad</label><input type="text"
                        name="localidad" class="form-control" value="{{ old('localidad') }}"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Provincia</label><input type="text"
                        name="provincia" class="form-control" value="{{ old('provincia') }}"></div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection