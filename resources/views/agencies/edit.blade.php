@extends('layouts.bootstrap')
@section('content')
<h2><i class="bi bi-pencil-square"></i> Editar Agencia: {{ $agency->nombre }}</h2>
<div class="card mt-3">
        <div class="card-body">
                <form action="{{ route('agencies.update', $agency) }}" method="POST">@csrf @method('PUT')
                        <div class="row">
                                <div class="col-md-3 mb-3"><label class="form-label">Código *</label><input type="text"
                                                name="codigo" class="form-control @error('codigo') is-invalid @enderror"
                                                value="{{ old('codigo', $agency->codigo) }}" required
                                                maxlength="10">@error('codigo')<div class="invalid-feedback">{{ $message
                                                }}</div>@enderror</div>
                                <div class="col-md-5 mb-3"><label class="form-label">Nombre *</label><input type="text"
                                                name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                                value="{{ old('nombre', $agency->nombre) }}" required>@error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="col-md-2 mb-3"><label class="form-label">Com. Origen % *</label><input
                                                type="number" step="0.01" name="com_origen" class="form-control"
                                                value="{{ old('com_origen', $agency->com_origen) }}" required min="0"
                                                max="100"></div>
                                <div class="col-md-2 mb-3"><label class="form-label">Com. Destino % *</label><input
                                                type="number" step="0.01" name="com_destino" class="form-control"
                                                value="{{ old('com_destino', $agency->com_destino) }}" required min="0"
                                                max="100"></div>
                        </div>
                        <div class="row">
                                <div class="col-md-4 mb-3"><label class="form-label">Localidad *</label>
                                        <select name="localidad_id"
                                                class="form-select @error('localidad_id') is-invalid @enderror"
                                                required>
                                                <option value="">Seleccione Localidad...</option>
                                                @foreach($localidades as $l)
                                                <option value="{{ $l->id }}" {{ old('localidad_id', $agency->
                                                        localidad_id)==$l->id ? 'selected' : '' }}>{{ $l->nombre }}
                                                </option>
                                                @endforeach
                                        </select>
                                        @error('localidad_id')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div>
                                <div class="col-md-6 mb-3"><label class="form-label">Dirección</label><input type="text"
                                                name="direccion" class="form-control"
                                                value="{{ old('direccion', $agency->direccion) }}"></div>
                                <div class="col-md-2 mb-3"><label class="form-label">Estado</label><select name="activa"
                                                class="form-select">
                                                <option value="1" {{ old('activa', $agency->activa) == 1 ? 'selected' :
                                                        '' }}>Activa</option>
                                                <option value="0" {{ old('activa', $agency->activa) == 0 ? 'selected' :
                                                        '' }}>Inactiva</option>
                                        </select></div>
                        </div>
                        <div class="row">
                                <div class="col-md-4 mb-3"><label class="form-label">Teléfono</label><input type="text"
                                                name="telefono" class="form-control"
                                                value="{{ old('telefono', $agency->telefono) }}"></div>
                                <div class="col-md-4 mb-3"><label class="form-label">Email</label><input type="email"
                                                name="email" class="form-control"
                                                value="{{ old('email', $agency->email) }}"></div>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Actualizar</button>
                        <a href="{{ route('agencies.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
        </div>
</div>
@endsection