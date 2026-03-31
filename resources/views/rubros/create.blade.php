@extends('layouts.bootstrap')
@section('content')
<h2><i class="bi bi-plus-lg"></i> Nuevo Rubro</h2>
<div class="card mt-3">
    <div class="card-body">
        <form action="{{ route('rubros.store') }}" method="POST">@csrf
            <div class="mb-3"><label class="form-label">Nombre *</label><input type="text" name="nombre"
                    class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}"
                    required>@error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="mb-3"><label class="form-label">Descripción</label><textarea name="descripcion"
                    class="form-control" rows="3">{{ old('descripcion') }}</textarea></div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
            <a href="{{ route('rubros.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection