@extends('layouts.bootstrap')

@section('content')
<div class="card shadow-sm border-0 mb-3">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold">Estado de Cuenta: {{ $cliente->nombre_fantasia }}</h5>
            <small class="text-muted">Historial de Movimientos</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('clientes.billing', $cliente) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-receipt"></i> Facturación (Guías)
            </a>
            <a href="{{ route('cliente_recibos.create', ['cliente_id' => $cliente->id]) }}"
                class="btn btn-success btn-sm">
                <i class="bi bi-cash"></i> Registrar Pago
            </a>
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary btn-sm">
                Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('clientes.history', $cliente) }}" method="GET" class="row g-2 mb-4">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Desde</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Hasta</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-filter"></i> Filtrar
                </button>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <a href="{{ route('clientes.history.print', [$cliente, 'from' => $from, 'to' => $to]) }}"
                    target="_blank" class="btn btn-outline-danger btn-sm w-100">
                    <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
                </a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Referencia</th>
                        <th>Detalle</th>
                        <th class="text-end">Debe</th>
                        <th class="text-end">Haber</th>
                        <th class="text-end">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @php $saldo = $saldoAnterior; @endphp
                    <tr class="table-info">
                        <td>{{ \Carbon\Carbon::parse($from)->format('d/m/Y') }}</td>
                        <td colspan="3"><strong>SALDO ANTERIOR</strong></td>
                        <td class="text-end">-</td>
                        <td class="text-end">-</td>
                        <td class="text-end fw-bold {{ $saldo > 0 ? 'text-danger' : 'text-success' }}">
                            $ {{ number_format($saldo, 2) }}
                        </td>
                    </tr>

                    @forelse($movimientos as $mov)
                    @php $saldo += ($mov['debe'] - $mov['haber']); @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($mov['fecha'])->format('d/m/Y') }}</td>
                        <td>
                            @php
                            $badge = 'bg-secondary';
                            if(str_contains($mov['tipo'], 'Factura')) $badge = 'bg-danger';
                            if(str_contains($mov['tipo'], 'Recibo')) $badge = 'bg-success';
                            if(str_contains($mov['tipo'], 'Guia')) $badge = 'bg-info';
                            @endphp
                            <span class="badge {{ $badge }}">{{ $mov['tipo'] }}</span>
                        </td>
                        <td>
                            @if(isset($mov['link']) && $mov['link'])
                            <a href="{{ $mov['link'] }}" class="text-decoration-none fw-bold">{{ $mov['referencia']
                                }}</a>
                            @else
                            {{ $mov['referencia'] }}
                            @endif

                            @if($mov['tipo'] == 'Factura' && isset($mov['factura_id']))
                            <div class="btn-group ms-1">
                                <a href="{{ route('facturas.print', $mov['factura_id']) }}" target="_blank"
                                    class="btn btn-outline-danger btn-sm px-2" title="Imprimir Factura">
                                    <i class="bi bi-printer"></i>
                                </a>
                                <form action="{{ route('facturas.destroy', $mov['factura_id']) }}" method="POST"
                                    onsubmit="return confirm('¿Está seguro de eliminar esta factura? Las guías asociadas quedarán como pendientes de facturación.')"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-secondary btn-sm px-2"
                                        title="Eliminar Factura">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                            @endif

                            @if($mov['tipo'] == 'Recibo' && isset($mov['recibo_id']))
                            <a href="{{ route('cliente_recibos.print', $mov['recibo_id']) }}" target="_blank"
                                class="btn btn-link btn-sm p-0 ms-1" title="Imprimir Recibo">
                                <i class="bi bi-printer text-success"></i>
                            </a>
                            @endif
                        </td>
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
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No hay movimientos registrados para este
                            periodo.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="6" class="text-end fw-bold">SALDO ACTUAL:</td>
                        <td class="text-end fw-bold {{ $saldo > 0 ? 'text-danger' : 'text-success' }}">
                            $ {{ number_format($saldo, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection