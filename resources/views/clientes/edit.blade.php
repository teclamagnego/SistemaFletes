@extends('layouts.bootstrap')
@section('content')
<h2><i class="bi bi-pencil-square"></i> Editar Cliente: {{ $cliente->nombre }}</h2>
<div class="card mt-3">
    <div class="card-body">
        <form action="{{ route('clientes.update', $cliente) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-4 mb-3"><label class="form-label">Nombre *</label><input type="text" name="nombre"
                        class="form-control @error('nombre') is-invalid @enderror"
                        value="{{ old('nombre', $cliente->nombre) }}" required>@error('nombre')<div
                        class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-4 mb-3"><label class="form-label">Apellido</label><input type="text" name="apellido"
                        class="form-control" value="{{ old('apellido', $cliente->apellido) }}"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Razón Social</label><input type="text"
                        name="razon_social" class="form-control"
                        value="{{ old('razon_social', $cliente->razon_social) }}"></div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3"><label class="form-label">CUIT</label><input type="text" name="cuit"
                        class="form-control" value="{{ old('cuit', $cliente->cuit) }}"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Teléfono</label><input type="text" name="telefono"
                        class="form-control" value="{{ old('telefono', $cliente->telefono) }}"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Email</label><input type="email" name="email"
                        class="form-control" value="{{ old('email', $cliente->email) }}"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Dirección</label><input type="text"
                        name="direccion" class="form-control" value="{{ old('direccion', $cliente->direccion) }}"></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Localidad</label><input type="text"
                        name="localidad" class="form-control" value="{{ old('localidad', $cliente->localidad) }}"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Provincia</label><input type="text"
                        name="provincia" class="form-control" value="{{ old('provincia', $cliente->provincia) }}"></div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Actualizar</button>
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection