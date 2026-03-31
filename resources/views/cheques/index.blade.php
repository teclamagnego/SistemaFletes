@extends('layouts.bootstrap')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold"><i class="bi bi-wallet2 text-primary"></i> Gestión de Cheques</h2>
</div>

{{-- Filtros --}}
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('cheques.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Cliente (Origen)</label>
                <input type="text" name="cliente" class="form-control" placeholder="Nombre del cliente..."
                    value="{{ request('cliente') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Nro de Cheque</label>
                <input type="text" name="numero" class="form-control" placeholder="Buscar por número..."
                    value="{{ request('numero') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Buscar</button>
                <a href="{{ route('cheques.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i> Limpiar</a>
            </div>
        </form>
    </div>
</div>

{{-- Tabla --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Número</th>
                        <th>Fecha</th>
                        <th>Cliente (Origen)</th>
                        <th class="text-end">Monto</th>
                        <th>Obs. Origen</th>
                        <th>Obs. Destino</th>
                        <th class="text-center pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cheques as $c)
                    <tr>
                        <td class="ps-3 fw-bold">{{ $c->numero }}</td>
                        <td>{{ \Carbon\Carbon::parse($c->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $c->cliente->nombre_fantasia ?? 'N/A' }}</td>
                        <td class="text-end fw-bold text-success">$ {{ number_format($c->monto, 2, ',', '.') }}</td>
                        <td class="small text-muted">{{ Str::limit($c->observacion_origen, 30) }}</td>
                        <td class="small text-muted">{{ Str::limit($c->observacion_destino, 30) }}</td>
                        <td class="text-center pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('cheques.edit', $c) }}" class="btn btn-outline-primary" title="Editar datos extras">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('cheques.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar cheque?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted"> No se encontraron cheques. </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $cheques->links() }}
</div>
@endsection
