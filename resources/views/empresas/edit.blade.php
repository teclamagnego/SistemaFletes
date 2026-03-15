@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-pencil-square"></i> Editar Empresa</h2>
    <a href="{{ route('empresas.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('empresas.update', $empresa) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre Fantasía <span class="text-danger">*</span></label>
                    <input type="text" name="nombre_fantasia" class="form-control"
                        value="{{ old('nombre_fantasia', $empresa->nombre_fantasia) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Razón Social <span class="text-danger">*</span></label>
                    <input type="text" name="razon_social" class="form-control"
                        value="{{ old('razon_social', $empresa->razon_social) }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">CUIT <span class="text-danger">*</span></label>
                    <input type="text" name="cuit" class="form-control" value="{{ old('cuit', $empresa->cuit) }}"
                        required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tipo de IVA <span class="text-danger">*</span></label>
                    <select name="tipoiva_id" class="form-select" required>
                        <option value="">Seleccione...</option>
                        @foreach($tiposIva as $iva)
                        <option value="{{ $iva->id }}" {{ old('tipoiva_id', $empresa->tipoiva_id) == $iva->id ?
                            'selected' : '' }}>
                            {{ $iva->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Logo de la Empresa</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                    <div class="form-text">Dejar en blanco para conservar el actual. Máx 2MB.</div>
                </div>
                @if($empresa->logo)
                <div class="col-md-6">
                    <label class="form-label d-block">Logo Actual</label>
                    <img src="{{ asset('storage/' . $empresa->logo) }}" alt="Logo" class="img-thumbnail"
                        style="max-height: 100px;">
                </div>
                @endif
            </div>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" name="esagenteretencioniva" id="esagenteretencioniva"
                    value="1" {{ old('esagenteretencioniva', $empresa->esagenteretencioniva) ? 'checked' : '' }}>
                <label class="form-check-label" for="esagenteretencioniva">
                    Es Agente Retención IVA
                </label>
            </div>

            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" name="activo" id="activo" value="1" {{ old('activo',
                    $empresa->activo) ? 'checked' : '' }}>
                <label class="form-check-label" for="activo">
                    Empresa Activa
                </label>
            </div>

            <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Actualizar Empresa</button>
        </form>
    </div>
</div>
@endsection