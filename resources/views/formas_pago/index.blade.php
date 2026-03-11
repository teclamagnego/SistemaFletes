@extends('layouts.bootstrap')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Formas de Pago</h5>
        @can('formas_pago.create')
        <a href="{{ route('formas_pago.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Nueva Forma de Pago
        </a>
        @endcan
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($formasPago as $formaPago)
                    <tr>
                        <td>{{ $formaPago->id }}</td>
                        <td>{{ $formaPago->nombre }}</td>
                        <td class="text-end">
                            @can('formas_pago.edit')
                            <a href="{{ route('formas_pago.edit', $formaPago) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('formas_pago.delete')
                            <form action="{{ route('formas_pago.destroy', $formaPago) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Está seguro?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 text-muted">No hay formas de pago registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $formasPago->links() }}
        </div>
    </div>
</div>
@endsection