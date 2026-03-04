@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-people"></i> Clientes</h2>
    @can('clientes.create')<a href="{{ route('clientes.create') }}" class="btn btn-primary"><i
            class="bi bi-plus-lg"></i> Nuevo Cliente</a>@endcan
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Documento</th>
                    <th>CUIT</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clientes as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>{{ $c->nombre }}</td>
                    <td>{{ $c->apellido }}</td>
                    <td><small class="text-muted">{{ $c->tipo_documento }}:</small> {{ $c->numero_documento }}</td>
                    <td>{{ $c->cuit }}</td>
                    <td>{{ $c->telefono }}</td>
                    <td>{{ $c->email }}</td>
                    <td>
                        @can('clientes.edit')<a href="{{ route('clientes.edit', $c) }}"
                            class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>@endcan
                        @can('clientes.delete')
                        <form action="{{ route('clientes.destroy', $c) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('¿Eliminar?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $clientes->links() }}</div>
@endsection