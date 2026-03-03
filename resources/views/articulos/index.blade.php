@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-box-seam"></i> Artículos</h2>
    @can('articulos.create')<a href="{{ route('articulos.create') }}" class="btn btn-primary"><i
            class="bi bi-plus-lg"></i> Nuevo Artículo</a>@endcan
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Rubros</th>
                    <th>Proveedores</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($articulos as $a)
                <tr>
                    <td><code>{{ $a->codigo }}</code></td>
                    <td>{{ $a->nombre }}</td>
                    <td>${{ number_format($a->precio, 2) }}</td>
                    <td>{{ $a->stock }}</td>
                    <td>@foreach($a->rubros as $r)<span class="badge bg-info text-dark">{{ $r->nombre }}</span>
                        @endforeach</td>
                    <td>@foreach($a->proveedores as $p)<span class="badge bg-success">{{ $p->nombre }}</span>
                        @endforeach</td>
                    <td>
                        @can('articulos.edit')<a href="{{ route('articulos.edit', $a) }}"
                            class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>@endcan
                        @can('articulos.delete')
                        <form action="{{ route('articulos.destroy', $a) }}" method="POST" class="d-inline"
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
<div class="mt-3">{{ $articulos->links() }}</div>
@endsection