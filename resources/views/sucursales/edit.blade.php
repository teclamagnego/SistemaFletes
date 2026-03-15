@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-pencil-square"></i> Editar Sucursal</h2>
    <a href="{{ route('sucursales.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('sucursales.update', $sucursal) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $sucursal->nombre) }}"
                        required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Empresa <span class="text-danger">*</span></label>
                    <select name="empresa_id" class="form-select" required>
                        <option value="">Seleccione...</option>
                        @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}" {{ old('empresa_id', $sucursal->empresa_id) == $empresa->id ?
                            'selected' : '' }}>
                            {{ $empresa->nombre_fantasia }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Domicilio <span class="text-danger">*</span></label>
                    <input type="text" name="domicilio" class="form-control"
                        value="{{ old('domicilio', $sucursal->domicilio) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Localidad <span class="text-danger">*</span></label>
                    <input type="text" name="localidad" class="form-control"
                        value="{{ old('localidad', $sucursal->localidad) }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                    <input type="text" name="telefono" class="form-control"
                        value="{{ old('telefono', $sucursal->telefono) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Punto de Venta <span class="text-danger">*</span></label>
                    <input type="text" name="puntoventa" class="form-control"
                        value="{{ old('puntoventa', $sucursal->puntoventa) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha Inicio Actividades <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_inicio_actividades" class="form-control"
                        value="{{ old('fecha_inicio_actividades', $sucursal->fecha_inicio_actividades?->format('Y-m-d')) }}"
                        required>
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Tope Líneas Factura</label>
                    <input type="number" name="tope_lineas_factura" class="form-control"
                        value="{{ old('tope_lineas_factura', $sucursal->tope_lineas_factura) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Decimales</label>
                    <input type="number" name="decimales" class="form-control"
                        value="{{ old('decimales', $sucursal->decimales) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Redondeo</label>
                    <input type="number" name="redondeo" class="form-control"
                        value="{{ old('redondeo', $sucursal->redondeo) }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Alias CBU</label>
                    <input type="text" name="alias_cbu" class="form-control"
                        value="{{ old('alias_cbu', $sucursal->alias_cbu) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Depósitos</label>
                    <input type="text" name="depositos" class="form-control"
                        value="{{ old('depositos', $sucursal->depositos) }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Listas de Precios</label>
                    <textarea name="listasprecios" class="form-control"
                        rows="2">{{ old('listasprecios', $sucursal->listasprecios) }}</textarea>
                </div>
            </div>

            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" name="edita_remitos" id="edita_remitos" value="1" {{
                    old('edita_remitos', $sucursal->edita_remitos) ? 'checked' : '' }}>
                <label class="form-check-label" for="edita_remitos">
                    Permitir editar remitos
                </label>
            </div>

            <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Actualizar Sucursal</button>
        </form>
    </div>
</div>
@endsection