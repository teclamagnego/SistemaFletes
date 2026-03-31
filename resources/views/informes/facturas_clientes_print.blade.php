<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Informe de Facturas de Clientes</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
            color: #333;
        }

        .header {
            margin-bottom: 20px;
        }

        .logo-container {
            width: 50%;
            float: left;
        }

        .report-info {
            width: 45%;
            float: right;
            text-align: right;
        }

        .clear {
            clear: both;
        }

        .filters {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            padding: 8px 12px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .filters p {
            margin: 2px 0;
        }

        .summary {
            margin-bottom: 15px;
        }

        .summary-box {
            display: inline-block;
            width: 30%;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            border-radius: 4px;
            margin-right: 2%;
        }

        .summary-box:last-child {
            margin-right: 0;
        }

        .summary-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }

        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 5px 8px;
            text-align: left;
        }

        th {
            background-color: #2c3e50;
            color: #fff;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            background-color: #f1f1f1;
            font-weight: bold;
            font-size: 11px;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            color: #fff;
        }

        .badge-primary { background-color: #0d6efd; }
        .badge-info { background-color: #0dcaf0; color: #000; }
        .badge-warning { background-color: #ffc107; color: #000; }
        .badge-secondary { background-color: #6c757d; }
        .badge-success { background-color: #198754; }
        .badge-danger { background-color: #dc3545; }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .text-danger { color: #dc3545; }
        .text-success { color: #198754; }

        tr:nth-child(even) {
            background-color: #fafafa;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo-container">
            <h1 style="margin: 0; color: #2c3e50;">{{ $empresa->nombre_fantasia ?? 'DOBLE G' }}</h1>
            <p style="margin: 2px 0;">{{ $empresa->razon_social ?? '' }}</p>
            <p style="margin: 2px 0;">CUIT: {{ $empresa->cuit ?? '' }}</p>
        </div>
        <div class="report-info">
            <h2 style="margin: 0; color: #7f8c8d;">INFORME DE FACTURAS</h2>
            <p style="margin: 5px 0;"><strong>Fecha de Emisión:</strong> {{ date('d/m/Y H:i') }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <div class="filters">
        <p><strong>Filtros aplicados:</strong></p>
        <p>
            <strong>Período:</strong>
            {{ request('fecha_desde') ? date('d/m/Y', strtotime(request('fecha_desde'))) : 'Sin límite' }}
            al
            {{ request('fecha_hasta') ? date('d/m/Y', strtotime(request('fecha_hasta'))) : 'Sin límite' }}
            @if(count($codigosSeleccionados) > 0)
                &nbsp; | &nbsp; <strong>Tipos:</strong> {{ implode(', ', $codigosSeleccionados) }}
            @else
                &nbsp; | &nbsp; <strong>Tipos:</strong> Todos
            @endif
        </p>
    </div>

    <div class="summary">
        <div class="summary-box">
            <div class="summary-label">Total Facturas</div>
            <div class="summary-value">{{ $totalFacturas }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Importe Total</div>
            <div class="summary-value">$ {{ number_format($totalImporte, 2, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Pendiente de Cobro</div>
            <div class="summary-value text-danger">$ {{ number_format($totalPendiente, 2, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Fecha</th>
                <th style="width: 7%;">Tipo</th>
                <th style="width: 15%;">Nro. Factura</th>
                <th style="width: 25%;">Cliente</th>
                <th style="width: 13%;">Forma de Pago</th>
                <th style="width: 12%; text-align: right;">Total</th>
                <th style="width: 12%; text-align: right;">Pendiente</th>
                <th style="width: 8%; text-align: center;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($facturas as $factura)
                @php
                    $codigoModel = \App\Models\FacturaCodigo::find($factura->codigo);
                    $codigoNombre = $codigoModel->nombre ?? $factura->codigo;
                    $badgeClass = match($codigoNombre) {
                        'FA', 'FB' => 'badge-primary',
                        'DA', 'DB' => 'badge-info',
                        'CA', 'CB' => 'badge-warning',
                        'REM' => 'badge-secondary',
                        default => 'badge-secondary',
                    };
                    $estado = 'Pendiente';
                    $estadoBadge = 'badge-danger';
                    if ($factura->falta_imputar <= 0) {
                        $estado = 'Pagada';
                        $estadoBadge = 'badge-success';
                    } elseif ($factura->falta_imputar < $factura->total) {
                        $estado = 'Parcial';
                        $estadoBadge = 'badge-warning';
                    }
                @endphp
                <tr>
                    <td>{{ date('d/m/Y', strtotime($factura->fecha)) }}</td>
                    <td class="text-center"><span class="badge {{ $badgeClass }}">{{ $codigoNombre }}</span></td>
                    <td>{{ $factura->nro_factura }}</td>
                    <td>{{ $factura->cliente->nombre_fantasia ?? $factura->cliente->razon_social ?? '-' }}</td>
                    <td>{{ $factura->formaPago->nombre ?? '-' }}</td>
                    <td class="text-end">$ {{ number_format($factura->total, 2, ',', '.') }}</td>
                    <td class="text-end {{ $factura->falta_imputar > 0 ? 'text-danger' : 'text-success' }}">
                        $ {{ number_format($factura->falta_imputar, 2, ',', '.') }}
                    </td>
                    <td class="text-center"><span class="badge {{ $estadoBadge }}">{{ $estado }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No se encontraron facturas.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-end">TOTALES:</td>
                <td class="text-end">$ {{ number_format($totalImporte, 2, ',', '.') }}</td>
                <td class="text-end text-danger">$ {{ number_format($totalPendiente, 2, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Informe generado el {{ date('d/m/Y') }} a las {{ date('H:i') }} hs. — {{ $empresa->nombre_fantasia ?? 'DOBLE G' }}</p>
    </div>
</body>

</html>
