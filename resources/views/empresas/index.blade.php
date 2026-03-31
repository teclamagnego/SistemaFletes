@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-buildings"></i> Empresas</h2>
    <a href="{{ route('empresas.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nueva Empresa</a>
</div>
<div class="card shadow-sm border-0">
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Logo</th>
                    <th>Nombre Fantasía</th>
                    <th>Razón Social</th>
                    <th>CUIT</th>
                    <th>Activo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($empresas as $empresa)
                <tr>
                    <td>{{ $empresa->id }}</td>
                    <td>
                        @if($empresa->logo)
                        <img src="{{ asset('storage/' . $empresa->logo) }}" alt="Logo"
                            style="height: 30px; width: auto;" class="rounded">
                        @else
                        <i class="bi bi-image text-muted"></i>
                        @endif
                    </td>
                    <td class="fw-semibold">{{ $empresa->nombre_fantasia }}</td>
                    <td>{{ $empresa->razon_social }}</td>
                    <td>{{ $empresa->cuit }}</td>
                    <td>
                        @if($empresa->activo)
                        <span class="badge bg-success">Sí</span>
                        @else
                        <span class="badge bg-danger">No</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('empresas.edit', $empresa) }}" class="btn btn-sm btn-outline-warning"
                            title="Editar"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('empresas.destroy', $empresa) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('¿Eliminar empresa?')">
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
<div class="mt-3">{{ $empresas->links() }}</div>
@endsection