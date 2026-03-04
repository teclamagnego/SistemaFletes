@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-truck"></i> Transportistas</h2>
    @can('carriers.create')<a href="{{ route('carriers.create') }}" class="btn btn-primary"><i
            class="bi bi-plus-lg"></i> Nuevo Transportista</a>@endcan
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Documento</th>
                    <th>Teléfono</th>
                    <th>Vehículo</th>
                    <th>Patente</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($carriers as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>{{ $c->full_name }}</td>
                    <td>{{ $c->documento }}</td>
                    <td>{{ $c->telefono }}</td>
                    <td>{{ $c->vehiculo }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $c->patente }}</span></td>
                    <td>
                        @if($c->activo)
                        <span class="badge bg-success">Activo</span>
                        @else
                        <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        @can('carriers.edit')<a href="{{ route('carriers.edit', $c) }}"
                            class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>@endcan
                        @can('carriers.delete')
                        <form action="{{ route('carriers.destroy', $c) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('¿Eliminar transportista?')">@csrf @method('DELETE')<button
                                class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button></form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $carriers->links() }}</div>
@endsection