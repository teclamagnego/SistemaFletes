@extends('layouts.bootstrap')
@section('content')
<h2><i class="bi bi-plus-lg"></i> Nuevo Rol</h2>
<div class="card mt-3">
    <div class="card-body">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nombre del Rol</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Permisos</label>
                <div class="row">
                    @foreach($permissions->groupBy(function($p) { return explode('.', $p->name)[0]; }) as $group =>
                    $perms)
                    <div class="col-md-3 mb-2">
                        <strong class="text-capitalize">{{ $group }}</strong>
                        @foreach($perms as $perm)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                value="{{ $perm->name }}" id="perm_{{ $perm->id }}" {{ in_array($perm->name,
                            old('permissions', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="perm_{{ $perm->id }}">{{ explode('.', $perm->name)[1]
                                }}</label>
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
                @error('permissions')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection