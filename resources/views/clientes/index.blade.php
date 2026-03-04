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
                    <th>Nombre Fantasía</th>
                    <th>Documento</th>
                    <th>Localidad</th>
                    <th>IVA / Cuenta</th>
                    <th>Origen / Destino</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clientes as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>
                        <strong>{{ $c->nombre_fantasia }}</strong><br>
                        <small class="text-muted">{{ $c->razon_social }}</small>
                    </td>
                    <td>
                        <small class="text-muted">{{ $c->tipoDoc->codigo ?? 'N/A' }}:</small> {{ $c->documento_nro }}
                    </td>
                    <td>{{ $c->localidad->nombre ?? 'N/A' }}</td>
                    <td>
                        <span class="badge bg-info text-dark">{{ $c->tipoIva->nombre ?? 'N/A' }}</span><br>
                        <small>{{ $c->tipoCuenta->nombre ?? 'N/A' }}</small>
                    </td>
                    <td>
                        <small>De:</small> {{ $c->agenciaOrigen->nombre ?? 'N/A' }}<br>
                        <small>A:</small> {{ $c->agenciaDestino->nombre ?? 'N/A' }}
                    </td>
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