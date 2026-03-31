@extends('layouts.bootstrap')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Recibos de Clientes</h5>
        @can('clientes.create')
        <a href="{{ route('cliente_recibos.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Nuevo Recibo
        </a>
        @endcan
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Nro. Recibo</th>
                        <th>Forma de Pago</th>
                        <th class="text-end">Monto</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recibos as $recibo)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($recibo->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $recibo->cliente->nombre_fantasia }}</td>
                        <td>{{ $recibo->nro_recibo ?? '-' }}</td>
                        <td>{{ $recibo->formaPago->nombre }}</td>
                        <td class="text-end fw-bold text-success">${{ number_format($recibo->monto, 2) }}</td>
                        <td class="text-end">
                            @can('clientes.edit')
                            <a href="{{ route('cliente_recibos.edit', $recibo) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('clientes.delete')
                            <form action="{{ route('cliente_recibos.destroy', $recibo) }}" method="POST" class="d-inline">
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
                        <td colspan="6" class="text-center py-4 text-muted">No hay recibos registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $recibos->links() }}
        </div>
    </div>
</div>
@endsection