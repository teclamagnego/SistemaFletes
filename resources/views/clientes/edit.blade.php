@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="bi bi-pencil-square"></i> Editar Cliente: {{ $cliente->nombre_fantasia }}</h2>
        <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i>
                Volver</a>
</div>

<div class="card shadow-sm">
        <div class="card-body">
                <form action="{{ route('clientes.update', $cliente) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <h5 class="card-title mb-4 border-bottom pb-2 text-primary">Datos Principales</h5>
                        <div class="row">
                                <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre Fantasía *</label>
                                        <input type="text" name="nombre_fantasia"
                                                class="form-control @error('nombre_fantasia') is-invalid @enderror"
                                                value="{{ old('nombre_fantasia', $cliente->nombre_fantasia) }}"
                                                required>
                                        @error('nombre_fantasia')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                        <label class="form-label">Razón Social</label>
                                        <input type="text" name="razon_social"
                                                class="form-control @error('razon_social') is-invalid @enderror"
                                                value="{{ old('razon_social', $cliente->razon_social) }}">
                                        @error('razon_social')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div>
                        </div>

                        <div class="row">
                                <div class="col-md-4 mb-3">
                                        <label class="form-label">Tipo Documento *</label>
                                        <select name="tipodoc_id"
                                                class="form-select @error('tipodoc_id') is-invalid @enderror" required>
                                                @foreach($tiposDoc as $td)
                                                <option value="{{ $td->id }}" {{ old('tipodoc_id', $cliente->tipodoc_id)
                                                        == $td->id ? 'selected' : '' }}>{{ $td->nombre }} ({{
                                                        $td->codigo }})</option>
                                                @endforeach
                                        </select>
                                        @error('tipodoc_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                        <label class="form-label">Documento Nro *</label>
                                        <input type="text" name="documento_nro"
                                                class="form-control @error('documento_nro') is-invalid @enderror"
                                                value="{{ old('documento_nro', $cliente->documento_nro) }}" required>
                                        @error('documento_nro')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                        <label class="form-label">Tipo IVA *</label>
                                        <select name="tipoiva_id"
                                                class="form-select @error('tipoiva_id') is-invalid @enderror" required>
                                                @foreach($tiposIva as $iva)
                                                <option value="{{ $iva->id }}" {{ old('tipoiva_id', $cliente->
                                                        tipoiva_id) == $iva->id ? 'selected' : '' }}>{{ $iva->nombre }}
                                                </option>
                                                @endforeach
                                        </select>
                                        @error('tipoiva_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                        </div>

                        <h5 class="card-title mt-4 mb-4 border-bottom pb-2 text-primary">Contacto y Ubicación</h5>
                        <div class="row">
                                <div class="col-md-4 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email', $cliente->email) }}">
                                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" name="telefono"
                                                class="form-control @error('telefono') is-invalid @enderror"
                                                value="{{ old('telefono', $cliente->telefono) }}">
                                        @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                        <label class="form-label">Localidad *</label>
                                        <select name="localidad_id"
                                                class="form-select @error('localidad_id') is-invalid @enderror"
                                                required>
                                                <option value="">Seleccione...</option>
                                                @foreach($localidades as $loc)
                                                <option value="{{ $loc->id }}" {{ old('localidad_id', $cliente->
                                                        localidad_id) == $loc->id ? 'selected' : '' }}>{{ $loc->nombre
                                                        }}</option>
                                                @endforeach
                                        </select>
                                        @error('localidad_id')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div>
                        </div>
                        <div class="mb-3">
                                <label class="form-label">Dirección</label>
                                <input type="text" name="direccion"
                                        class="form-control @error('direccion') is-invalid @enderror"
                                        value="{{ old('direccion', $cliente->direccion) }}">
                                @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <h5 class="card-title mt-4 mb-4 border-bottom pb-2 text-primary">Configuración de Cuenta</h5>
                        <div class="row">
                                <div class="col-md-4 mb-3">
                                        <label class="form-label">Tipo de Cuenta *</label>
                                        <select name="tipocuenta_id"
                                                class="form-select @error('tipocuenta_id') is-invalid @enderror"
                                                required>
                                                @foreach($tiposCuenta as $tc)
                                                <option value="{{ $tc->id }}" {{ old('tipocuenta_id', $cliente->
                                                        tipocuenta_id) == $tc->id ? 'selected' : '' }}>{{ $tc->nombre }}
                                                </option>
                                                @endforeach
                                        </select>
                                        @error('tipocuenta_id')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                        <label class="form-label">Agencia Origen Habitual *</label>
                                        <select name="agenciaorigen_id"
                                                class="form-select @error('agenciaorigen_id') is-invalid @enderror"
                                                required>
                                                @foreach($agencies as $ag)
                                                <option value="{{ $ag->id }}" {{ old('agenciaorigen_id', $cliente->
                                                        agenciaorigen_id) == $ag->id ? 'selected' : '' }}>{{ $ag->nombre
                                                        }}</option>
                                                @endforeach
                                        </select>
                                        @error('agenciaorigen_id')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                        <label class="form-label">Agencia Destino Habitual *</label>
                                        <select name="agenciadestino_id"
                                                class="form-select @error('agenciadestino_id') is-invalid @enderror"
                                                required>
                                                @foreach($agencies as $ag)
                                                <option value="{{ $ag->id }}" {{ old('agenciadestino_id', $cliente->
                                                        agenciadestino_id) == $ag->id ? 'selected' : '' }}>{{
                                                        $ag->nombre }}</option>
                                                @endforeach
                                        </select>
                                        @error('agenciadestino_id')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div>
                        </div>

                        <div class="mb-4">
                                <label class="form-label">Observaciones</label>
                                <textarea name="observacion"
                                        class="form-control @error('observacion') is-invalid @enderror"
                                        rows="3">{{ old('observacion', $cliente->observacion) }}</textarea>
                                @error('observacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <hr>
                        <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Actualizar
                                        Cliente</button>
                        </div>
                </form>
        </div>
</div>
@endsection