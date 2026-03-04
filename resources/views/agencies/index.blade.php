@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-shop"></i> Agencias</h2>
    @can('agencies.create')<a href="{{ route('agencies.create') }}" class="btn btn-primary"><i
            class="bi bi-plus-lg"></i> Nueva Agencia</a>@endcan
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Localidad</th>
                    <th>Teléfono</th>
                    <th>Com. Origen</th>
                    <th>Com. Destino</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($agencies as $a)
                <tr>
                    <td><code>{{ $a->codigo }}</code></td>
                    <td>{{ $a->nombre }}</td>
                    <td>{{ $a->localidad }}</td>
                    <td>{{ $a->provincia }}</td>
                    <td>{{ $a->telefono }}</td>
                    <td>{{ $a->comision_porcentaje }}%</td>
                    <td>
                        @if($a->activa)
                        <span class="badge bg-success">Activa</span>
                        @else
                        <span class="badge bg-secondary">Inactiva</span>
                        @endif
                    </td>
                    <td>
                        @can('agencies.edit')<a href="{{ route('agencies.edit', $a) }}"
                            class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>@endcan
                        @can('agencies.delete')
                        <form action="{{ route('agencies.destroy', $a) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('¿Eliminar agencia?')">@csrf @method('DELETE')<button
                                class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button></form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $agencies->links() }}</div>
@endsection