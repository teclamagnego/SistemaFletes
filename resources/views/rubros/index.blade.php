@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-tags"></i> Rubros</h2>
    @can('rubros.create')<a href="{{ route('rubros.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i>
        Nuevo Rubro</a>@endcan
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rubros as $r)
                <tr>
                    <td>{{ $r->id }}</td>
                    <td>{{ $r->nombre }}</td>
                    <td>{{ Str::limit($r->descripcion, 60) }}</td>
                    <td>
                        @can('rubros.edit')<a href="{{ route('rubros.edit', $r) }}" class="btn btn-sm btn-warning"><i
                                class="bi bi-pencil"></i></a>@endcan
                        @can('rubros.delete')
                        <form action="{{ route('rubros.destroy', $r) }}" method="POST" class="d-inline"
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
<div class="mt-3">{{ $rubros->links() }}</div>
@endsection