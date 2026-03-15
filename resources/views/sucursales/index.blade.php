@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-shop"></i> Sucursales</h2>
    <a href="{{ route('sucursales.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nueva Sucursal</a>
</div>
<div class="card shadow-sm border-0">
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Empresa</th>
                    <th>Localidad</th>
                    <th>Teléfono</th>
                    <th>Punto Venta</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sucursales as $sucursal)
                <tr>
                    <td>{{ $sucursal->id }}</td>
                    <td class="fw-semibold">{{ $sucursal->nombre }}</td>
                    <td>{{ $sucursal->empresa?->nombre_fantasia ?? 'N/A' }}</td>
                    <td>{{ $sucursal->localidad }}</td>
                    <td>{{ $sucursal->telefono }}</td>
                    <td>{{ $sucursal->puntoventa }}</td>
                    <td>
                        <a href="{{ route('sucursales.edit', $sucursal) }}" class="btn btn-sm btn-outline-warning"
                            title="Editar"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('sucursales.destroy', $sucursal) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('¿Eliminar sucursal?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Eliminar"><i
                                    class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $sucursales->links() }}</div>
@endsection