@extends('layouts.bootstrap')
@section('content')
<h2><i class="bi bi-plus-lg"></i> Nuevo Artículo</h2>
<div class="card mt-3">
    <div class="card-body">
        <form action="{{ route('articulos.store') }}" method="POST">@csrf
            <div class="row">
                <div class="col-md-3 mb-3"><label class="form-label">Código *</label><input type="text" name="codigo"
                        class="form-control @error('codigo') is-invalid @enderror" value="{{ old('codigo') }}"
                        required>@error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-5 mb-3"><label class="form-label">Nombre *</label><input type="text" name="nombre"
                        class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}"
                        required>@error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-2 mb-3"><label class="form-label">Precio *</label><input type="number" step="0.01"
                        name="precio" class="form-control" value="{{ old('precio', 0) }}" required></div>
                <div class="col-md-2 mb-3"><label class="form-label">Stock *</label><input type="number" name="stock"
                        class="form-control" value="{{ old('stock', 0) }}" required></div>
            </div>
            <div class="mb-3"><label class="form-label">Descripción</label><textarea name="descripcion"
                    class="form-control" rows="2">{{ old('descripcion') }}</textarea></div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Rubros</label>
                    <select name="rubros[]" class="form-select" multiple size="5">
                        @foreach($rubros as $r)
                        <option value="{{ $r->id }}" {{ in_array($r->id, old('rubros', [])) ? 'selected' : '' }}>{{
                            $r->nombre }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Ctrl+click para selección múltiple</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Proveedores</label>
                    <select name="proveedores[]" class="form-select" multiple size="5">
                        @foreach($proveedores as $p)
                        <option value="{{ $p->id }}" {{ in_array($p->id, old('proveedores', [])) ? 'selected' : '' }}>{{
                            $p->nombre }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Ctrl+click para selección múltiple</small>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
            <a href="{{ route('articulos.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection