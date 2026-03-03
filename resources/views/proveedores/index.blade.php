@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-building"></i> Proveedores</h2>
    @can('proveedores.create')<a href="{{ route('proveedores.create') }}" class="btn btn-primary"><i
            class="bi bi-plus-lg"></i> Nuevo Proveedor</a>@endcan
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Razón Social</th>
                    <th>CUIT</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Localidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proveedores as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->nombre }}</td>
                    <td>{{ $p->razon_social }}</td>
                    <td>{{ $p->cuit }}</td>
                    <td>{{ $p->telefono }}</td>
                    <td>{{ $p->email }}</td>
                    <td>{{ $p->localidad }}</td>
                    <td>
                        @can('proveedores.edit')<a href="{{ route('proveedores.edit', $p) }}"
                            class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>@endcan
                        @can('proveedores.delete')
                        <form action="{{ route('proveedores.destroy', $p) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('¿Eliminar?')">@csrf @method('DELETE')<button
                                class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button></form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $proveedores->links() }}</div>
@endsection