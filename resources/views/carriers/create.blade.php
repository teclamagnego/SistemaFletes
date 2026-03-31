@extends('layouts.bootstrap')
@section('content')
<h2><i class="bi bi-plus-lg"></i> Nuevo Transportista</h2>
<div class="card mt-3">
    <div class="card-body">
        <form action="{{ route('carriers.store') }}" method="POST">@csrf
            <div class="row">
                <div class="col-md-4 mb-3"><label class="form-label">Nombre *</label><input type="text" name="nombre"
                        class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}"
                        required>@error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-4 mb-3"><label class="form-label">Apellido *</label><input type="text"
                        name="apellido" class="form-control @error('apellido') is-invalid @enderror"
                        value="{{ old('apellido') }}" required>@error('apellido')<div class="invalid-feedback">{{
                        $message }}</div>@enderror</div>
                <div class="col-md-4 mb-3"><label class="form-label">Documento</label><input type="text"
                        name="documento" class="form-control" value="{{ old('documento') }}"></div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3"><label class="form-label">Teléfono</label><input type="text" name="telefono"
                        class="form-control" value="{{ old('telefono') }}"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Email</label><input type="email" name="email"
                        class="form-control" value="{{ old('email') }}"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Estado</label><select name="activo"
                        class="form-select">
                        <option value="1" {{ old('activo', 1)==1 ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('activo')==='0' ? 'selected' : '' }}>Inactivo</option>
                    </select></div>
            </div>
            <div class="row">
                <div class="col-md-8 mb-3"><label class="form-label">Vehículo (Marca/Modelo)</label><input type="text"
                        name="vehiculo" class="form-control" value="{{ old('vehiculo') }}"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Patente</label><input type="text" name="patente"
                        class="form-control" value="{{ old('patente') }}"></div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
            <a href="{{ route('carriers.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection