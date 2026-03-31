@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-building-add"></i> Nueva Empresa</h2>
    <a href="{{ route('empresas.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('empresas.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre Fantasía <span class="text-danger">*</span></label>
                    <input type="text" name="nombre_fantasia" class="form-control" value="{{ old('nombre_fantasia') }}"
                        required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Razón Social <span class="text-danger">*</span></label>
                    <input type="text" name="razon_social" class="form-control" value="{{ old('razon_social') }}"
                        required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">CUIT <span class="text-danger">*</span></label>
                    <input type="text" name="cuit" class="form-control" value="{{ old('cuit') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tipo de IVA <span class="text-danger">*</span></label>
                    <select name="tipoiva_id" class="form-select" required>
                        <option value="">Seleccione...</option>
                        @foreach($tiposIva as $iva)
                        <option value="{{ $iva->id }}" {{ old('tipoiva_id')==$iva->id ? 'selected' : '' }}>
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
                    <div class="form-text">Formatos permitidos: JPG, PNG, GIF. Máx 2MB.</div>
                </div>
            </div>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" name="esagenteretencioniva" id="esagenteretencioniva"
                    value="1" {{ old('esagenteretencioniva') ? 'checked' : '' }}>
                <label class="form-check-label" for="esagenteretencioniva">
                    Es Agente Retención IVA
                </label>
            </div>

            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" name="activo" id="activo" value="1" checked>
                <label class="form-check-label" for="activo">
                    Empresa Activa
                </label>
            </div>

            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar Empresa</button>
        </form>
    </div>
</div>
@endsection