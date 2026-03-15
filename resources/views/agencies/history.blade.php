@extends('layouts.bootstrap')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="mb-0 fw-bold">Cuenta Corriente: {{ $agency->nombre }}</h2>
            <p class="text-muted mb-0">Gestión de comisiones y pagos</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <div class="btn-group">
                <a href="{{ route('agencies.billing', $agency) }}" class="btn btn-primary">
                    <i class="bi bi-receipt-cutoff me-1"></i> Liquidar Comisiones
                </a>
                <a href="{{ route('agencia_recibos.create', ['agency_id' => $agency->id]) }}" class="btn btn-success">
                    <i class="bi bi-cash-coin me-1"></i> Registrar Pago
                </a>
            </div>
            <a href="{{ route('agencies.index') }}" class="btn btn-outline-secondary ms-2">Volver</a>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('agencies.history', $agency) }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Desde</label>
                    <input type="date" name="from" class="form-control" value="{{ $from }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Hasta</label>
                    <input type="date" name="to" class="form-control" value="{{ $to }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-filter"></i> Filtrar
                    </button>
                    <button type="button" onclick="window.print()" class="btn btn-outline-secondary">
                        <i class="bi bi-printer"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Referencia</th>
                            <th>Detalle</th>
                            <th class="text-end">Debe (Comisión)</th>
                            <th class="text-end">Haber (Pago)</th>
                            <th class="text-end">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-info">
                            <td colspan="4" class="text-end fw-bold italic">Saldo Anterior al {{
                                \Carbon\Carbon::parse($from)->format('d/m/Y') }}:</td>
                            <td class="text-end"></td>
                            <td class="text-end"></td>
                            <td class="text-end fw-bold">$ {{ number_format($saldoAnterior, 2) }}</td>
                        </tr>

                        @php $saldo = $saldoAnterior; @endphp
                        @foreach($movimientos as $mov)
                        @php $saldo += ($mov['debe'] - $mov['haber']); @endphp
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($mov['fecha'])->format('d/m/Y') }}</td>
                            <td>
                                <span
                                    class="badge {{ $mov['debe'] > 0 ? 'bg-danger-subtle text-danger border border-danger' : 'bg-success-subtle text-success border border-success' }}">
                                    {{ $mov['tipo'] }}
                                </span>
                            </td>
                            <td class="fw-bold">{{ $mov['referencia'] }}</td>
                            <td><small class="text-muted">{{ $mov['detalle'] ?? '-' }}</small></td>
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
                        @endforeach

                        @if($movimientos->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No hay movimientos en este periodo.</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr class="table-dark">
                            <td colspan="6" class="text-end fw-bold">SALDO ACTUAL:</td>
                            <td class="text-end fw-bold">$ {{ number_format($saldo, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection