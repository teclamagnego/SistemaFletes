@extends('layouts.bootstrap')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold">Estado de Cuenta: {{ $cliente->nombre_fantasia }}</h5>
            <small class="text-muted">Historial de Guías y Pagos</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('cliente_recibos.create', ['cliente_id' => $cliente->id]) }}" class="btn btn-success btn-sm">
                <i class="bi bi-cash"></i> Registrar Pago
            </a>
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary btn-sm">
                Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Referencia</th>
                        <th>Detalle / F. Pago</th>
                        <th class="text-end">Debe (Guías)</th>
                        <th class="text-end">Haber (Pagos)</th>
                        <th class="text-end">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @php $saldo = 0; @endphp
                    @forelse($movimientos as $mov)
                        @php $saldo += ($mov['debe'] - $mov['haber']); @endphp
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($mov['fecha'])->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge {{ $mov['tipo'] == 'Guía' ? 'bg-info' : 'bg-success' }}">
                                    {{ $mov['tipo'] }}
                                </span>
                            </td>
                            <td>
                                @if($mov['link'])
                                    <a href="{{ $mov['link'] }}" class="text-decoration-none fw-bold">{{ $mov['referencia'] }}</a>
                                @else
                                    {{ $mov['referencia'] }}
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $mov['detalle'] ?? '-' }}</small>
                            </td>
                            <td class="text-end text-danger">
                                {{ $mov['debe'] > 0 ? '$ ' . number_format($mov['debe'], 2) : '-' }}
                            </td>
                            <td class="text-end text-success">
                                {{ $mov['haber'] > 0 ? '$ ' . number_format($mov['haber'], 2) : '-' }}
                            </td>
                            <td class="text-end fw-bold {{ $saldo > 0 ? 'text-danger' : 'text-success' }}">
                                $ {{ number_format($saldo, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No hay movimientos registrados para este cliente.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($movimientos->isNotEmpty())
                <tfoot class="table-light">
                    <tr>
                        <td colspan="6" class="text-end fw-bold">SALDO ACTUAL:</td>
                        <td class="text-end fw-bold {{ $saldo > 0 ? 'text-danger' : 'text-success' }}">
                            $ {{ number_format($saldo, 2) }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection