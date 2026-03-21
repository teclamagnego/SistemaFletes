@extends('layouts.bootstrap')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-shop"></i> Saldo de Comisiones (Agencias)</h1>
    <div class="card bg-warning text-dark p-2">
        <div class="small">Saldo Pendiente Total Agencias</div>
        <div class="h5 mb-0 fw-bold">$ {{ number_format($totalSaldos, 2) }}</div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Agencia</th>
                        <th>Ciudad</th>
                        <th class="text-end">Total Comisiones</th>
                        <th class="text-end">Total Cobrado (Ag)</th>
                        <th class="text-end pe-4 text-primary">Saldo Pendiente</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agencias as $agencia)
                        @php
                            $totalFacturado = $agencia->facturas_sum_total ?? 0;
                            $saldoPendiente = $agencia->facturas_sum_falta_imputar ?? 0;
                            $totalCobrado = $totalFacturado - $saldoPendiente;
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold">{{ $agencia->nombre }}</span>
                            </td>
                            <td class="text-muted small">{{ $agencia->ciudad }}</td>
                            <td class="text-end">$ {{ number_format($totalFacturado, 2) }}</td>
                            <td class="text-end text-success">$ {{ number_format($totalCobrado, 2) }}</td>
                            <td class="text-end pe-4 fw-bold text-danger">
                                $ {{ number_format($saldoPendiente, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-info-circle"></i> No hay agencias con saldos de comisiones pendientes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .table td { border-bottom: 1px solid #f0f0f0; }
</style>
@endpush
