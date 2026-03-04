@extends('layouts.bootstrap')
@section('content')
<h2><i class="bi bi-plus-lg"></i> Nueva Agencia</h2>
<div class="card mt-3">
    <div class="card-body">
        <form action="{{ route('agencies.store') }}" method="POST">@csrf
            <div class="row">
                <div class="col-md-3 mb-3"><label class="form-label">Código *</label><input type="text" name="codigo"
                        class="form-control @error('codigo') is-invalid @enderror" value="{{ old('codigo') }}" required
                        maxlength="10">@error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-5 mb-3"><label class="form-label">Nombre *</label><input type="text" name="nombre"
                        class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}"
                        required>@error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-2 mb-3"><label class="form-label">Comisión % *</label><input type="number"
                        step="0.01" name="comision_porcentaje" class="form-control"
                        value="{{ old('comision_porcentaje', 10) }}" required min="0" max="100"></div>
                <div class="col-md-2 mb-3"><label class="form-label">Estado</label><select name="activa"
                        class="form-select">
                        <option value="1" {{ old('activa', 1)==1 ? 'selected' : '' }}>Activa</option>
                        <option value="0" {{ old('activa')==='0' ? 'selected' : '' }}>Inactiva</option>
                    </select></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Dirección</label><input type="text"
                        name="direccion" class="form-control" value="{{ old('direccion') }}"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Localidad</label><input type="text"
                        name="localidad" class="form-control" value="{{ old('localidad') }}"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Provincia</label><input type="text"
                        name="provincia" class="form-control" value="{{ old('provincia') }}"></div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3"><label class="form-label">Teléfono</label><input type="text" name="telefono"
                        class="form-control" value="{{ old('telefono') }}"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Email</label><input type="email" name="email"
                        class="form-control" value="{{ old('email') }}"></div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
            <a href="{{ route('agencies.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection