@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-people"></i> Clientes</h2>
    @can('clientes.create')<a href="{{ route('clientes.create') }}" class="btn btn-primary"><i
            class="bi bi-plus-lg"></i> Nuevo Cliente</a>@endcan
</div>
<div class="card mb-3 shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('clientes.index') }}" method="GET" class="row g-2">
            <div class="col-md-5">
                <input type="text" name="nombre" class="form-control" placeholder="Nombre Fantasía..."
                    value="{{ request('nombre') }}">
            </div>
            <div class="col-md-5">
                <select name="localidad_id" class="form-select">
                    <option value="">Todas las localidades</option>
                    @foreach($localidades as $loc)
                    <option value="{{ $loc->id }}" {{ request('localidad_id')==$loc->id ? 'selected' : '' }}>{{
                        $loc->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>
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
                    <a href="{{ route('clientes.history', $c) }}" class="btn btn-sm btn-info" title="Estado de Cuenta">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                    </a>
                    @can('clientes.edit')<a href="{{ route('clientes.edit', $c) }}" class="btn btn-sm btn-warning"><i
                            class="bi bi-pencil"></i></a>@endcan
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