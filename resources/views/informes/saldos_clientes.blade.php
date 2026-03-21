@extends('layouts.bootstrap')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-person-lines-fill"></i> Saldo de Clientes</h1>
    <div class="card bg-primary text-white p-2">
        <div class="small">Saldo Pendiente Total</div>
        <div class="h5 mb-0 fw-bold">$ {{ number_format($totalSaldos, 2) }}</div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Cliente</th>
                        <th>Razón Social</th>
                        <th class="text-end">Total Facturado</th>
                        <th class="text-end">Total Pagado</th>
                        <th class="text-end pe-4 text-primary">Saldo Pendiente</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cliente)
                        @php
                            $totalFacturado = $cliente->facturas_sum_total ?? 0;
                            $saldoPendiente = $cliente->facturas_sum_falta_imputar ?? 0;
                            $totalPagado = $totalFacturado - $saldoPendiente;
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('clientes.history', $cliente->id) }}" class="fw-bold text-decoration-none text-primary">
                                    {{ $cliente->nombre_fantasia }}
                                </a>
                            </td>
                            <td class="text-muted small">{{ $cliente->razon_social }}</td>
                            <td class="text-end">$ {{ number_format($totalFacturado, 2) }}</td>
                            <td class="text-end text-success">$ {{ number_format($totalPagado, 2) }}</td>
                            <td class="text-end pe-4 fw-bold text-danger">
                                $ {{ number_format($saldoPendiente, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-info-circle"></i> No hay clientes con saldos pendientes en este momento.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($clientes->isEmpty())
    <div class="alert alert-info mt-3 text-center">
        <i class="bi bi-info-circle"></i> No hay clientes con saldos pendientes en este momento.
    </div>
@endif

@endsection

@push('styles')
<style>
    .table td { border-bottom: 1px solid #f0f0f0; }
</style>
@endpush
