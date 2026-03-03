@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-shield-lock"></i> Roles</h2>
    <a href="{{ route('roles.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nuevo Rol</a>
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Permisos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td><span class="badge bg-primary">{{ $role->name }}</span></td>
                    <td>@foreach($role->permissions as $perm)<span class="badge bg-secondary me-1">{{ $perm->name
                            }}</span>@endforeach</td>
                    <td>
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-warning"><i
                                class="bi bi-pencil"></i></a>
                        @if($role->name !== 'admin')
                        <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('¿Eliminar rol?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $roles->links() }}</div>
@endsection